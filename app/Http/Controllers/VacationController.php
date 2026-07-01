<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Vacation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VacationController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $search = $request->get('search');

        // Obtener todos los empleados de la empresa con sus relaciones
        $employees = Employee::where('company_id', Auth::user()->company_id)
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
                return [
                    'id' => $employee->id,
                    'name' => $employee->user->name ?? 'N/A',
                    'email' => $employee->user->email ?? 'N/A',
                    'department' => $employee->department_rel->name ?? $employee->department ?? 'N/A',
                    'position' => $employee->position->name ?? 'N/A',
                    'hire_date' => $employee->hire_date?->format('d/m/Y') ?? 'N/A',
                    'years_of_service' => $employee->years_of_service,
                    'days_entitled' => $employee->vacation_days_entitled,
                    'days_taken' => $employee->getVacationDaysTaken($year),
                    'days_remaining' => $employee->getVacationDaysRemaining($year),
                    'vacations' => $employee->vacations,
                    'can_take_vacation' => $employee->years_of_service >= 1,
                ];
            });

        // Años disponibles para el filtro
        $years = range(now()->year - 2, now()->year + 1);

        return view('vacations.index', compact('employees', 'year', 'years', 'search'));
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
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verificar que el empleado pertenezca a la empresa
        $employee = Employee::findOrFail($request->employee_id);
        if ($employee->company_id !== Auth::user()->company_id) {
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
        $daysTaken = Vacation::calculateBusinessDays($startDate, $endDate, Auth::user()->company_id);

        // Verificar días disponibles
        $year = $startDate->year;
        $daysRemaining = $employee->getVacationDaysRemaining($year);

        if ($daysTaken > $daysRemaining) {
            return back()
                ->withInput()
                ->with('error', "El empleado solo tiene {$daysRemaining} días disponibles en {$year}. Está intentando solicitar {$daysTaken} días.");
        }

        Vacation::create([
            'employee_id' => $request->employee_id,
            'company_id' => Auth::user()->company_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_taken' => $daysTaken,
            'year' => $year,
            'notes' => $request->notes,
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('vacations.index', ['year' => $year])
            ->with('success', "Vacaciones registradas exitosamente. {$daysTaken} días tomados.");
    }

    public function show(Employee $employee)
    {
        // Verificar que el empleado pertenezca a la empresa
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $vacations = $employee->vacations()
            ->with(['approver', 'creator'])
            ->orderBy('start_date', 'desc')
            ->get()
            ->groupBy('year');

        return view('vacations.show', compact('employee', 'vacations'));
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
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:500',
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
            'end_date' => $request->end_date,
            'notes' => $request->notes,
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
