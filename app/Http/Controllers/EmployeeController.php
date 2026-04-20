<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = Employee::where('company_id', $companyId)->with('user');

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $employees = $query->latest()->paginate(15);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,supervisor,usuario',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'hire_date' => 'nullable|date',
            'contract_type' => 'nullable|in:full_time,part_time,contractor',
            'bank_account' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:20',
            'work_start' => 'nullable|date_format:H:i',
            'work_end' => 'nullable|date_format:H:i',
            'ars_extras' => 'nullable|array',
            'ars_extras.*.name' => 'required|string|max:255',
            'ars_extras.*.id_number' => 'nullable|string|max:20',
            'ars_extras.*.relationship' => 'nullable|string|max:255',
            'ars_extras.*.birth_date' => 'nullable|date',
            'ars_extras.*.sex' => 'nullable|string|max:20',
            'ars_extras.*.phone' => 'nullable|string|max:50',
            'ars_extras.*.address' => 'nullable|string',
            'ars_extras.*.ars_amount' => 'required|numeric|min:0',
        ]);

        $companyId = Auth::user()->company_id;

        $user = User::create([
            'company_id' => $companyId,
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'position' => $data['position'] ?? null,
            'status' => 'active',
        ]);

        $employee = Employee::create([
            'user_id' => $user->id,
            'company_id' => $companyId,
            'department' => $data['department'] ?? null,
            'salary' => $data['salary'] ?? 0,
            'hire_date' => $data['hire_date'] ?? now(),
            'contract_type' => $data['contract_type'] ?? 'full_time',
            'bank_account' => $data['bank_account'] ?? null,
            'id_number' => $data['id_number'] ?? null,
            'work_start' => $data['work_start'] ?? '08:00',
            'work_end' => $data['work_end'] ?? '17:00',
        ]);

        if (!empty($data['ars_extras'])) {
            foreach ($data['ars_extras'] as $extra) {
                $employee->arsExtras()->create($extra);
            }
        }

        return redirect()->route('employees.index')->with('success', 'Empleado creado exitosamente.');
    }

    public function show(Employee $employee)
    {
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $employee->load(['user', 'payrolls']);
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $employee->load('user');
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'role' => 'required|in:admin,supervisor,usuario',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'hire_date' => 'nullable|date',
            'contract_type' => 'nullable|in:full_time,part_time,contractor',
            'bank_account' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:20',
            'work_start' => 'nullable|date_format:H:i',
            'work_end' => 'nullable|date_format:H:i',
            'status' => 'nullable|in:active,inactive',
            'ars_extras' => 'nullable|array',
            'ars_extras.*.name' => 'required|string|max:255',
            'ars_extras.*.id_number' => 'nullable|string|max:20',
            'ars_extras.*.relationship' => 'nullable|string|max:255',
            'ars_extras.*.birth_date' => 'nullable|date',
            'ars_extras.*.sex' => 'nullable|string|max:20',
            'ars_extras.*.phone' => 'nullable|string|max:50',
            'ars_extras.*.address' => 'nullable|string',
            'ars_extras.*.ars_amount' => 'required|numeric|min:0',
        ]);

        $employee->user->update([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'position' => $data['position'] ?? null,
            'status' => $data['status'] ?? 'active',
        ]);

        $employee->update([
            'department' => $data['department'] ?? null,
            'salary' => $data['salary'] ?? 0,
            'hire_date' => $data['hire_date'] ?? null,
            'contract_type' => $data['contract_type'] ?? 'full_time',
            'bank_account' => $data['bank_account'] ?? null,
            'id_number' => $data['id_number'] ?? null,
            'work_start' => $data['work_start'] ?? '08:00',
            'work_end' => $data['work_end'] ?? '17:00',
        ]);

        $employee->arsExtras()->delete();
        if (!empty($data['ars_extras'])) {
            foreach ($data['ars_extras'] as $extra) {
                $employee->arsExtras()->create($extra);
            }
        }

        return redirect()->route('employees.index')->with('success', 'Empleado actualizado exitosamente.');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $employee->user->update(['status' => 'inactive']);
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Empleado eliminado exitosamente.');
    }
}
