@extends('layouts.app')
@section('title', 'Nueva Tarea')
@section('page-title', 'Nueva Tarea')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-plus-circle me-2"></i>Crear Tarea</div>
            <div class="card-body">
                <form method="POST" action="{{ route('tasks.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adjuntar Imágenes o Videos</label>
                        <input type="file" class="form-control" name="attachments[]" accept="image/*,video/*" multiple>
                        <div class="form-text text-muted small">Puedes seleccionar varios archivos a la vez (Máx 30MB cada uno).</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="description" rows="4">{{ old('description') }}</textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Proyecto <span class="text-danger">*</span></label>
                            <select class="form-select" name="project_id" required>
                                <option value="">Seleccione un proyecto</option>
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Prioridad</label>
                            <select class="form-select" name="priority">
                                <option value="low">Baja</option>
                                <option value="medium" selected>Media</option>
                                <option value="high">Alta</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Asignar a</label>
                            <select class="form-select" name="assigned_to">
                                <option value="">Sin asignar</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha límite</label>
                            <input type="date" class="form-control" name="due_date" value="{{ old('due_date') }}">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> Crear Tarea</button>
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
