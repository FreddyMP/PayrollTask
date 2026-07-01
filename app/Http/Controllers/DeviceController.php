<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Device::where('company_id', auth()->user()->company_id)
            ->with('employee');

        // Búsqueda por nombre o marca
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por asignación (asignado/no asignado)
        if ($request->filled('assignment')) {
            if ($request->assignment === 'assigned') {
                $query->whereNotNull('employee_id');
            } elseif ($request->assignment === 'unassigned') {
                $query->whereNull('employee_id');
            }
        }

        // Filtro por empleado específico
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $devices = $query->latest()->paginate(10)->withQueryString();

        // Obtener lista de empleados para el select
        $employees = \App\Models\Employee::where('company_id', auth()->user()->company_id)
            ->with('user')
            ->get()
            ->sortBy(fn($e) => $e->user->name ?? '');


        return view('devices.index', compact('devices', 'employees'));
    }

    public function create()
    {
        return view('devices.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'type' => 'required|in:laptop,desktop,tablet,phone,otros',
            'status' => 'required|in:activo,inactivo,mantenimiento',
            'employee_id' => 'nullable|exists:employees,id',
            'ip_address' => 'required|string|max:45',
            'description' => 'nullable|string',
        ]);

        $data['company_id'] = auth()->user()->company_id;

        \App\Models\Device::create($data);

        return redirect()->route('devices.index')->with('success', 'Dispositivo registrado exitosamente.');
    }

    public function edit(\App\Models\Device $device)
    {
        if ($device->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        return view('devices.edit', compact('device'));
    }

    public function update(Request $request, \App\Models\Device $device)
    {
        if ($device->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'type' => 'required|in:laptop,desktop,tablet,phone,otros',
            'status' => 'required|in:activo,inactivo,mantenimiento',
            'employee_id' => 'nullable|exists:employees,id',
            'ip_address' => 'required|string|max:45',
            'description' => 'nullable|string',
        ]);

        $device->update($data);

        return redirect()->route('devices.index')->with('success', 'Dispositivo actualizado exitosamente.');
    }

    public function destroy(\App\Models\Device $device)
    {
        if ($device->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $device->delete();

        return redirect()->route('devices.index')->with('success', 'Dispositivo eliminado exitosamente.');
    }
}
