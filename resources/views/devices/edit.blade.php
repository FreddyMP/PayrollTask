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
