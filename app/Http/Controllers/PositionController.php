<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::where('company_id', Auth::user()->company_id)
            ->with('department')
            ->orderBy('title')
            ->get();
        return view('positions.index', compact('positions'));
    }

    public function create()
    {
        $departments = Department::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('positions.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'nullable|string|max:50',
            'base_salary' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Validate department belongs to same company
        $department = Department::find($request->department_id);
        if ($department->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        Position::create([
            'company_id' => Auth::user()->company_id,
            'department_id' => $request->department_id,
            'title' => $request->title,
            'description' => $request->description,
            'code' => $request->code,
            'base_salary' => $request->base_salary,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('positions.index')->with('success', 'Posición creada exitosamente.');
    }

    public function edit(Position $position)
    {
        if ($position->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $departments = Department::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('positions.edit', compact('position', 'departments'));
    }

    public function update(Request $request, Position $position)
    {
        if ($position->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'nullable|string|max:50',
            'base_salary' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Validate department belongs to same company
        $department = Department::find($request->department_id);
        if ($department->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $position->update([
            'department_id' => $request->department_id,
            'title' => $request->title,
            'description' => $request->description,
            'code' => $request->code,
            'base_salary' => $request->base_salary,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('positions.index')->with('success', 'Posición actualizada exitosamente.');
    }

    public function destroy(Position $position)
    {
        if ($position->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        // Check if position has employees
        if ($position->employees()->exists()) {
            return redirect()->route('positions.index')->with('error', 'No se puede eliminar la posición porque tiene empleados asignados.');
        }

        $position->delete();
        return redirect()->route('positions.index')->with('success', 'Posición eliminada exitosamente.');
    }
}
