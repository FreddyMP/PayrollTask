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
