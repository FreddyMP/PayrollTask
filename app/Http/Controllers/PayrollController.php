<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\UserRequest;
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

        return view('payroll.create', compact('employees', 'currentPeriod'));
    }

    public function bonuses()
    {
        $employees = Employee::where('company_id', Auth::user()->company_id)
            ->with('user')
            ->get();

        return view('payroll.bonuses', compact('employees'));
    }

    /**
     * API: Devuelve horas extra aprobadas y monto segun CT dominicano
     * para un empleado en un periodo (Y-m) dado.
     */
    public function apiOvertimeData(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $period     = $request->input('period', date('Y-m')); // e.g. "2026-04"

        $employee = Employee::where('company_id', Auth::user()->company_id)
            ->where('id', $employeeId)
            ->with('user')
            ->firstOrFail();

        // Buscar en requests: tipo overtime, aprobadas, del usuario del empleado, en el mes del periodo
        [$year, $month] = explode('-', $period);

        $overtimeHours = UserRequest::where('company_id', Auth::user()->company_id)
            ->where('user_id', $employee->user_id)
            ->where('type', 'overtime')
            ->where('status', 'approved')
            ->whereYear('overtime_date', $year)
            ->whereMonth('overtime_date', $month)
            ->sum('overtime_hours');

        $overtimeHours = round((float) $overtimeHours, 2);

        // --- Calculo segun Codigo de Trabajo RD (Art. 203) ---
        // Hora ordinaria = salario_mensual / 173.33  (40h/semana * 52 semanas / 12 meses)
        // Hora extra (dias laborables) = hora_ordinaria * 1.35
        // Nota: horas en dia de descanso = hora_ordinaria * 2.0
        // Para este calculo usamos el recargo de dias laborables (35%) como
        // valor general; el admin puede ajustar si corresponde dia de descanso.
        $monthlySalary   = (float) $employee->salary;
        $hourlyRate      = $monthlySalary / 173.33;
        $overtimeRate    = $hourlyRate * 1.35;          // +35% recargo CT-RD
        $overtimePay     = round($overtimeRate * $overtimeHours, 2);

        return response()->json([
            'overtime_hours' => $overtimeHours,
            'overtime_pay'   => $overtimePay,
            'hourly_rate'    => round($hourlyRate, 4),
            'overtime_rate'  => round($overtimeRate, 4),
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
        $ars = $salary * 0.0304;
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
}
