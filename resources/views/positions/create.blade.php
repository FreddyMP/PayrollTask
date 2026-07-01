@extends('layouts.app')
@section('title', 'Nueva Posición')
@section('page-title', 'Nueva Posición')

@section('content')
@include('departments.partials.tabs', ['activeTab' => 'positions'])

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark-2 py-3 border-0">
                <i class="bi bi-person-badge me-2"></i>
                <span class="text-white fw-bold">Crear Posición</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('positions.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Título del Puesto</label>
                        <input type="text" class="form-control" name="title" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Departamento</label>
                        <select class="form-select" name="department_id" required>
                            <option value="">Seleccionar...</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Código (Opcional)</label>
                        <input type="text" class="form-control" name="code">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Salario Base (Opcional)</label>
                        <input type="number" step="0.01" class="form-control" name="base_salary">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                            <label class="form-check-label text-white" for="isActive">
                                Posición Activa
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-check-lg me-1"></i> Guardar
                        </button>
                        <a href="{{ route('positions.index') }}" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
