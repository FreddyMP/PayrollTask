<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = \App\Models\Device::where('company_id', auth()->user()->company_id)->latest()->paginate(10);
        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        return view('devices.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
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
