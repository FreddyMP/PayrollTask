<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\UserRequest;
use App\Models\Holiday;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = Payroll::where('company_id', $companyId)->with('employee.user');

        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->latest()->paginate(15);

        return view('payroll.index', compact('payrolls'));
    }

    public function create()
    {
        $user      = Auth::user();
        $company   = $user->company;
        $employees = Employee::where('company_id', $user->company_id)
            ->with('user')->get();

        $isBiweekly = $company->payroll_frequency === 'biweekly';
        $currentPeriod = $isBiweekly
            ? date('Y-m') . (date('j') <= 15 ? '-Q1' : '-Q2')
            : date('Y-m');

        // Generar lista de períodos (últimos 12 meses y próximo)
        $periods = [];
        for ($i = -12; $i <= 1; $i++) {
            $date = Carbon::now()->addMonths($i);
            if ($isBiweekly) {
                // Primera quincena
                $periods[] = [
                    'value' => $date->format('Y-m') . '-Q1',
                    'label' => ucfirst($date->translatedFormat('F Y')) . ' — 1ª Quincena (1-15)',
                ];
                // Segunda quincena
                $periods[] = [
                    'value' => $date->format('Y-m') . '-Q2',
                    'label' => ucfirst($date->translatedFormat('F Y')) . ' — 2ª Quincena (16-fin)',
                ];
            } else {
                $periods[] = [
                    'value' => $date->format('Y-m'),
                    'label' => ucfirst($date->translatedFormat('F Y')),
                ];
            }
        }
        $periods = array_reverse($periods);

        return view('payroll.create', compact('employees', 'currentPeriod', 'periods', 'isBiweekly'));
    }

    public function bonuses()
    {
        $employees = Employee::where('company_id', Auth::user()->company_id)
            ->with('user')
            ->get();

        return view('payroll.bonuses', compact('employees'));
    }

    public function benefits()
    {
        $employees = Employee::where('company_id', Auth::user()->company_id)
            ->with('user')
            ->get();

        return view('payroll.benefits', compact('employees'));
    }

    public function christmas()
    {
        $employees = Employee::where('company_id', Auth::user()->company_id)
            ->with('user')
            ->get();

        $now = \Carbon\Carbon::now();

        $employees->each(function ($employee) use ($now) {
            if (!$employee->hire_date) {
                $employee->christmas_salary = 0;
                $employee->months_worked = 0;
                return;
            }

            $hireDate = \Carbon\Carbon::parse($employee->hire_date);
            $months = $hireDate->diffInMonths($now);
            
            if ($months >= 12) {
                $employee->christmas_salary = $employee->salary;
                $employee->months_worked = '12+';
            } else {
                $floatMonths = $hireDate->floatDiffInMonths($now);
                $monthsWorked = max(1, (int) ceil($floatMonths));
                $employee->christmas_salary = ($employee->salary * $monthsWorked) / 12;
                $employee->months_worked = $monthsWorked;
            }
        });

        return view('payroll.christmas', compact('employees'));
    }

    public function ir17(Request $request)
    {
        $company = Auth::user()->company;
        $period = $request->get('period', date('Y-m'));

        $payrollData = Payroll::where('company_id', $company->id)
            ->where('period', $period)
            ->with('employee.user', 'employee.arsExtras')
            ->get();

        $report = $payrollData->map(function ($p) {
            $salary = (float) $p->gross_salary;
            $extras = (float) ($p->extras ?? 0);
            $remuneracionBruta = $salary + $extras;

            // Aportes TSS del empleado
            $sfs = $salary * 0.0304;
            $afp = $salary * 0.0287;
            $arsExtra = $p->employee->total_ars_extra ?? 0;
            $totalTSS = $sfs + $afp + $arsExtra;

            // Renta neta imponible (anualizada)
            $multiplier = ($company->payroll_frequency === 'biweekly') ? 24 : 12;
            $baseAnual = ($remuneracionBruta * $multiplier) - ($totalTSS * $multiplier);

            // Escala ISR vigente
            $isrAnual = 0;
            $tramo = 'Exento';
            if ($baseAnual <= 416220) {
                $isrAnual = 0;
                $tramo = 'Exento';
            } elseif ($baseAnual < 624329) {
                $isrAnual = ($baseAnual - 416220) * 0.15;
                $tramo = '15%';
            } elseif ($baseAnual < 867123) {
                $isrAnual = ($baseAnual - 624329) * 0.20 + 31216.35;
                $tramo = '20%';
            } else {
                $isrAnual = ($baseAnual - 867123) * 0.25 + (31216.35 + 48558.80);
                $tramo = '25%';
            }

            $isrMensual = $isrAnual / $multiplier;

            return [
                'cedula'             => $p->employee->id_number ?? '—',
                'nombre'             => $p->employee->user->name,
                'remuneracion_bruta' => $remuneracionBruta,
                'otros_ingresos'     => $extras,
                'sfs'                => $sfs + $arsExtra,
                'afp'                => $afp,
                'total_tss'          => $totalTSS,
                'ingreso_gravable'   => $remuneracionBruta - $totalTSS,
                'base_anual'         => $baseAnual,
                'tramo'              => $tramo,
                'isr_retenido'       => $isrMensual,
                'neto'               => $remuneracionBruta - $totalTSS - $isrMensual,
            ];
        });

        $availablePeriods = Payroll::where('company_id', $company->id)
            ->distinct()
            ->pluck('period')
            ->sortDesc();

        if (!$availablePeriods->contains($period)) {
            $availablePeriods->prepend($period);
        }

        return view('payroll.ir17', compact('report', 'period', 'availablePeriods', 'company'));
    }

    /**
     * API: Devuelve horas extra aprobadas y monto segun CT dominicano
     * para un empleado en un periodo (Y-m o Y-m-Q1/Q2) dado.
     */
    public function apiOvertimeData(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $period     = $request->input('period', date('Y-m'));
        $user       = Auth::user();

        $employee = Employee::where('company_id', $user->company_id)
            ->where('id', $employeeId)
            ->with('user')
            ->firstOrFail();

        $company = $user->company;

        // Detectar si es período quincenal (Y-m-Q1 o Y-m-Q2)
        $quincena = null;
        if (preg_match('/^(\d{4}-\d{2})-(Q[12])$/', $period, $m)) {
            $basePeriod = $m[1];
            $quincena   = $m[2]; // 'Q1' o 'Q2'
        } else {
            $basePeriod = $period;
        }

        [$year, $month] = explode('-', $basePeriod);

        $query = UserRequest::where('company_id', $user->company_id)
            ->where('user_id', $employee->user_id)
            ->where('type', 'overtime')
            ->where('status', 'approved')
            ->whereYear('overtime_date', $year)
            ->whereMonth('overtime_date', $month);

        // Filtrar por quincena si aplica
        if ($quincena === 'Q1') {
            $query->whereDay('overtime_date', '<=', 15);
        } elseif ($quincena === 'Q2') {
            $query->whereDay('overtime_date', '>', 15);
        }

        $requests = $query->get();

        // Cargar festivos del mes (filtrando por quincena si aplica)
        $holidayQuery = Holiday::where('company_id', $user->company_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month);
        if ($quincena === 'Q1') {
            $holidayQuery->whereDay('date', '<=', 15);
        } elseif ($quincena === 'Q2') {
            $holidayQuery->whereDay('date', '>', 15);
        }
        $holidays = $holidayQuery->pluck('date')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        $monthlySalary = (float) $employee->salary;
        $hourlyRate    = ($monthlySalary / 23.83)/8;

        $totalOvertimePay = 0;
        $totalHours = 0;

        $details = [
            'diurnas'           => 0,
            'nocturnas'         => 0,
            'feriados_descanso' => 0,
        ];

        foreach ($requests as $req) {
            $date = $req->overtime_date->format('Y-m-d');
            $dayOfWeek = $req->overtime_date->dayOfWeek; // 0 (Sun) to 6 (Sat)

            $isRestDay = ($dayOfWeek === 0 && $company->sunday_rest) || ($dayOfWeek === 6 && $company->saturday_rest);
            $isHoliday = in_array($date, $holidays);

            $hours = (float) $req->overtime_hours;
            $totalHours += $hours;

            if ($isRestDay || $isHoliday) {
                // All hours are at 100% surcharge (2.0x)
                $totalOvertimePay += ($hourlyRate * 2.0) * $hours;
                $details['feriados_descanso'] += $hours;
            } else {
                // Split logic: Day (07:00-21:00) @ 1.35x, Night (21:00-07:00) @ 2.0x
                $start = Carbon::parse($req->overtime_start);
                $end   = Carbon::parse($req->overtime_end);

                $nightStart = Carbon::parse($req->overtime_start)->setTime(21, 0, 0);
                $dayStart   = Carbon::parse($req->overtime_start)->setTime(7, 0, 0);

                $dayHours   = 0;
                $nightHours = 0;

                // Night part (before 7 AM)
                if ($start->lt($dayStart)) {
                    $nightEndRef = $end->lt($dayStart) ? $end : $dayStart;
                    $nightHours += $start->diffInMinutes($nightEndRef) / 60;
                    $current = $nightEndRef;
                } else {
                    $current = $start;
                }

                // Day part (7 AM to 9 PM)
                if ($current->lt($nightStart) && $end->gt($dayStart)) {
                    $dayEndRef   = $end->lt($nightStart) ? $end : $nightStart;
                    $dayStartRef = $current->gt($dayStart) ? $current : $dayStart;
                    if ($dayEndRef->gt($dayStartRef)) {
                        $dayHours += $dayStartRef->diffInMinutes($dayEndRef) / 60;
                    }
                    $current = $dayEndRef;
                }

                // Night part (after 9 PM)
                if ($end->gt($nightStart)) {
                    $nightStartRef = $current->gt($nightStart) ? $current : $nightStart;
                    $nightHours += $nightStartRef->diffInMinutes($end) / 60;
                }

                $totalOvertimePay += ($hourlyRate * 1.35 * $dayHours) + ($hourlyRate * 2.0 * $nightHours);
                $details['diurnas']   += $dayHours;
                $details['nocturnas'] += $nightHours;
            }
        }

        return response()->json([
            'overtime_hours' => round($totalHours, 2),
            'overtime_pay'   => round($totalOvertimePay, 2),
            'hourly_rate'    => round($hourlyRate, 4),
            'details'        => $details,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id'  => 'required|exists:employees,id',
            'period'       => 'required|string|max:20',
            'gross_salary' => 'required|numeric|min:0',
            'extras'       => 'nullable|numeric|min:0',
            'descuentos'   => 'nullable|numeric|min:0',
            'ars'          => 'required|numeric|min:0',
            'afp'          => 'required|numeric|min:0',
            'isr'          => 'required|numeric|min:0',
            'payment_date' => 'nullable|date',
            'overtime_pay' => 'nullable|numeric|min:0',
        ]);

        $salary           = $data['gross_salary'];
        $extras           = $data['extras'] ?? 0;
        $overtime_pay     = $data['overtime_pay'] ?? 0;
        $descuentos_otros = $data['descuentos'] ?? 0;

        // Si hay pago de horas extra, se suma a extras
        $total_extras = $extras + $overtime_pay;

        // Determinar multiplicador según frecuencia de nómina de la empresa
        // Mensual → 12 períodos/año | Quincenal → 24 períodos/año
        $company     = Auth::user()->company;
        $multiplier  = ($company->payroll_frequency === 'biweekly') ? 24 : 12;

        // Recalculate taxes on server-side for integrity
        $employeeRecord = Employee::find($data['employee_id']);
        $ars_extra = $employeeRecord->total_ars_extra;
        $ars = ($salary * 0.0304) + $ars_extra;
        $afp = $salary * 0.0287;

        // base_imponible anualizada = ((salario + extras) * mult) - ((ARS * mult) + (AFP * mult))
        $base_imponible = (($salary + $total_extras) * $multiplier) - (($ars * $multiplier) + ($afp * $multiplier));

        $isrAnnual = 0;
        if ($base_imponible <= 416220) {
            $isrAnnual = 0;
        } elseif ($base_imponible < 624329) {
            $isrAnnual = ($base_imponible - 416220) * 0.15;
        } elseif ($base_imponible < 867123) {
            $isrAnnual = ($base_imponible - 624329) * 0.20 + 31216.35;
        } else {
            $isrAnnual = ($base_imponible - 867123) * 0.25 + (31216.35 + 48558.80);
        }
        // ISR por período = ISR anual / multiplicador
        $isr = $isrAnnual / $multiplier;

        // Total deductions = ARS + AFP + ISR + Other discounts
        $total_deductions = $ars + $afp + $isr + $descuentos_otros;

        Payroll::create([
            'employee_id'  => $data['employee_id'],
            'company_id'   => Auth::user()->company_id,
            'period'       => $data['period'],
            'gross_salary' => $salary,
            'extras'       => $total_extras,
            'descuentos'   => $descuentos_otros,
            'ars'          => $ars,
            'afp'          => $afp,
            'isr'          => $isr,
            'deductions'   => $total_deductions,
            'net_salary'   => ($salary + $total_extras) - $total_deductions,
            'payment_date' => $data['payment_date'] ?? null,
            'status'       => 'pending',
        ]);

        return redirect()->route('payroll.index')->with('success', 'Nómina registrada exitosamente.');
    }

    public function markPaid(Payroll $payroll)
    {
        if ($payroll->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $payroll->load('employee.user');

        $payroll->update([
            'status' => 'paid',
            'payment_date' => $payroll->payment_date ?? now(),
        ]);

        // Enviar correo con volante de pago al empleado
        if ($payroll->employee->user && $payroll->employee->user->email) {
            \Mail::to($payroll->employee->user->email)->send(new \App\Mail\PayrollReceipt($payroll));
        }

        return redirect()->route('payroll.index')->with('success', 'Nómina marcada como pagada y volante enviado al empleado.');
    }

    public function destroy(Payroll $payroll)
    {
        if ($payroll->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $payroll->delete();
        return redirect()->route('payroll.index')->with('success', 'Nómina eliminada.');
    }

    public function tss(Request $request)
    {
        $company = Auth::user()->company;
        $period = $request->get('period', date('Y-m'));

        $payrollData = Payroll::where('company_id', $company->id)
            ->where('period', $period)
            ->with('employee.user')
            ->get();

        // Topes 2026 (Basados en salario mínimo nacional de RD$ 23,223.00)
        $topes = [
            'sfs' => 232230.00, // 10 salarios
            'afp' => 464460.00, // 20 salarios
            'srl' => 92892.00,  // 4 salarios
        ];

        $report = $payrollData->map(function ($p) use ($topes, $company) {
            $salary = $p->gross_salary;
            
            // Bases Cotizables
            $baseSFS = min($salary, $topes['sfs']);
            $baseAFP = min($salary, $topes['afp']);
            $baseSRL = min($salary, $topes['srl']);
            $baseINFOTEP = $salary;

            return [
                'employee' => $p->employee->user->name,
                'salary'   => $salary,
                // SFS (ARS)
                'sfs_emp'  => $baseSFS * 0.0304,
                'sfs_pat'  => $baseSFS * 0.0709,
                // AFP
                'afp_emp'  => $baseAFP * 0.0287,
                'afp_pat'  => $baseAFP * 0.0710,
                // SRL
                'srl_pat'  => $baseSRL * (($company->srl_rate ?? 1.10) / 100),
                // INFOTEP
                'infotep_pat' => $baseINFOTEP * 0.0100,
            ];
        });

        $availablePeriods = Payroll::where('company_id', $company->id)
            ->distinct()
            ->pluck('period')
            ->sortDesc();

        if (!$availablePeriods->contains($period)) {
            $availablePeriods->prepend($period);
        }

        return view('payroll.tss', compact('report', 'period', 'availablePeriods', 'company'));
    }

    public function autoGenerate(Request $request)
    {
        $request->validate([
            'period' => 'required|string|max:20',
        ]);

        $company = Auth::user()->company;
        $period = $request->period;
        $multiplier = ($company->payroll_frequency === 'biweekly') ? 24 : 12;
        $isBiweekly = $company->payroll_frequency === 'biweekly';

        // Get all active employees for the company
        $employees = Employee::where('company_id', $company->id)
            ->with('user')
            ->get();

        $generatedCount = 0;
        $skippedCount = 0;

        foreach ($employees as $employee) {
            // Check if payroll already exists for this employee and period
            $existing = Payroll::where('employee_id', $employee->id)
                ->where('period', $period)
                ->first();

            if ($existing) {
                $skippedCount++;
                continue;
            }

            // Calculate salary based on payroll frequency
            $salary = $isBiweekly ? $employee->salary / 2 : $employee->salary;

            // Calculate overtime pay for the period
            $overtime_pay = $this->calculateOvertimePay($employee, $period, $company);

            // Calculate taxes
            $ars_extra = $employee->total_ars_extra ?? 0;
            $ars = ($salary * 0.0304) + $ars_extra;
            $afp = $salary * 0.0287;

            $total_extras = $overtime_pay;
            $base_imponible = (($salary + $total_extras) * $multiplier) - (($ars * $multiplier) + ($afp * $multiplier));

            $isrAnnual = 0;
            if ($base_imponible <= 416220) {
                $isrAnnual = 0;
            } elseif ($base_imponible < 624329) {
                $isrAnnual = ($base_imponible - 416220) * 0.15;
            } elseif ($base_imponible < 867123) {
                $isrAnnual = ($base_imponible - 624329) * 0.20 + 31216.35;
            } else {
                $isrAnnual = ($base_imponible - 867123) * 0.25 + (31216.35 + 48558.80);
            }

            $isr = $isrAnnual / $multiplier;
            $total_deductions = $ars + $afp + $isr;

            Payroll::create([
                'employee_id'  => $employee->id,
                'company_id'   => $company->id,
                'period'       => $period,
                'gross_salary' => $salary,
                'extras'       => $total_extras,
                'descuentos'   => 0,
                'ars'          => $ars,
                'afp'          => $afp,
                'isr'          => $isr,
                'deductions'   => $total_deductions,
                'net_salary'   => ($salary + $total_extras) - $total_deductions,
                'payment_date' => null,
                'status'       => 'pending',
            ]);

            $generatedCount++;
        }

        return redirect()->route('payroll.index')->with('success', "Se generaron {$generatedCount} nóminas automáticamente. {$skippedCount} empleados ya tenían nómina para este período.");
    }

    private function calculateOvertimePay($employee, $period, $company)
    {
        // Detectar si es período quincenal (Y-m-Q1 o Y-m-Q2)
        $quincena = null;
        if (preg_match('/^(\d{4}-\d{2})-(Q[12])$/', $period, $m)) {
            $basePeriod = $m[1];
            $quincena   = $m[2];
        } else {
            $basePeriod = $period;
        }

        [$year, $month] = explode('-', $basePeriod);
        $year = (int) $year;
        $month = (int) $month;

        $query = \DB::table('requests')
            ->where('company_id', $company->id)
            ->where('user_id', $employee->user_id)
            ->where('type', 'overtime')
            ->where('status', 'approved')
            ->whereYear('overtime_date', $year)
            ->whereMonth('overtime_date', $month);

        // Filtrar por quincena si aplica
        if ($quincena === 'Q1') {
            $query->whereDay('overtime_date', '<=', 15);
        } elseif ($quincena === 'Q2') {
            $query->whereDay('overtime_date', '>', 15);
        }

        $requests = $query->get();

        // Cargar festivos del mes - usar DB::raw para evitar problemas con casts
        $holidayQuery = \DB::table('holidays')
            ->where('company_id', $company->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month);
        if ($quincena === 'Q1') {
            $holidayQuery->whereDay('date', '<=', 15);
        } elseif ($quincena === 'Q2') {
            $holidayQuery->whereDay('date', '>', 15);
        }
        $holidays = $holidayQuery->pluck('date')->toArray();

        $monthlySalary = (float) $employee->salary;
        $hourlyRate    = ($monthlySalary / 23.83)/8;

        $totalOvertimePay = 0;

        foreach ($requests as $req) {
            $overtimeDate = Carbon::parse($req->overtime_date);
            $date = $overtimeDate->format('Y-m-d');
            $dayOfWeek = $overtimeDate->dayOfWeek;

            $isRestDay = ($dayOfWeek === 0 && $company->sunday_rest) || ($dayOfWeek === 6 && $company->saturday_rest);
            $isHoliday = in_array($date, $holidays);

            $hours = (float) $req->overtime_hours;

            if ($isRestDay || $isHoliday) {
                $totalOvertimePay += ($hourlyRate * 2.0) * $hours;
            } else {
                $start = Carbon::parse($req->overtime_start);
                $end   = Carbon::parse($req->overtime_end);

                $nightStart = Carbon::parse($req->overtime_start)->setTime(21, 0, 0);
                $dayStart   = Carbon::parse($req->overtime_start)->setTime(7, 0, 0);

                $dayHours   = 0;
                $nightHours = 0;

                if ($start->lt($dayStart)) {
                    $nightEndRef = $end->lt($dayStart) ? $end : $dayStart;
                    $nightHours += $start->diffInMinutes($nightEndRef) / 60;
                    $current = $nightEndRef;
                } else {
                    $current = $start;
                }

                if ($current->lt($nightStart) && $end->gt($dayStart)) {
                    $dayEndRef   = $end->lt($nightStart) ? $end : $nightStart;
                    $dayStartRef = $current->gt($dayStart) ? $current : $dayStart;
                    if ($dayEndRef->gt($dayStartRef)) {
                        $dayHours += $dayStartRef->diffInMinutes($dayEndRef) / 60;
                    }
                    $current = $dayEndRef;
                }

                if ($end->gt($nightStart)) {
                    $nightStartRef = $current->gt($nightStart) ? $current : $nightStart;
                    $nightHours += $nightStartRef->diffInMinutes($end) / 60;
                }

                $totalOvertimePay += ($hourlyRate * 1.35 * $dayHours) + ($hourlyRate * 2.0 * $nightHours);
            }
        }

        return $totalOvertimePay;
    }

    public function edit(Payroll $payroll)
    {
        if ($payroll->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        if ($payroll->status === 'paid') {
            return redirect()->route('payroll.index')->with('error', 'No se puede editar una nómina marcada como pagada.');
        }

        $user      = Auth::user();
        $company   = $user->company;
        $employees = Employee::where('company_id', $user->company_id)
            ->with('user')->get();

        $isBiweekly = $company->payroll_frequency === 'biweekly';

        // Generar lista de períodos (últimos 12 meses y próximo)
        $periods = [];
        for ($i = -12; $i <= 1; $i++) {
            $date = Carbon::now()->addMonths($i);
            if ($isBiweekly) {
                $periods[] = [
                    'value' => $date->format('Y-m') . '-Q1',
                    'label' => ucfirst($date->translatedFormat('F Y')) . ' — 1ª Quincena (1-15)',
                ];
                $periods[] = [
                    'value' => $date->format('Y-m') . '-Q2',
                    'label' => ucfirst($date->translatedFormat('F Y')) . ' — 2ª Quincena (16-fin)',
                ];
            } else {
                $periods[] = [
                    'value' => $date->format('Y-m'),
                    'label' => ucfirst($date->translatedFormat('F Y')),
                ];
            }
        }
        $periods = array_reverse($periods);

        return view('payroll.edit', compact('payroll', 'employees', 'periods', 'isBiweekly'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        if ($payroll->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        if ($payroll->status === 'paid') {
            return redirect()->route('payroll.index')->with('error', 'No se puede editar una nómina marcada como pagada.');
        }

        $data = $request->validate([
            'employee_id'  => 'required|exists:employees,id',
            'period'       => 'required|string|max:20',
            'gross_salary' => 'required|numeric|min:0',
            'extras'       => 'nullable|numeric|min:0',
            'descuentos'   => 'nullable|numeric|min:0',
            'ars'          => 'required|numeric|min:0',
            'afp'          => 'required|numeric|min:0',
            'isr'          => 'required|numeric|min:0',
            'payment_date' => 'nullable|date',
            'overtime_pay' => 'nullable|numeric|min:0',
        ]);

        $salary           = $data['gross_salary'];
        $extras           = $data['extras'] ?? 0;
        $overtime_pay     = $data['overtime_pay'] ?? 0;
        $descuentos_otros = $data['descuentos'] ?? 0;

        $total_extras = $extras + $overtime_pay;

        $company     = Auth::user()->company;
        $multiplier  = ($company->payroll_frequency === 'biweekly') ? 24 : 12;

        $employeeRecord = Employee::find($data['employee_id']);
        $ars_extra = $employeeRecord->total_ars_extra;
        $ars = ($salary * 0.0304) + $ars_extra;
        $afp = $salary * 0.0287;

        $base_imponible = (($salary + $total_extras) * $multiplier) - (($ars * $multiplier) + ($afp * $multiplier));

        $isrAnnual = 0;
        if ($base_imponible <= 416220) {
            $isrAnnual = 0;
        } elseif ($base_imponible < 624329) {
            $isrAnnual = ($base_imponible - 416220) * 0.15;
        } elseif ($base_imponible < 867123) {
            $isrAnnual = ($base_imponible - 624329) * 0.20 + 31216.35;
        } else {
            $isrAnnual = ($base_imponible - 867123) * 0.25 + (31216.35 + 48558.80);
        }

        $isr = $isrAnnual / $multiplier;
        $total_deductions = $ars + $afp + $isr + $descuentos_otros;

        $payroll->update([
            'employee_id'  => $data['employee_id'],
            'period'       => $data['period'],
            'gross_salary' => $salary,
            'extras'       => $total_extras,
            'descuentos'   => $descuentos_otros,
            'ars'          => $ars,
            'afp'          => $afp,
            'isr'          => $isr,
            'deductions'   => $total_deductions,
            'net_salary'   => ($salary + $total_extras) - $total_deductions,
            'payment_date' => $data['payment_date'] ?? null,
        ]);

        return redirect()->route('payroll.index')->with('success', 'Nómina actualizada exitosamente.');
    }
}
