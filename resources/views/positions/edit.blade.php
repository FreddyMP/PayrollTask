@extends('layouts.app')
@section('title', 'Editar Posición')
@section('page-title', 'Editar Posición')

@section('content')
<ul class="nav nav-tabs mb-4 px-3" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('departments.index') }}" style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
            Departamentos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('positions.index') }}" style="color: white; border-bottom: 2px solid var(--primary); background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
            Posiciones
        </a>
    </li>
</ul>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark-2 py-3 border-0">
                <i class="bi bi-pencil me-2"></i>
                <span class="text-white fw-bold">Editar Posición</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('positions.update', $position) }}">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Título del Puesto</label>
                        <input type="text" class="form-control" name="title" value="{{ old('title', $position->title) }}" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Departamento</label>
                        <select class="form-select" name="department_id" required>
                            <option value="">Seleccionar...</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ $position->department_id == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Código (Opcional)</label>
                        <input type="text" class="form-control" name="code" value="{{ old('code', $position->code) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Salario Base (Opcional)</label>
                        <input type="number" step="0.01" class="form-control" name="base_salary" value="{{ old('base_salary', $position->base_salary) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="description" rows="3">{{ old('description', $position->description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ $position->is_active ? 'checked' : '' }}>
                            <label class="form-check-label text-white" for="isActive">
                                Posición Activa
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-check-lg me-1"></i> Actualizar
                        </button>
                        <a href="{{ route('positions.index') }}" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
