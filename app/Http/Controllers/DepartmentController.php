<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::where('company_id', Auth::user()->company_id)
            ->with('parentDepartment', 'childDepartments')
            ->orderBy('name')
            ->get();
        return view('departments.index', compact('departments'));
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
