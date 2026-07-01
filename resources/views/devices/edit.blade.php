@extends('layouts.app')
@section('title', 'Editar Dispositivo')
@section('page-title', 'Editar Dispositivo')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-pencil me-2"></i>Editar Dispositivo</div>
            <div class="card-body">
                <form method="POST" action="{{ route('devices.update', $device) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nombre del Dispositivo</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $device->name) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" class="form-control" name="brand" value="{{ old('brand', $device->brand) }}" placeholder="Ej: Dell, HP, Samsung">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" name="type" required>
                                <option value="laptop" {{ old('type', $device->type) == 'laptop' ? 'selected' : '' }}>Laptop</option>
                                <option value="desktop" {{ old('type', $device->type) == 'desktop' ? 'selected' : '' }}>Desktop</option>
                                <option value="tablet" {{ old('type', $device->type) == 'tablet' ? 'selected' : '' }}>Tablet</option>
                                <option value="phone" {{ old('type', $device->type) == 'phone' ? 'selected' : '' }}>Phone</option>
                                <option value="otros" {{ old('type', $device->type) == 'otros' ? 'selected' : '' }}>Otros</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="status" required>
                                <option value="activo" {{ old('status', $device->status) == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('status', $device->status) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                <option value="mantenimiento" {{ old('status', $device->status) == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Asignar a Empleado (Opcional)</label>
                            <select class="form-select" name="employee_id">
                                <option value="">Sin asignar</option>
                                @foreach(\App\Models\Employee::where('company_id', auth()->user()->company_id)->with('user')->get()->sortBy(fn($e) => $e->user->name ?? '') as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id', $device->employee_id) == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->user->name ?? '—' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección IP</label>
                        <input type="text" class="form-control" name="ip_address" value="{{ old('ip_address', $device->ip_address) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción (Opcional)</label>
                        <textarea class="form-control" name="description" rows="3">{{ old('description', $device->description) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> Guardar Cambios</button>
                        <a href="{{ route('devices.index') }}" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
