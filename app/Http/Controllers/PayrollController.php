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
        $employees = Employee::where('company_id', Auth::user()->company_id)
            ->with('user')->get();

        $currentPeriod = date('Y-m');

        // Generar lista de periodos (últimos 12 meses y próximo)
        $periods = [];
        for ($i = -12; $i <= 1; $i++) {
            $date = Carbon::now()->addMonths($i);
            $periods[] = [
                'value' => $date->format('Y-m'),
                'label' => ucfirst($date->translatedFormat('F Y'))
            ];
        }
        $periods = array_reverse($periods);

        return view('payroll.create', compact('employees', 'currentPeriod', 'periods'));
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

    /**
     * API: Devuelve horas extra aprobadas y monto segun CT dominicano
     * para un empleado en un periodo (Y-m) dado.
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

        [$year, $month] = explode('-', $period);

        $requests = UserRequest::where('company_id', $user->company_id)
            ->where('user_id', $employee->user_id)
            ->where('type', 'overtime')
            ->where('status', 'approved')
            ->whereYear('overtime_date', $year)
            ->whereMonth('overtime_date', $month)
            ->get();

        $holidays = Holiday::where('company_id', $user->company_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->pluck('date')
            ->map(fn($d) => $d->format('Y-m-d'))
            ->toArray();

        $monthlySalary = (float) $employee->salary;
        $hourlyRate    = ($monthlySalary / 23.83)/8;

        $totalOvertimePay = 0;
        $totalHours = 0;
        
        $details = [
            'diurnas' => 0,
            'nocturnas' => 0,
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

                // Default night boundaries for the same day
                $nightStart = Carbon::parse($req->overtime_start)->setTime(21, 0, 0);
                $dayStart   = Carbon::parse($req->overtime_start)->setTime(7, 0, 0);

                $dayHours = 0;
                $nightHours = 0;

                // Simple interval intersection logic for a single day
                // We assume start < end as validated in RequestController
                
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
                    $dayEndRef = $end->lt($nightStart) ? $end : $nightStart;
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
                $details['diurnas'] += $dayHours;
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

        $salary             = $data['gross_salary'];
        $extras             = $data['extras'] ?? 0;
        $overtime_pay       = $data['overtime_pay'] ?? 0;   // incluido en extras automaticamente
        $descuentos_otros   = $data['descuentos'] ?? 0;

        // Si hay pago de horas extra, se suma a extras
        $total_extras = $extras + $overtime_pay;
        
        // Recalculate taxes on server-side for integrity
        $employeeRecord = Employee::find($data['employee_id']);
        $ars_extra = $employeeRecord->total_ars_extra;
        $ars = ($salary * 0.0304) + $ars_extra;
        $afp = $salary * 0.0287;
        
        // base_imponible = (salario * 12) - ((ARS * 12) + (AFP * 12))
        $base_imponible = ($salary * 12) - (($ars * 12) + ($afp * 12));
        
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
        $isr = $isrAnnual / 12;

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

        $payroll->update([
            'status' => 'paid',
            'payment_date' => $payroll->payment_date ?? now(),
        ]);

        return redirect()->route('payroll.index')->with('success', 'Nómina marcada como pagada.');
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
}
