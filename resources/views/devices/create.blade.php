@extends('layouts.app')
@section('title', 'Nuevo Dispositivo')
@section('page-title', 'Registrar Dispositivo')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-plus-circle me-2"></i>Nuevo Dispositivo</div>
            <div class="card-body">
                <form method="POST" action="{{ route('devices.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nombre del Dispositivo</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Ej: PC Recepción, Android Juan" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" class="form-control" name="brand" value="{{ old('brand') }}" placeholder="Ej: Dell, HP, Samsung">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" name="type" required>
                                <option value="laptop" {{ old('type') == 'laptop' ? 'selected' : '' }}>Laptop</option>
                                <option value="desktop" {{ old('type') == 'desktop' ? 'selected' : '' }}>Desktop</option>
                                <option value="tablet" {{ old('type') == 'tablet' ? 'selected' : '' }}>Tablet</option>
                                <option value="phone" {{ old('type') == 'phone' ? 'selected' : '' }}>Phone</option>
                                <option value="otros" {{ old('type', 'otros') == 'otros' ? 'selected' : '' }}>Otros</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="status" required>
                                <option value="activo" {{ old('status', 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('status') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                <option value="mantenimiento" {{ old('status') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Asignar a Empleado (Opcional)</label>
                            <select class="form-select" name="employee_id">
                                <option value="">Sin asignar</option>
                                @foreach(\App\Models\Employee::where('company_id', auth()->user()->company_id)->with('user')->get()->sortBy(fn($e) => $e->user->name ?? '') as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->user->name ?? '—' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dirección IP</label>
                        <input type="text" class="form-control" name="ip_address" value="{{ old('ip_address') }}" placeholder="Ej: 192.168.1.10" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción (Opcional)</label>
                        <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> Registrar Dispositivo</button>
                        <a href="{{ route('devices.index') }}" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
