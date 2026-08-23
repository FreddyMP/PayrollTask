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

        // Filtro por nombre de empleado
        if ($request->filled('employee_name')) {
            $query->whereHas('employee.user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->employee_name . '%');
            });
        }

        // Filtro por período
        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por rango de salario (mínimo)
        if ($request->filled('salary_min')) {
            $query->where('net_salary', '>=', $request->salary_min);
        }

        // Filtro por rango de salario (máximo)
        if ($request->filled('salary_max')) {
            $query->where('net_salary', '<=', $request->salary_max);
        }

        $payrolls = $query->latest()->paginate(15)->appends($request->query());

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

        $paidEmployeeIds = \App\Models\Payroll::where('company_id', Auth::user()->company_id)
            ->where('period', $now->year . '-NAVIDAD')
            ->pluck('employee_id')
            ->toArray();

        return view('payroll.christmas', compact('employees', 'paidEmployeeIds'));
    }

    public function payChristmas(Employee $employee)
    {
        $companyId = Auth::user()->company_id;
        if ($employee->company_id !== $companyId) {
            abort(403);
        }

        $now = \Carbon\Carbon::now();
        if (!$employee->hire_date) {
            return back()->with('error', 'Empleado sin fecha de ingreso.');
        }

        $hireDate = \Carbon\Carbon::parse($employee->hire_date);
        $months = $hireDate->diffInMonths($now);

        if ($months >= 12) {
            $amount = $employee->salary;
        } else {
            $floatMonths = $hireDate->floatDiffInMonths($now);
            $monthsWorked = max(1, (int) ceil($floatMonths));
            $amount = ($employee->salary * $monthsWorked) / 12;
        }

        $period = $now->year . '-NAVIDAD';

        $payroll = Payroll::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'company_id' => $companyId,
                'period' => $period,
            ],
            [
                'gross_salary' => $amount,
                'extras' => 0,
                'descuentos' => 0,
                'ars' => 0,
                'afp' => 0,
                'isr' => 0,
                'deductions' => 0,
                'net_salary' => $amount,
                'status' => 'paid',
                'payment_date' => $now,
            ]
        );

        if ($employee->user && $employee->user->email) {
            \Mail::to($employee->user->email)->send(new \App\Mail\ChristmasReceipt($payroll));
        }

        return back()->with('success', 'Salario de Navidad marcado como pagado y correo enviado.');
    }

    public function ir17(Request $request)
    {
        $company = Auth::user()->company;
        $period = $request->get('period', date('Y-m'));
        $period = substr($period, 0, 7); // Force Y-m format

        $payrollData = Payroll::where('company_id', $company->id)
            ->where('period', 'like', $period . '%')
            ->with('employee.user', 'employee.arsExtras')
            ->get();

        $grouped = $payrollData->groupBy('employee_id');

        $report = $grouped->map(function ($payrolls) use ($company) {
            $first = $payrolls->first();
            $salary = $payrolls->sum('gross_salary');
            $extras = $payrolls->sum('extras');
            $remuneracionBruta = $salary + $extras;

            // Aportes TSS del empleado (mensual)
            $sfs = $salary * 0.0304;
            $afp = $salary * 0.0287;
            $arsExtra = $first->employee->total_ars_extra ?? 0;
            $totalTSS = $sfs + $afp + $arsExtra;

            // Renta neta imponible (anualizada, cálculo mensual siempre multiplicamos por 12)
            $multiplier = 12;
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
                'cedula'             => $first->employee->id_number ?? '—',
                'nombre'             => $first->employee->user->name,
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
        })->values();

        $availablePeriods = Payroll::where('company_id', $company->id)
            ->distinct()
            ->pluck('period')
            ->map(function ($p) {
                return substr($p, 0, 7);
            })
            ->unique()
            ->sortDesc()
            ->values();

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
            'payment_date' => 'nullable|date',
            'overtime_pay' => 'nullable|numeric|min:0',
            'incentives'   => 'nullable|array',
            'discounts'    => 'nullable|array',
        ]);

        $salary           = $data['gross_salary'];
        $overtime_pay     = $data['overtime_pay'] ?? 0;
        
        $total_incentives = 0;
        $taxable_incentives = 0;
        $incentives_details = [];
        if (!empty($data['incentives'])) {
            foreach ($data['incentives'] as $inc) {
                $amount = (float) $inc['amount'];
                $total_incentives += $amount;
                if (!empty($inc['is_taxable'])) {
                    $taxable_incentives += $amount;
                }
                $incentives_details[] = [
                    'description' => $inc['description'],
                    'amount' => $amount,
                    'is_taxable' => !empty($inc['is_taxable']),
                ];
            }
        }

        $total_discounts = 0;
        $tax_affecting_discounts = 0;
        $discounts_details = [];
        if (!empty($data['discounts'])) {
            foreach ($data['discounts'] as $disc) {
                $amount = (float) $disc['amount'];
                $total_discounts += $amount;
                if (!empty($disc['affects_taxes'])) {
                    $tax_affecting_discounts += $amount;
                }
                $discounts_details[] = [
                    'description' => $disc['description'],
                    'amount' => $amount,
                    'affects_taxes' => !empty($disc['affects_taxes']),
                ];
            }
        }

        // Si hay pago de horas extra, se suma a extras
        $total_extras = $total_incentives + $overtime_pay;
        $total_taxable_extras = $taxable_incentives + $overtime_pay;

        // Determinar multiplicador según frecuencia de nómina de la empresa
        // Mensual → 12 períodos/año | Quincenal → 24 períodos/año
        $company     = Auth::user()->company;
        $multiplier  = ($company->payroll_frequency === 'biweekly') ? 24 : 12;

        // Recalculate taxes on server-side for integrity
        $employeeRecord = Employee::find($data['employee_id']);
        $ars_extra = $employeeRecord->total_ars_extra;
        $ars = ($salary * 0.0304) + $ars_extra;
        $afp = $salary * 0.0287;

        // base_imponible anualizada = ((salario + extras gravables) * mult) - ((ARS + AFP + descuentos que afectan impuestos) * mult)
        $base_imponible = (($salary + $total_taxable_extras) * $multiplier) - (($ars + $afp + $tax_affecting_discounts) * $multiplier);
        if ($base_imponible < 0) $base_imponible = 0;

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
        $total_deductions = $ars + $afp + $isr + $total_discounts;

        Payroll::create([
            'employee_id'  => $data['employee_id'],
            'company_id'   => Auth::user()->company_id,
            'period'       => $data['period'],
            'gross_salary' => $salary,
            'extras'       => $total_extras,
            'incentives_details' => $incentives_details,
            'descuentos'   => $total_discounts,
            'discounts_details'  => $discounts_details,
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

    public function markAllPaid(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $company   = Auth::user()->company;

        // Determinar período actual
        $isBiweekly    = $company->payroll_frequency === 'biweekly';
        $currentPeriod = $isBiweekly
            ? date('Y-m') . (date('j') <= 15 ? '-Q1' : '-Q2')
            : date('Y-m');

        // Obtener todas las nóminas pendientes del período actual
        $pendingPayrolls = Payroll::where('company_id', $companyId)
            ->where('period', $currentPeriod)
            ->where('status', 'pending')
            ->with('employee.user')
            ->get();

        if ($pendingPayrolls->isEmpty()) {
            return redirect()->route('payroll.index')
                ->with('error', 'No hay nóminas pendientes para el período actual.');
        }

        $markedCount = 0;
        foreach ($pendingPayrolls as $payroll) {
            $payroll->update([
                'status'       => 'paid',
                'payment_date' => $payroll->payment_date ?? now(),
            ]);

            // Enviar correo con volante de pago al empleado
            if ($payroll->employee && $payroll->employee->user && $payroll->employee->user->email) {
                \Mail::to($payroll->employee->user->email)->send(new \App\Mail\PayrollReceipt($payroll));
            }

            $markedCount++;
        }

        return redirect()->route('payroll.index')
            ->with('success', "Se marcaron {$markedCount} nóminas como pagadas y se enviaron los volantes de pago correspondientes.");
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
        $period = substr($period, 0, 7); // Force Y-m format

        $payrollData = Payroll::where('company_id', $company->id)
            ->where('period', 'like', $period . '%')
            ->with('employee.user')
            ->get();

        $grouped = $payrollData->groupBy('employee_id');

        // Topes 2026 (Basados en salario mínimo nacional de RD$ 23,223.00)
        $topes = [
            'sfs' => 232230.00, // 10 salarios
            'afp' => 464460.00, // 20 salarios
            'srl' => 92892.00,  // 4 salarios
        ];

        $report = $grouped->map(function ($payrolls) use ($topes, $company) {
            $first = $payrolls->first();
            $salary = $payrolls->sum('gross_salary');

            // Bases Cotizables
            $baseSFS = min($salary, $topes['sfs']);
            $baseAFP = min($salary, $topes['afp']);
            $baseSRL = min($salary, $topes['srl']);
            $baseINFOTEP = $salary;

            return [
                'employee' => $first->employee->user->name,
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
        })->values();

        $availablePeriods = Payroll::where('company_id', $company->id)
            ->distinct()
            ->pluck('period')
            ->map(function ($p) {
                return substr($p, 0, 7);
            })
            ->unique()
            ->sortDesc()
            ->values();

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
            'payment_date' => 'nullable|date',
            'overtime_pay' => 'nullable|numeric|min:0',
            'incentives'   => 'nullable|array',
            'discounts'    => 'nullable|array',
        ]);

        $salary           = $data['gross_salary'];
        $overtime_pay     = $data['overtime_pay'] ?? 0;

        $total_incentives = 0;
        $taxable_incentives = 0;
        $incentives_details = [];
        if (!empty($data['incentives'])) {
            foreach ($data['incentives'] as $inc) {
                $amount = (float) $inc['amount'];
                $total_incentives += $amount;
                if (!empty($inc['is_taxable'])) {
                    $taxable_incentives += $amount;
                }
                $incentives_details[] = [
                    'description' => $inc['description'],
                    'amount' => $amount,
                    'is_taxable' => !empty($inc['is_taxable']),
                ];
            }
        }

        $total_discounts = 0;
        $tax_affecting_discounts = 0;
        $discounts_details = [];
        if (!empty($data['discounts'])) {
            foreach ($data['discounts'] as $disc) {
                $amount = (float) $disc['amount'];
                $total_discounts += $amount;
                if (!empty($disc['affects_taxes'])) {
                    $tax_affecting_discounts += $amount;
                }
                $discounts_details[] = [
                    'description' => $disc['description'],
                    'amount' => $amount,
                    'affects_taxes' => !empty($disc['affects_taxes']),
                ];
            }
        }

        $total_extras = $total_incentives + $overtime_pay;
        $total_taxable_extras = $taxable_incentives + $overtime_pay;

        $company     = Auth::user()->company;
        $multiplier  = ($company->payroll_frequency === 'biweekly') ? 24 : 12;

        $employeeRecord = Employee::find($data['employee_id']);
        $ars_extra = $employeeRecord->total_ars_extra;
        $ars = ($salary * 0.0304) + $ars_extra;
        $afp = $salary * 0.0287;

        $base_imponible = (($salary + $total_taxable_extras) * $multiplier) - (($ars + $afp + $tax_affecting_discounts) * $multiplier);
        if ($base_imponible < 0) $base_imponible = 0;

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
        $total_deductions = $ars + $afp + $isr + $total_discounts;

        $payroll->update([
            'employee_id'  => $data['employee_id'],
            'period'       => $data['period'],
            'gross_salary' => $salary,
            'extras'       => $total_extras,
            'incentives_details' => $incentives_details,
            'descuentos'   => $total_discounts,
            'discounts_details'  => $discounts_details,
            'ars'          => $ars,
            'afp'          => $afp,
            'isr'          => $isr,
            'deductions'   => $total_deductions,
            'net_salary'   => ($salary + $total_extras) - $total_deductions,
            'payment_date' => $data['payment_date'] ?? null,
        ]);

        return redirect()->route('payroll.index')->with('success', 'Nómina actualizada exitosamente.');
    }

    public function addBonusesToPayroll(Request $request)
    {
        $company = Auth::user()->company;
        if ($company->bonus_payment_method !== 'payroll') {
            return back()->with('error', 'La empresa no está configurada para pagar bonificaciones con la nómina.');
        }

        $bonuses = $request->input('bonuses', []);
        if (empty($bonuses)) {
            return back()->with('error', 'No se recibieron datos de bonificación.');
        }

        $isBiweekly = $company->payroll_frequency === 'biweekly';
        $splitMethod = $company->bonus_biweekly_split;
        $multiplier = $isBiweekly ? 24 : 12;

        $now = \Carbon\Carbon::now();
        $basePeriod = $now->format('Y-m');

        // Verificar si ya existen nóminas generadas en los períodos
        $periodsToCheck = [];
        if ($isBiweekly) {
            if ($splitMethod === 'both') {
                $periodsToCheck = [$basePeriod . '-Q1', $basePeriod . '-Q2'];
            } elseif ($splitMethod === 'q1') {
                $periodsToCheck = [$basePeriod . '-Q1'];
            } else {
                $periodsToCheck = [$basePeriod . '-Q2'];
            }
        } else {
            $periodsToCheck = [$basePeriod];
        }

        $existingPayrolls = Payroll::where('company_id', $company->id)
            ->whereIn('period', $periodsToCheck)
            ->exists();

        if ($existingPayrolls) {
            return back()->with('payroll_exists_error', true);
        }

        foreach ($bonuses as $employeeId => $amount) {
            if ($amount <= 0) continue;

            $employee = Employee::find($employeeId);
            if (!$employee || $employee->company_id !== $company->id) continue;

            // Determine periods to update/create
            $periodsToUpdate = [];
            if ($isBiweekly) {
                if ($splitMethod === 'both') {
                    $periodsToUpdate = [
                        ['period' => $basePeriod . '-Q1', 'amount' => $amount / 2],
                        ['period' => $basePeriod . '-Q2', 'amount' => $amount / 2],
                    ];
                } elseif ($splitMethod === 'q1') {
                    $periodsToUpdate = [['period' => $basePeriod . '-Q1', 'amount' => $amount]];
                } else { // q2
                    $periodsToUpdate = [['period' => $basePeriod . '-Q2', 'amount' => $amount]];
                }
            } else {
                $periodsToUpdate = [['period' => $basePeriod, 'amount' => $amount]];
            }

            foreach ($periodsToUpdate as $pData) {
                $period = $pData['period'];
                $bonusAmount = $pData['amount'];

                $payroll = Payroll::firstOrNew([
                    'employee_id' => $employee->id,
                    'company_id'  => $company->id,
                    'period'      => $period,
                ]);

                if ($payroll->status === 'paid') {
                    continue; // Skip already paid payrolls
                }

                $salary = $isBiweekly ? $employee->salary / 2 : $employee->salary;
                $payroll->gross_salary = $salary;

                // Add bonus to extras
                $currentExtras = $payroll->exists ? $payroll->extras : 0;
                $payroll->extras = $currentExtras + $bonusAmount;

                $payroll->status = $payroll->exists ? $payroll->status : 'pending';

                // Recalculate deductions
                $ars_extra = $employee->total_ars_extra ?? 0;
                $ars = ($salary * 0.0304) + $ars_extra;
                $afp = $salary * 0.0287;

                $total_extras = $payroll->extras;
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
                $total_deductions = $ars + $afp + $isr + ($payroll->descuentos ?? 0);

                $payroll->ars = $ars;
                $payroll->afp = $afp;
                $payroll->isr = $isr;
                $payroll->deductions = $total_deductions;
                $payroll->net_salary = ($salary + $total_extras) - $total_deductions;
                $payroll->save();
            }
        }

        return redirect()->route('payroll.bonuses')->with('success', 'Bonificaciones agregadas a la nómina del período actual exitosamente.');
    }

    public function paySeparateBonus(Request $request, Employee $employee)
    {
        $company = Auth::user()->company;
        if ($employee->company_id !== $company->id) {
            abort(403);
        }

        if ($company->bonus_payment_method !== 'separate') {
            return back()->with('error', 'La empresa no está configurada para realizar pagos separados de bonificación.');
        }

        $amount = (float) $request->input('amount', 0);
        if ($amount <= 0) {
            return back()->with('error', 'Monto de bonificación inválido.');
        }

        $now = \Carbon\Carbon::now();
        $period = $now->year . '-BONIFICACION';

        $multiplier  = ($company->payroll_frequency === 'biweekly') ? 24 : 12;
        $monthlySalary = $employee->salary;
        $ars_extra = $employee->total_ars_extra ?? 0;
        $ars = ($monthlySalary * 0.0304) + $ars_extra;
        $afp = $monthlySalary * 0.0287;

        $base_imponible = (($monthlySalary + $amount) * $multiplier) - (($ars * $multiplier) + ($afp * $multiplier));

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

        $base_imponible_no_bonus = ($monthlySalary * $multiplier) - (($ars * $multiplier) + ($afp * $multiplier));
        $isrAnnual_no_bonus = 0;
        if ($base_imponible_no_bonus <= 416220) {
            $isrAnnual_no_bonus = 0;
        } elseif ($base_imponible_no_bonus < 624329) {
            $isrAnnual_no_bonus = ($base_imponible_no_bonus - 416220) * 0.15;
        } elseif ($base_imponible_no_bonus < 867123) {
            $isrAnnual_no_bonus = ($base_imponible_no_bonus - 624329) * 0.20 + 31216.35;
        } else {
            $isrAnnual_no_bonus = ($base_imponible_no_bonus - 867123) * 0.25 + (31216.35 + 48558.80);
        }

        $isrDeduction = ($isrAnnual - $isrAnnual_no_bonus) / $multiplier;
        if ($isrDeduction < 0) $isrDeduction = 0;

        $payroll = Payroll::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'company_id' => $company->id,
                'period' => $period,
            ],
            [
                'gross_salary' => $amount,
                'extras' => 0,
                'descuentos' => 0,
                'ars' => 0,
                'afp' => 0,
                'isr' => $isrDeduction,
                'deductions' => $isrDeduction,
                'net_salary' => $amount - $isrDeduction,
                'status' => 'paid',
                'payment_date' => $now,
            ]
        );

        if ($employee->user && $employee->user->email) {
            \Mail::to($employee->user->email)->send(new \App\Mail\BonusReceipt($payroll));
        }

        return back()->with('success', 'Bonificación de Ley marcada como pagada y correo enviado.');
    }
}
