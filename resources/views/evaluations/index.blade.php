@extends('layouts.app')

@section('title', 'Evaluaciones de Personal')
@section('page-title', 'Evaluaciones de Personal')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="text-white mb-1">Gestión de Evaluaciones</h5>
            <p class="small ">Crea formularios de evaluación y asígnalos a los empleados.</p>
        </div>
        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle me-2"></i>Nueva Evaluación
        </button>
    </div>
</div>

<div class="row">
    @forelse($evaluations as $evaluation)
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 class="text-white fw-bold mb-0">{{ $evaluation->title }}</h6>
                    <span class="badge {{ $evaluation->status === 'active' ? 'bg-success' : ($evaluation->status === 'draft' ? 'bg-secondary' : 'bg-danger') }}">
                        {{ ucfirst($evaluation->status) }}
                    </span>
                </div>
                <p class="small text-muted mb-3">{{ Str::limit($evaluation->description, 100) }}</p>
                <div class="d-flex gap-3 mb-4">
                    <div class="text-center">
                        <span class="d-block h5 text-white mb-0">{{ $evaluation->questions_count }}</span>
                        <span class="small text-muted" style="font-size:0.75rem;">Preguntas</span>
                    </div>
                    <div class="text-center">
                        <span class="d-block h5 text-white mb-0">{{ $evaluation->assignments_count }}</span>
                        <span class="small text-muted" style="font-size:0.75rem;">Asignados</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('evaluations.show', $evaluation) }}" class="btn btn-primary-custom btn-sm flex-grow-1">
                        <i class="bi bi-gear-fill me-1"></i> Gestionar
                    </a>
                    <a href="{{ route('evaluations.results', $evaluation) }}" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-bar-chart-fill"></i>
                    </a>
                    <form action="{{ route('evaluations.destroy', $evaluation) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar esta evaluación?')">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-clipboard2-check display-1 opacity-25"></i>
        <p class="mt-3 text-muted">No hay evaluaciones creadas.</p>
    </div>
    @endforelse
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('evaluations.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-white">Nueva Evaluación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="allow_multiple_responses" class="form-check-input" id="multipleResponses">
                        <label class="form-check-label text-white small" for="multipleResponses">
                            Permitir responder múltiples veces
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Crear Evaluación</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
