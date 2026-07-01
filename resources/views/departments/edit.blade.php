@extends('layouts.app')
@section('title', 'Editar Departamento')
@section('page-title', 'Editar Departamento')

@section('content')
@include('departments.partials.tabs', ['activeTab' => 'departments'])

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark-2 py-3 border-0">
                <i class="bi bi-pencil me-2"></i>
                <span class="text-white fw-bold">Editar Departamento</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('departments.update', $department) }}">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Nombre del Departamento</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $department->name) }}" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Departamento Padre (Opcional)</label>
                        <select class="form-select" name="parent_department_id">
                            <option value="">Sin departamento padre (Departamento principal)</option>
                            @foreach($parentDepartments as $parent)
                            <option value="{{ $parent->id }}" {{ $department->parent_department_id == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Si selecciona un departamento padre, este será un subdepartamento del mismo.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="description" rows="3">{{ old('description', $department->description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ $department->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">
                                Departamento Activo
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-check-lg me-1"></i> Actualizar
                        </button>
                        <a href="{{ route('departments.index') }}" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
