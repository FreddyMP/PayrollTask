@extends('layouts.app')
@section('title', 'Editar Proyecto')
@section('page-title', 'Editar Proyecto: ' . $project->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10">
        <div class="card bg-dark-2 border-0 shadow-lg">
            <div class="card-header border-bottom border-light bg-transparent p-4">
                <h5 class="mb-0 text-white">Editar Información del Proyecto</h5>
                <p class="text-muted small mb-0">Actualice los detalles y el equipo del proyecto.</p>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('projects.update', $project) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Nombre del Proyecto</label>
                            <input type="text" name="name" class="form-control" placeholder="Ej: Rediseño de Sitio Web" required value="{{ old('name', $project->name) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Descripción</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Breve descripción del objetivo y alcance...">{{ old('description', $project->description) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Fecha de Inicio</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Fecha de Fin</label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $project->end_date ? $project->end_date->format('Y-m-d') : '') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Estado</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>En Pausa</option>
                                <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completado</option>
                                <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <hr class="my-4 border-light opacity-10">
                            <h6 class="text-white mb-3">Equipo del Proyecto</h6>
                            <p class="text-muted small mb-3">Actualice los miembros asignados a este proyecto.</p>
                            
                            <div class="row g-2" style="max-height: 250px; overflow-y: auto;">
                                @foreach($users as $user)
                                <div class="col-md-6">
                                    <div class="form-check p-2 rounded-3 {{ in_array($user->id, $projectTeamIds) ? 'active-member' : '' }}" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="team[]" value="{{ $user->id }}" id="user-{{ $user->id }}" {{ in_array($user->id, $projectTeamIds) ? 'checked' : '' }}>
                                        <label class="form-check-label d-flex align-items-center" for="user-{{ $user->id }}">
                                            <div class="avatar-group me-2">
                                                <div class="avatar-xs" style="width: 24px; height: 24px; border-radius: 6px; background: var(--gradient-2); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.6rem;">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-white small fw-bold">{{ $user->name }}</div>
                                                <div class="text-muted" style="font-size: 0.65rem;">{{ ucfirst($user->role) }}</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12 mt-5">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary-custom px-4">Guardar Cambios</button>
                                <a href="{{ route('projects.index') }}" class="btn btn-outline-custom">Cancelar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
