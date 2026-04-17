@extends('layouts.app')
@section('title', 'Editar Tarea')
@section('page-title', 'Editar Tarea')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-pencil me-2"></i>Editar Tarea</div>
            <div class="card-body">
                <form method="POST" action="{{ route('tasks.update', $task) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" name="title" value="{{ old('title', $task->title) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adjuntar Más Imágenes o Videos</label>
                        <input type="file" class="form-control" name="attachments[]" accept="image/*,video/*" multiple>
                        <div class="form-text text-muted small">Puedes seleccionar varios archivos a la vez (Máx 30MB cada uno).</div>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase" style="color: #94a3b8;">Archivos de Supervisión</label>
                            <div class="p-2 rounded rounded-3 border border-light border-opacity-10" style="background: rgba(255,255,255,0.02); min-height: 50px;">
                                @forelse($task->attachments->where('uploader_group', 'supervisor') as $att)
                                <div class="d-flex align-items-center justify-content-between mb-2 p-2 rounded bg-dark-3">
                                    <div class="text-truncate flex-grow-1 small">
                                        <i class="bi {{ $att->file_type == 'video' ? 'bi-play-circle' : 'bi-image' }} me-2"></i>
                                        <a href="{{ Storage::url($att->file_path) }}" target="_blank" class="text-white opacity-75 text-decoration-none">Ver archivo</a>
                                        <span class="ms-2 text-muted" style="font-size: 0.65rem;">— {{ $att->user->name }}</span>
                                    </div>
                                    @if(Auth::user()->isSupervisor() || Auth::id() === $att->user_id)
                                    <form method="POST" action="{{ route('tasks.attachments.destroy', $att) }}" class="ms-2">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('¿Eliminar archivo?')"><i class="bi bi-x-lg"></i></button>
                                    </form>
                                    @endif
                                </div>
                                @empty
                                <div class="text-muted small p-2 text-center opacity-50">Sin archivos</div>
                                @endforelse
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase" style="color: #94a3b8;">Archivos del Usuario Asignado</label>
                            <div class="p-2 rounded rounded-3 border border-light border-opacity-10" style="background: rgba(255,255,255,0.02); min-height: 50px;">
                                @forelse($task->attachments->where('uploader_group', 'assigned') as $att)
                                <div class="d-flex align-items-center justify-content-between mb-2 p-2 rounded bg-dark-3">
                                    <div class="text-truncate flex-grow-1 small">
                                        <i class="bi {{ $att->file_type == 'video' ? 'bi-play-circle' : 'bi-image' }} me-2"></i>
                                        <a href="{{ Storage::url($att->file_path) }}" target="_blank" class="text-white opacity-75 text-decoration-none">Ver archivo</a>
                                        <span class="ms-2 text-muted" style="font-size: 0.65rem;">— {{ $att->user->name }}</span>
                                    </div>
                                    @if(Auth::user()->isSupervisor() || Auth::id() === $att->user_id)
                                    <form method="POST" action="{{ route('tasks.attachments.destroy', $att) }}" class="ms-2">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0" onclick="return confirm('¿Eliminar archivo?')"><i class="bi bi-x-lg"></i></button>
                                    </form>
                                    @endif
                                </div>
                                @empty
                                <div class="text-muted small p-2 text-center opacity-50">Sin archivos</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="description" rows="4">{{ old('description', $task->description) }}</textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Proyecto <span class="text-danger">*</span></label>
                            <select class="form-select" name="project_id" required>
                                <option value="">Seleccione un proyecto</option>
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ $task->project_id == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="status">
                                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                                <option value="review" {{ $task->status == 'review' ? 'selected' : '' }}>Revisión</option>
                                <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completada</option>
                                <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Prioridad</label>
                            <select class="form-select" name="priority">
                                <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Baja</option>
                                <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Media</option>
                                <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>Alta</option>
                                <option value="urgent" {{ $task->priority == 'urgent' ? 'selected' : '' }}>Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Asignar a</label>
                            <select class="form-select" name="assigned_to">
                                <option value="">Sin asignar</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $task->assigned_to == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha límite</label>
                            <input type="date" class="form-control" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> Guardar Cambios</button>
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
