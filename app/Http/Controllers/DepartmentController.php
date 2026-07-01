<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::where('company_id', Auth::user()->company_id)
            ->with('parentDepartment', 'childDepartments');

        // Filtro por búsqueda de nombre
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Filtro por departamento padre
        if ($request->filled('parent_id')) {
            if ($request->parent_id === 'null') {
                $query->whereNull('parent_department_id');
            } else {
                $query->where('parent_department_id', $request->parent_id);
            }
        }

        $departments = $query->orderBy('name')->get();

        // Obtener todos los departamentos para el filtro de padre
        $allDepartments = Department::where('company_id', Auth::user()->company_id)
            ->orderBy('name')
            ->get();

        return view('departments.index', compact('departments', 'allDepartments'));
    }

    public function create()
    {
        $parentDepartments = Department::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('departments.create', compact('parentDepartments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Validate parent department belongs to same company
        if ($request->parent_department_id) {
            $parent = Department::find($request->parent_department_id);
            if ($parent->company_id !== Auth::user()->company_id) {
                abort(403);
            }
        }

        Department::create([
            'company_id' => Auth::user()->company_id,
            'parent_department_id' => $request->parent_department_id,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('departments.index')->with('success', 'Departamento creado exitosamente.');
    }

    public function edit(Department $department)
    {
        if ($department->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $parentDepartments = Department::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->where('id', '!=', $department->id)
            ->orderBy('name')
            ->get();
        return view('departments.edit', compact('department', 'parentDepartments'));
    }

    public function update(Request $request, Department $department)
    {
        if ($department->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'parent_department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Validate parent department belongs to same company and is not self
        if ($request->parent_department_id) {
            $parent = Department::find($request->parent_department_id);
            if ($parent->company_id !== Auth::user()->company_id || $parent->id === $department->id) {
                abort(403);
            }
        }

        $department->update([
            'parent_department_id' => $request->parent_department_id,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('departments.index')->with('success', 'Departamento actualizado exitosamente.');
    }

    public function destroy(Department $department)
    {
        if ($department->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        // Check if department has employees
        if ($department->employees()->exists()) {
            return redirect()->route('departments.index')->with('error', 'No se puede eliminar el departamento porque tiene empleados asignados.');
        }

        // Check if department has child departments
        if ($department->childDepartments()->exists()) {
            return redirect()->route('departments.index')->with('error', 'No se puede eliminar el departamento porque tiene departamentos dependientes.');
        }

        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Departamento eliminado exitosamente.');
    }
}
