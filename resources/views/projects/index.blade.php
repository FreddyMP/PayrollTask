@extends('layouts.app')
@section('title', 'Proyectos')
@section('page-title', 'Gestión de Proyectos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-white">Proyectos</h4>
    @if(auth()->user()->isSupervisor() || auth()->user()->isAdmin() || auth()->user()->isSuper())
    <a href="{{ route('projects.create') }}" class="btn btn-primary-custom">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Proyecto
    </a>
    @endif
</div>

<div class="row g-4">
    @forelse($projects as $project)
    <div class="col-xl-4 col-md-6">
        <div class="card h-100 stat-card" style="border: 1px solid rgba(255,255,255,0.06); background: var(--dark-2); transition: all 0.3s ease;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge-status badge-{{ $project->status }}">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</span>
                    <div class="d-flex align-items-center gap-2">
                        @if(auth()->user()->isSupervisor() || auth()->user()->isAdmin() || auth()->user()->isSuper())
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn text-secondary p-0" title="Editar">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        @endif
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="{{ route('projects.show', $project) }}"><i class="bi bi-eye me-2"></i>Ver Detalles</a></li>
                            @if(auth()->user()->isSupervisor() || auth()->user()->isAdmin() || auth()->user()->isSuper())
                            <li><a class="dropdown-item" href="{{ route('projects.edit', $project) }}"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('¿Eliminar proyecto y sus tareas relacionadas?')">
                                    @csrf @method('DELETE')
                                    <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Eliminar</button>
                                </form>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

                <h5 class="text-white mb-2">{{ $project->name }}</h5>
                <p class="text-secondary small mb-4" style="height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                    {{ $project->description ?? 'Sin descripción' }}
                </p>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1 small">
                        <span class="text-muted">Progreso</span>
                        <span class="text-white fw-bold">{{ $project->progress }}%</span>
                    </div>
                    <div class="progress" style="height: 6px; background: rgba(255,255,255,0.05);">
                        <div class="progress-bar" style="width: {{ $project->progress }}%; background: var(--gradient-1);"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="avatar-group d-flex">
                        @foreach($project->team->take(4) as $member)
                        <div class="avatar-sm" title="{{ $member->name }}" style="width: 28px; height: 28px; border-radius: 50%; background: var(--gradient-2); border: 2px solid var(--dark-2); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; margin-right: -10px;">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        @endforeach
                        @if($project->team->count() > 4)
                        <div class="avatar-sm" style="width: 28px; height: 28px; border-radius: 50%; background: var(--dark-3); border: 2px solid var(--dark-2); color: #94a3b8; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; margin-right: -10px;">
                            +{{ $project->team->count() - 4 }}
                        </div>
                        @endif
                    </div>
                    <div class="small text-secondary">
                        <i class="bi bi-calendar-event me-1"></i> {{ $project->end_date ? $project->end_date->format('d/m/Y') : 'Sin fecha' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <div class="mb-3"><i class="bi bi-folder-x display-4 text-muted"></i></div>
        <h5 class="text-muted">No hay proyectos registrados</h5>
        @if(auth()->user()->isSupervisor()|| auth()->user()->isAdmin() || auth()->user()->isSuper())
        <a href="{{ route('projects.create') }}" class="btn btn-primary-custom mt-3">Crear primer proyecto</a>
        @endif
    </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $projects->links() }}
</div>
@endsection
