<?php

namespace App\Http\Controllers;

use App\Mail\VacationReceipt;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\UserRequest;
use App\Models\Vacation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class VacationController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $search = $request->get('search');
        $company = Auth::user()->company;

        // Obtener todos los empleados de la empresa con sus relaciones
        $employees = Employee::where('company_id', $company->id)
            ->with(['user', 'department_rel', 'position', 'vacations' => function ($query) use ($year) {
                $query->where('year', $year)
                    ->whereIn('status', ['approved', 'completed']);
            }])
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->get()
            ->map(function ($employee) use ($year) {
                $dailySalary = $employee->daily_salary;
                $daysEntitled = $employee->vacation_days_entitled;
                $daysTaken = $employee->getVacationDaysTaken($year);
                $daysRemaining = $employee->getVacationDaysRemaining($year);

                return [
                    'id'               => $employee->id,
                    'name'             => $employee->user->name ?? 'N/A',
                    'email'            => $employee->user->email ?? 'N/A',
                    'department'       => $employee->department_rel->name ?? $employee->department ?? 'N/A',
                    'position'         => $employee->position->name ?? 'N/A',
                    'salary'           => (float) $employee->salary,
                    'daily_salary'     => $dailySalary,
                    'hire_date'        => $employee->hire_date?->format('d/m/Y') ?? 'N/A',
                    'years_of_service' => $employee->years_of_service,
                    'days_entitled'    => $daysEntitled,
                    'amount_entitled'  => round($dailySalary * $daysEntitled, 2),
                    'days_taken'       => $daysTaken,
                    'amount_taken'     => round($dailySalary * $daysTaken, 2),
                    'days_remaining'   => $daysRemaining,
                    'amount_remaining' => round($dailySalary * $daysRemaining, 2),
                    'vacations'        => $employee->vacations,
                    'can_take_vacation'=> $employee->years_of_service >= 1,
                ];
            });

        // Vacaciones aprobadas pendientes de pago elegibles (inicio de disfrute <= hoy + 5 días)
        $eligibleVacations = Vacation::where('company_id', $company->id)
            ->where('status', 'approved')
            ->where('is_paid', false)
            ->where('start_date', '<=', now()->addDays(5)->endOfDay())
            ->with(['employee.user', 'employee.department_rel', 'employee.position'])
            ->orderBy('start_date')
            ->get();

        // Años disponibles para el filtro
        $years = range(now()->year - 2, now()->year + 1);

        return view('vacations.index', compact('employees', 'year', 'years', 'search', 'company', 'eligibleVacations'));
    }

    public function create(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $employees = Employee::where('company_id', Auth::user()->company_id)
            ->with('user')
            ->get();

        $selectedEmployee = $employeeId ? Employee::find($employeeId) : null;

        return view('vacations.create', compact('employees', 'selectedEmployee'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'notes'       => 'nullable|string|max:500',
        ]);

        $company = Auth::user()->company;

        // Verificar que el empleado pertenezca a la empresa
        $employee = Employee::findOrFail($request->employee_id);
        if ($employee->company_id !== $company->id) {
            return back()->with('error', 'Empleado no encontrado.');
        }

        // Verificar que el empleado tenga al menos 1 año de antigüedad
        if ($employee->years_of_service < 1) {
            return back()
                ->withInput()
                ->with('error', "El empleado debe tener al menos 1 año de antigüedad para tomar vacaciones. Antigüedad actual: " . number_format($employee->years_of_service, 1) . " años.");
        }

        // Calcular días
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $daysTaken = Vacation::calculateBusinessDays($startDate, $endDate, $company->id);

        // Verificar días disponibles
        $year = $startDate->year;
        $daysRemaining = $employee->getVacationDaysRemaining($year);

        if ($daysTaken > $daysRemaining) {
            return back()
                ->withInput()
                ->with('error', "El empleado solo tiene {$daysRemaining} días disponibles en {$year}. Está intentando solicitar {$daysTaken} días.");
        }

        $vacation = Vacation::create([
            'employee_id' => $request->employee_id,
            'company_id'  => $company->id,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'days_taken'  => $daysTaken,
            'year'        => $year,
            'notes'       => $request->notes,
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'created_by'  => Auth::id(),
        ]);

        return redirect()
            ->route('vacations.index', ['year' => $year])
            ->with('success', "Vacaciones registradas exitosamente. {$daysTaken} días tomados.");
    }

    /**
     * Procesa el pago de todas las vacaciones aprobadas elegibles (inicio <= hoy + 5 días)
     * cuando la empresa tiene configurado pago independiente a la nómina.
     */
    public function payApproved(Request $request)
    {
        $company = Auth::user()->company;

        $vacations = Vacation::where('company_id', $company->id)
            ->where('status', 'approved')
            ->where('is_paid', false)
            ->where('start_date', '<=', now()->addDays(5)->endOfDay())
            ->with(['employee.user', 'employee.department_rel', 'employee.position'])
            ->get();

        if ($vacations->isEmpty()) {
            return back()->with('error', 'No hay vacaciones aprobadas pendientes de pago que inicien en los próximos 5 días.');
        }

        $paidCount = 0;
        foreach ($vacations as $vacation) {
            $this->processVacationPayment($vacation, $company);
            $paidCount++;
        }

        return redirect()->route('vacations.index')
            ->with('success', "Se procesó el pago de {$paidCount} registro(s) de vacaciones aprobadas y se enviaron los volantes a los empleados.");
    }

    /**
     * Procesa el pago individual de un registro de vacaciones
     */
    public function paySingle(Vacation $vacation)
    {
        $company = Auth::user()->company;

        if ($vacation->company_id !== $company->id) {
            abort(403);
        }

        if ($vacation->is_paid) {
            return back()->with('error', 'Esta vacación ya fue pagada.');
        }

        if ($vacation->status !== 'approved') {
            return back()->with('error', 'Solo se pueden pagar vacaciones que estén aprobadas.');
        }

        $this->processVacationPayment($vacation, $company);

        return back()->with('success', 'Pago de vacaciones procesado exitosamente y volante enviado al empleado.');
    }

    /**
     * Lógica unificada para calcular TSS, ISR y generar Payroll + Comprobante de pago de vacaciones
     */
    private function processVacationPayment(Vacation $vacation, $company)
    {
        $employee = $vacation->employee;
        $monthlySalary = (float) $employee->salary;
        $dailySalary = $monthlySalary / 23.83;
        $grossVacationPay = round($vacation->days_taken * $dailySalary, 2);

        // Descuentos de ley (TSS: SFS 3.04%, AFP 2.87%)
        $sfsVac = round($grossVacationPay * 0.0304, 2);
        $afpVac = round($grossVacationPay * 0.0287, 2);
        $tssVac = $sfsVac + $afpVac;

        // Cálculo de retención marginal de ISR (IR-3) en el mes
        $sfsRegular = $monthlySalary * 0.0304;
        $afpRegular = $monthlySalary * 0.0287;
        $tssRegular = $sfsRegular + $afpRegular;

        // Base anualizada sin vacaciones
        $baseWithout = ($monthlySalary * 12) - ($tssRegular * 12);
        if ($baseWithout < 0) $baseWithout = 0;
        $isrWithoutAnnual = $this->calculateAnnualISR($baseWithout);

        // Base anualizada con vacaciones en el mes
        $baseWith = (($monthlySalary + $grossVacationPay) * 12) - (($tssRegular + $tssVac) * 12);
        if ($baseWith < 0) $baseWith = 0;
        $isrWithAnnual = $this->calculateAnnualISR($baseWith);

        // Retención marginal de ISR para este pago
        $marginalIsr = max(0, round(($isrWithAnnual - $isrWithoutAnnual) / 12, 2));

        $totalDeductions = $sfsVac + $afpVac + $marginalIsr;
        $netSalary = $grossVacationPay - $totalDeductions;

        $period = $vacation->start_date->format('Y-m') . '-VACACIONES-' . $vacation->id;

        $payroll = Payroll::create([
            'employee_id'  => $employee->id,
            'company_id'   => $company->id,
            'period'       => $period,
            'gross_salary' => $grossVacationPay,
            'extras'       => 0,
            'descuentos'   => 0,
            'ars'          => $sfsVac,
            'afp'          => $afpVac,
            'isr'          => $marginalIsr,
            'deductions'   => $totalDeductions,
            'net_salary'   => $netSalary,
            'status'       => 'paid',
            'payment_date' => now(),
        ]);

        $vacation->update([
            'is_paid'            => true,
            'paid_at'            => now(),
            'payment_payroll_id' => $payroll->id,
            'payment_amount'     => $grossVacationPay,
            'payment_ars'        => $sfsVac,
            'payment_afp'        => $afpVac,
            'payment_isr'        => $marginalIsr,
            'payment_net'        => $netSalary,
        ]);

        // Enviar comprobante por correo
        if ($employee->user && $employee->user->email) {
            try {
                Mail::to($employee->user->email)->send(new VacationReceipt($vacation, $payroll));
            } catch (\Exception $e) {
                Log::error('Error al enviar volante de vacaciones: ' . $e->getMessage());
            }
        }

        return $payroll;
    }

    private function calculateAnnualISR(float $baseAnnual): float
    {
        if ($baseAnnual <= 416220) {
            return 0;
        } elseif ($baseAnnual < 624329) {
            return ($baseAnnual - 416220) * 0.15;
        } elseif ($baseAnnual < 867123) {
            return ($baseAnnual - 624329) * 0.20 + 31216.35;
        } else {
            return ($baseAnnual - 867123) * 0.25 + (31216.35 + 48558.80);
        }
    }

    public function show(Employee $employee)
    {
        // Verificar que el empleado pertenezca a la empresa
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $company = Auth::user()->company;

        // Vacaciones registradas directamente en el módulo de gestión de vacaciones
        $moduleVacations = collect($employee->vacations()
            ->with(['approver', 'creator', 'payroll'])
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($v) {
                return [
                    'id'          => $v->id,
                    'source'      => 'vacation_module',
                    'start_date'  => $v->start_date,
                    'end_date'    => $v->end_date,
                    'days_taken'  => $v->days_taken,
                    'year'        => $v->year,
                    'notes'       => $v->notes,
                    'status'      => $v->status,
                    'is_paid'     => $v->is_paid,
                    'paid_at'     => $v->paid_at,
                    'amount'      => $v->calculated_amount,
                    'payment_net' => $v->payment_net,
                    'is_eligible' => $v->isEligibleForPayment(),
                    'created_by'  => $v->creator->name ?? 'N/A',
                    'created_at'  => $v->created_at,
                    'approved_by' => $v->approver->name ?? 'N/A',
                    'vacation_id' => $v->id,
                    'vacation_model' => $v,
                ];
            })->values()->all());

        // Vacaciones aprobadas provenientes del módulo de permisos (requests)
        $permissionVacations = collect();
        if ($employee->user) {
            $permissionVacations = collect(UserRequest::where('user_id', $employee->user_id)
                ->where('company_id', $employee->company_id)
                ->where('type', 'vacation')
                ->where('status', 'approved')
                ->whereNotNull('start_date')
                ->whereNotNull('end_date')
                ->with(['reviewer'])
                ->orderBy('start_date', 'desc')
                ->get()
                ->map(function ($r) use ($employee) {
                    $startDate = Carbon::parse($r->start_date);
                    $endDate   = Carbon::parse($r->end_date);
                    $daysTaken = Vacation::calculateBusinessDays($startDate, $endDate, $employee->company_id);
                    $amount = round($daysTaken * ($employee->daily_salary), 2);

                    return [
                        'id'          => 'req_' . $r->id,
                        'source'      => 'request_module',
                        'start_date'  => $startDate,
                        'end_date'    => $endDate,
                        'days_taken'  => $daysTaken,
                        'year'        => $startDate->year,
                        'notes'       => $r->description,
                        'status'      => $r->status,
                        'is_paid'     => false,
                        'paid_at'     => null,
                        'amount'      => $amount,
                        'payment_net' => null,
                        'is_eligible' => false,
                        'created_by'  => $employee->user->name ?? 'N/A',
                        'created_at'  => $r->created_at,
                        'approved_by' => $r->reviewer->name ?? 'N/A',
                        'vacation_id' => null,
                        'vacation_model' => null,
                    ];
                })->values()->all());
        }

        // Unir ambas colecciones base, ordenar por fecha desc y agrupar por año
        $vacations = $moduleVacations
            ->merge($permissionVacations)
            ->sortByDesc('start_date')
            ->groupBy('year')
            ->sortKeysDesc();

        return view('vacations.show', compact('employee', 'vacations', 'company'));
    }

    public function edit(Vacation $vacation)
    {
        // Verificar que la vacación pertenezca a la empresa
        if ($vacation->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $employees = Employee::where('company_id', Auth::user()->company_id)
            ->with('user')
            ->get();

        return view('vacations.edit', compact('vacation', 'employees'));
    }

    public function update(Request $request, Vacation $vacation)
    {
        // Verificar que la vacación pertenezca a la empresa
        if ($vacation->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'notes'      => 'nullable|string|max:500',
        ]);

        // Calcular nuevos días
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $newDaysTaken = Vacation::calculateBusinessDays($startDate, $endDate, Auth::user()->company_id);
        $year = $startDate->year;

        // Calcular días disponibles (sin contar esta vacación)
        $employee = $vacation->employee;
        $totalTaken = $employee->getVacationDaysTaken($year) - $vacation->days_taken;
        $daysRemaining = $employee->vacation_days_entitled - $totalTaken;

        if ($newDaysTaken > $daysRemaining) {
            return back()
                ->withInput()
                ->with('error', "El empleado solo tiene {$daysRemaining} días disponibles. Está intentando solicitar {$newDaysTaken} días.");
        }

        $vacation->update([
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'notes'      => $request->notes,
        ]);

        return redirect()
            ->route('vacations.index', ['year' => $year])
            ->with('success', 'Vacaciones actualizadas exitosamente.');
    }

    public function destroy(Vacation $vacation)
    {
        // Verificar que la vacación pertenezca a la empresa
        if ($vacation->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $year = $vacation->year;
        $vacation->delete();

        return redirect()
            ->route('vacations.index', ['year' => $year])
            ->with('success', 'Vacaciones eliminadas exitosamente.');
    }
}
