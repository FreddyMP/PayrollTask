@extends('layouts.app')

@section('title', 'Gestionar Vacante')
@section('page-title', $vacancy->title)

@push('styles')
<style>
    .step-card {
        border-left: 4px solid var(--primary);
        transition: all 0.2s;
    }
    .step-card:hover { transform: translateX(5px); }
    .timeline-item {
        position: relative;
        padding-left: 30px;
        padding-bottom: 20px;
        border-left: 1px solid var(--dark-3);
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--dark-3);
    }
    .timeline-item.completed::before { background: var(--success); }
    .timeline-item.active::before { background: var(--primary); }
    .timeline-item.discarded::before { background: var(--danger); }
</style>
@endpush

@section('content')
<div class="row">
    <!-- Left Column: Steps and Config -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-white">Pasos del Proceso</span>
                <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addStepModal">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush bg-transparent">
                    @forelse($vacancy->steps as $step)
                    <div class="list-group-item bg-transparent border-0 ps-0 mb-3 step-card">
                        <div class="d-flex justify-content-between">
                            <h6 class="text-white mb-1">{{ $loop->iteration }}. {{ $step->name }}</h6>
                            <span class="badge badge-status badge-primary">{{ $step->points }} pts</span>
                        </div>
                        <p class="small text-muted mb-0">Resp: {{ $step->responsible->name }}</p>
                    </div>
                    @empty
                    <p class="text-muted text-center py-3">Define los pasos para esta vacante.</p>
                    @endforelse
                </div>
                
                @if($vacancy->steps->count() > 0)
                <div class="mt-3">
                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Puntuación Total</span>
                        <span>{{ $vacancy->steps->sum('points') }} / 100</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: {{ $vacancy->steps->sum('points') }}%"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="text-white">Información General</span>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">{{ $vacancy->description }}</p>
                <div class="d-grid">
                    <a href="{{ route('recruitment.ranking', $vacancy) }}" class="btn btn-outline-custom">
                        <i class="bi bi-trophy-fill me-2 text-warning"></i>Ver Ranking
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Candidates -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-white mb-0">Candidatos Postulados</h5>
                <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addCandidateModal">
                    <i class="bi bi-person-plus-fill me-2"></i>Agregar Candidato
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Candidato</th>
                                <th>CV</th>
                                <th>Paso Actual</th>
                                <th>Puntos</th>
                                <th class="text-end">Gestión</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vacancy->candidates as $candidate)
                            <tr class="{{ $candidate->status === 'discarded' ? 'opacity-50' : '' }}">
                                <td>
                                    <div class="fw-bold text-dark">{{ $candidate->name }}</div>
                                    <small class="text-dark">{{ $candidate->email }}</small>
                                </td>
                                <td>
                                    @if($candidate->cv_path)
                                    <a href="{{ asset('storage/' . $candidate->cv_path) }}" target="_blank" class="text-primary-light">
                                        <i class="bi bi-file-earmark-pdf"></i> PDF
                                    </a>
                                    @endif
                                </td>
                                <td>
                                    @if($candidate->status === 'discarded')
                                        <span class="badge badge-status badge-rejected">Descartado</span>
                                    @else
                                        <span class="text-primary-light small">
                                            {{ $candidate->current_step->name ?? 'Completado' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-status badge-review">{{ $candidate->total_points }} pts</span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-outline-custom btn-sm" onclick="showProgressModal({{ $candidate->toJson() }}, {{ $candidate->current_step ? $candidate->current_step->toJson() : 'null' }})">
                                        <i class="bi bi-gear"></i> Procesar
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No hay candidatos registrados.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Step Modal -->
<div class="modal fade" id="addStepModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('recruitment.steps.store', $vacancy) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-white">Agregar Paso de Reclutamiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del paso</label>
                        <input type="text" name="name" class="form-control" placeholder="Ej: Entrevista RRHH" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Responsable</label>
                        <select name="responsible_id" class="form-select" required>
                            <option value="">Seleccione un usuario...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Puntos que vale este paso (Máx {{ 100 - $vacancy->steps->sum('points') }})</label>
                        <input type="number" name="points" class="form-control" min="1" max="100" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Agregar Paso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Candidate Modal -->
<div class="modal fade" id="addCandidateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('recruitment.candidates.store', $vacancy) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-white">Nuevo Candidato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre Completo</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-2 row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CV (PDF/Doc)</label>
                        <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Progress Modal -->
<div class="modal fade" id="progressModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="progressModalTitle">Línea de Tiempo: Candidato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Timeline Section -->
                    <div class="col-md-6 border-end border-dark-3">
                        <h6 class="text-white mb-4">Historial de Pasos</h6>
                        <div id="candidateTimeline">
                            <!-- JS will populate this -->
                        </div>
                    </div>
                    <!-- Action Section -->
                    <div class="col-md-6">
                        <div id="currentActionContainer">
                            <h6 class="text-white mb-3">Acción del Paso Actual</h6>
                            <form id="progressForm" method="POST">
                                @csrf
                                <input type="hidden" name="recruitment_step_id" id="formStepId">
                                <div class="mb-3">
                                    <label class="form-label">Puntuación Obtenida (Máx <span id="maxScoreLabel"></span>)</label>
                                    <input type="number" name="score" id="scoreInput" class="form-control" min="0">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notas / Feedback</label>
                                    <textarea name="notes" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Resultado</label>
                                    <select name="status" id="statusSelect" class="form-select" required>
                                        <option value="completed">Aprobar Paso</option>
                                        <option value="discarded">Descartar Candidato</option>
                                    </select>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary-custom">Guardar Progreso</button>
                                </div>
                            </form>
                        </div>
                        <div id="candidateDiscardedMessage" class="alert alert-danger d-none">
                            Este candidato ha sido descartado.
                        </div>
                        <div id="allStepsCompletedMessage" class="alert alert-success d-none">
                            Proceso completado para este candidato.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showProgressModal(candidate, currentStep) {
    $('#progressModalTitle').text('Progreso: ' + candidate.name);
    $('#formStepId').val(currentStep ? currentStep.id : '');
    $('#maxScoreLabel').text(currentStep ? currentStep.points : '');
    $('#scoreInput').attr('max', currentStep ? currentStep.points : '');
    $('#progressForm').attr('action', `/recruitment/candidates/${candidate.id}/progress`);

    const timelineContainer = $('#candidateTimeline');
    timelineContainer.empty();

    // Populate timeline via progress data
    const progress = candidate.progress || [];
    const steps = @json($vacancy->steps);
    
    steps.forEach(step => {
        const stepProgress = progress.find(p => p.recruitment_step_id === step.id);
        let statusClass = '';
        let badgeText = 'Pendiente';
        
        if (stepProgress) {
            if (stepProgress.status === 'completed') {
                statusClass = 'completed';
                badgeText = stepProgress.score + ' pts';
            } else if (stepProgress.status === 'discarded') {
                statusClass = 'discarded';
                badgeText = 'Descartado';
            } else {
                statusClass = 'active';
                badgeText = 'En curso';
            }
        }

        timelineContainer.append(`
            <div class="timeline-item ${statusClass}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="text-white small fw-bold">${step.name}</div>
                        <div class="text-muted" style="font-size: 0.7rem;">${step.responsible ? step.responsible.name : ''}</div>
                    </div>
                    <span class="badge badge-status badge-${statusClass || 'pending'}">${badgeText}</span>
                </div>
            </div>
        `);
    });

    // Handle form visibility
    if (candidate.status === 'discarded') {
        $('#currentActionContainer').addClass('d-none');
        $('#candidateDiscardedMessage').removeClass('d-none');
        $('#allStepsCompletedMessage').addClass('d-none');
    } else if (!currentStep) {
        $('#currentActionContainer').addClass('d-none');
        $('#candidateDiscardedMessage').addClass('d-none');
        $('#allStepsCompletedMessage').removeClass('d-none');
    } else {
        $('#currentActionContainer').removeClass('d-none');
        $('#candidateDiscardedMessage').addClass('d-none');
        $('#allStepsCompletedMessage').addClass('d-none');
    }

    const modal = new bootstrap.Modal(document.getElementById('progressModal'));
    modal.show();
}
</script>
@endpush
