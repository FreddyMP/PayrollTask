<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Position;
use App\Models\Department;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = Employee::where('company_id', $companyId)->with(['user', 'position']);

        // Filtro de búsqueda por nombre/email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por departamento
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        // Filtro por rol
        if ($request->filled('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        // Filtro por tipo de contrato
        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        $employees = $query->latest()->paginate(15)->withQueryString();

        // Obtener departamentos para el dropdown
        $departments = Department::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $companyId = Auth::user()->company_id;
        $positions = Position::where('company_id', $companyId)->where('is_active', true)->with('department')->orderBy('title')->get();
        return view('employees.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $messages = [
            'email.unique' => 'Ese correo no está disponible.',
        ];

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                \Illuminate\Validation\Rule::unique('users', 'email')->where(function ($query) {
                    return $query->where('status', 'active');
                })
            ],
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,supervisor,usuario',
            'phone' => 'nullable|string|max:20',
            'position_id' => 'nullable|exists:positions,id',
            'salary' => 'nullable|numeric|min:0',
            'hire_date' => 'nullable|date',
            'contract_type' => 'nullable|in:full_time,part_time,contractor',
            'bank_account' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:20',
            'work_start' => 'nullable|date_format:H:i',
            'work_end' => 'nullable|date_format:H:i',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i',
            'ars_extras' => 'nullable|array',
            'ars_extras.*.name' => 'required|string|max:255',
            'ars_extras.*.id_number' => 'nullable|string|max:20',
            'ars_extras.*.relationship' => 'nullable|string|max:255',
            'ars_extras.*.birth_date' => 'nullable|date',
            'ars_extras.*.sex' => 'nullable|string|max:20',
            'ars_extras.*.phone' => 'nullable|string|max:50',
            'ars_extras.*.address' => 'nullable|string',
            'ars_extras.*.ars_amount' => 'required|numeric|min:0',
            'documents' => 'nullable|array',
            'documents.*.name' => 'required|string|max:255',
            'documents.*.file' => 'nullable|file|max:10240',
        ], $messages);

        $companyId = Auth::user()->company_id;

        if (!empty($data['position_id'])) {
            $position = Position::with('department')->find($data['position_id']);
            if (!$position || $position->company_id !== $companyId) {
                abort(403);
            }
        }

        $positionTitle = isset($position) ? $position->title : null;
        $departmentId = isset($position) ? $position->department_id : null;
        $departmentName = isset($position) && $position->department ? $position->department->name : null;

        $user = User::create([
            'company_id' => $companyId,
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'position' => $positionTitle,
            'status' => 'active',
        ]);

        $employee = Employee::create([
            'user_id' => $user->id,
            'company_id' => $companyId,
            'position_id' => $data['position_id'] ?? null,
            'department_id' => $departmentId,
            'department' => $departmentName,
            'salary' => $data['salary'] ?? 0,
            'hire_date' => $data['hire_date'] ?? now(),
            'contract_type' => $data['contract_type'] ?? 'full_time',
            'bank_account' => $data['bank_account'] ?? null,
            'id_number' => $data['id_number'] ?? null,
            'work_start' => $data['work_start'] ?? '08:00',
            'work_end' => $data['work_end'] ?? '17:00',
            'break_start' => $data['break_start'] ?? null,
            'break_end' => $data['break_end'] ?? null,
        ]);

        if (!empty($data['ars_extras'])) {
            foreach ($data['ars_extras'] as $extra) {
                $employee->arsExtras()->create($extra);
            }
        }

        $uploadedDocuments = $request->file('documents');
        if (is_array($uploadedDocuments)) {
            foreach ($uploadedDocuments as $index => $docData) {
                if (isset($docData['file'])) {
                    $path = $docData['file']->store('employee_documents', config('filesystems.default'));
                    $employee->documents()->create([
                        'company_id' => $companyId,
                        'name' => $request->input("documents.$index.name"),
                        'file_path' => $path,
                    ]);
                }
            }
        }

        return redirect()->route('employees.index')->with('success', 'Empleado creado exitosamente.');
    }

    public function show(Employee $employee)
    {
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $employee->load(['user', 'payrolls', 'position']);
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $employee->load(['user', 'position']);
        $companyId = Auth::user()->company_id;
        $positions = Position::where('company_id', $companyId)->where('is_active', true)->with('department')->orderBy('title')->get();
        return view('employees.edit', compact('employee', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $messages = [
            'email.unique' => 'Ese correo no está disponible.',
        ];

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                \Illuminate\Validation\Rule::unique('users', 'email')->where(function ($query) {
                    return $query->where('status', 'active');
                })->ignore($employee->user_id)
            ],
            'role' => 'required|in:admin,supervisor,usuario',
            'phone' => 'nullable|string|max:20',
            'position_id' => 'nullable|exists:positions,id',
            'salary' => 'nullable|numeric|min:0',
            'hire_date' => 'nullable|date',
            'contract_type' => 'nullable|in:full_time,part_time,contractor',
            'bank_account' => 'nullable|string|max:255',
            'id_number' => 'nullable|string|max:20',
            'work_start' => 'nullable|date_format:H:i',
            'work_end' => 'nullable|date_format:H:i',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i',
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
            'documents' => 'nullable|array',
            'documents.*.name' => 'required|string|max:255',
            'documents.*.file' => 'nullable|file|max:10240',
        ], $messages);

        if (!empty($data['position_id'])) {
            $position = Position::with('department')->find($data['position_id']);
            if (!$position || $position->company_id !== $employee->company_id) {
                abort(403);
            }
        }

        $positionTitle = isset($position) ? $position->title : null;
        $departmentId = isset($position) ? $position->department_id : null;
        $departmentName = isset($position) && $position->department ? $position->department->name : null;

        $employee->user->update([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'position' => $positionTitle,
            'status' => $data['status'] ?? 'active',
        ]);

        $employee->update([
            'position_id' => $data['position_id'] ?? null,
            'department_id' => $departmentId,
            'department' => $departmentName,
            'salary' => $data['salary'] ?? 0,
            'hire_date' => $data['hire_date'] ?? null,
            'contract_type' => $data['contract_type'] ?? 'full_time',
            'bank_account' => $data['bank_account'] ?? null,
            'id_number' => $data['id_number'] ?? null,
            'work_start' => $data['work_start'] ?? '08:00',
            'work_end' => $data['work_end'] ?? '17:00',
            'break_start' => $data['break_start'] ?? null,
            'break_end' => $data['break_end'] ?? null,
        ]);

        $employee->arsExtras()->delete();
        if (!empty($data['ars_extras'])) {
            foreach ($data['ars_extras'] as $extra) {
                $employee->arsExtras()->create($extra);
            }
        }

        $uploadedDocuments = $request->file('documents');
        if (is_array($uploadedDocuments)) {
            foreach ($uploadedDocuments as $index => $docData) {
                if (isset($docData['file'])) {
                    $path = $docData['file']->store('employee_documents', config('filesystems.default'));
                    $employee->documents()->create([
                        'company_id' => $employee->company_id,
                        'name' => $request->input("documents.$index.name"),
                        'file_path' => $path,
                    ]);
                }
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

    public function destroyDocument(EmployeeDocument $document)
    {
        if ($document->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        Storage::disk(config('filesystems.default'))->delete($document->file_path);
        $document->delete();

        return redirect()->back()->with('success', 'Documento eliminado correctamente.');
    }
}
