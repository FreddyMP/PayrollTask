@extends('layouts.app')
@section('title', 'Nuevo Departamento')
@section('page-title', 'Nuevo Departamento')

@section('content')
<ul class="nav nav-tabs mb-4 px-3" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('departments.index') }}" style="color: white; border-bottom: 2px solid var(--primary); background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
            Departamentos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('positions.index') }}" style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
            Posiciones
        </a>
    </li>
</ul>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark-2 py-3 border-0">
                <i class="bi bi-diagram-3 me-2"></i>
                <span class="text-white fw-bold">Crear Departamento</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('departments.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nombre del Departamento</label>
                        <input type="text" class="form-control" name="name" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Departamento Padre (Opcional)</label>
                        <select class="form-select" name="parent_department_id">
                            <option value="">Sin departamento padre (Departamento principal)</option>
                            @foreach($parentDepartments as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Si selecciona un departamento padre, este será un subdepartamento del mismo.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                            <label class="form-check-label text-white" for="isActive">
                                Departamento Activo
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-check-lg me-1"></i> Guardar
                        </button>
                        <a href="{{ route('departments.index') }}" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
