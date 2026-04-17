@extends('layouts.app')
@section('title', 'Detalle del Proyecto')
@section('page-title', 'Proyecto: ' . $project->name)

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card bg-dark-2 border-0 mb-4 h-100">
            <div class="card-header border-bottom border-light bg-transparent p-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">Información General</h5>
                <span class="badge-status badge-{{ $project->status }}">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
            </div>
            <div class="card-body p-4">
                <p class="text-muted mb-4" style="line-height: 1.8; white-space: pre-line;">
                    {{ $project->description ?? 'Sin descripción adicional para este proyecto.' }}
                </p>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                            <div class="text-muted small mb-1">Fecha Inicio</div>
                            <div class="text-white fw-bold"><i class="bi bi-calendar-check me-2 text-primary"></i>{{ $project->start_date ? $project->start_date->format('d M, Y') : '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                            <div class="text-muted small mb-1">Fecha Fin</div>
                            <div class="text-white fw-bold"><i class="bi bi-calendar-x me-2 text-danger"></i>{{ $project->end_date ? $project->end_date->format('d M, Y') : '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                            <div class="text-muted small mb-1">Tareas</div>
                            <div class="text-white fw-bold"><i class="bi bi-check2-all me-2 text-success"></i>{{ $project->tasks->count() }} totales</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white fw-bold">Progreso del Proyecto</span>
                        <span class="text-primary fw-bold">{{ $project->progress }}%</span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 6px; background: rgba(255,255,255,0.05);">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: {{ $project->progress }}%; background: var(--gradient-1);"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-dark-2 border-0 h-100">
            <div class="card-header border-bottom border-light bg-transparent p-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">Equipo Asignado</h5>
                @if(auth()->user()->isSupervisor())
                <button type="button" class="btn btn-outline-custom btn-sm" data-bs-toggle="modal" data-bs-target="#manageTeamModal">
                    <i class="bi bi-person-plus me-1"></i> Gestionar
                </button>
                @endif
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-3">
                    @foreach($project->team as $member)
                    <div class="d-flex align-items-center p-3 rounded-4" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                        <div class="avatar-md me-3" style="width: 40px; height: 40px; border-radius: 12px; background: var(--gradient-2); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="text-white fw-bold">{{ $member->name }}</div>
                            <div class="text-muted small">{{ ucfirst($member->role) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mt-4">
        <div class="card bg-dark-2 border-0">
            <div class="card-header border-bottom border-light bg-transparent p-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">Tareas del Proyecto</h5>
                <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}" class="btn btn-outline-custom btn-sm">Gestionar Tareas</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0">
                        <thead class="bg-dark-3">
                            <tr class="text-muted small text-uppercase">
                                <th class="border-0 px-4 py-3">Tarea</th>
                                <th class="border-0 px-4 py-3">Estado</th>
                                <th class="border-0 px-4 py-3">Responsable</th>
                                <th class="border-0 px-4 py-3">Prioridad</th>
                                <th class="border-0 px-4 py-3 text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project->tasks->take(10) as $task)
                            <tr>
                                <td class="px-4 py-3 align-middle">
                                    <div class="text-white fw-medium">{{ $task->title }}</div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <span class="badge-status badge-{{ $task->status }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-2" style="width: 24px; height: 24px; border-radius: 50%; background: var(--dark-3); font-size: 0.6rem; display: flex; align-items: center; justify-content: center;">{{ substr($task->assignedUser->name ?? '—', 0, 1) }}</div>
                                        <span class="small">{{ $task->assignedUser->name ?? 'Sin asignar' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <span class="badge-status badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                                </td>
                                <td class="px-4 py-3 align-middle text-end">
                                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-link p-0 text-muted"><i class="bi bi-pencil"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->isSupervisor())
<!-- Modal Gestionar Equipo -->
<div class="modal fade" id="manageTeamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark-2 border-0 shadow-lg">
            <div class="modal-header border-bottom border-light p-4">
                <h5 class="modal-title text-white">Gestionar Equipo del Proyecto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('projects.updateTeam', $project) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Seleccione los supervisores y usuarios que formarán parte de este proyecto.</p>
                    
                    <div class="row g-2" style="max-height: 400px; overflow-y: auto;">
                        @php $teamIds = $project->team->pluck('id')->toArray(); @endphp
                        @foreach($availableUsers as $user)
                        <div class="col-12">
                            <div class="form-check p-3 rounded-4 d-flex align-items-center" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); cursor: pointer;" onclick="document.getElementById('m-user-{{ $user->id }}').click()">
                                <input class="form-check-input ms-0 me-3" type="checkbox" name="team[]" value="{{ $user->id }}" id="m-user-{{ $user->id }}" {{ in_array($user->id, $teamIds) ? 'checked' : '' }} onclick="event.stopPropagation()">
                                <label class="form-check-label d-flex align-items-center w-100" for="m-user-{{ $user->id }}" onclick="event.stopPropagation()">
                                    <div class="avatar-sm me-3" style="width: 32px; height: 32px; border-radius: 8px; background: var(--gradient-2); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.8rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-white fw-bold">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ ucfirst($user->role) }}</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-top border-light p-4">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom px-4">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
