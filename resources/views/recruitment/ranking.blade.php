@extends('layouts.app')

@section('title', 'Ranking de Candidatos')
@section('page-title', 'Ranking: ' . $vacancy->title)

@push('styles')
<style>
    .ranking-top-1 {
        background: linear-gradient(135deg, rgba(234, 179, 8, 0.1) 0%, rgba(15, 23, 42, 0.8) 100%);
        border: 2px solid #eab308 !important;
        transform: scale(1.02);
    }
    .ranking-top-2 {
        background: linear-gradient(135deg, rgba(148, 163, 184, 0.1) 0%, rgba(15, 23, 42, 0.8) 100%);
        border: 2px solid #94a3b8 !important;
    }
    .ranking-top-3 {
        background: linear-gradient(135deg, rgba(180, 83, 9, 0.1) 0%, rgba(15, 23, 42, 0.8) 100%);
        border: 2px solid #b45309 !important;
    }
    .trophy-badge {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="text-white mb-1">Resultados de Evaluación</h5>
            <p class="text-white small">Candidatos ordenados por su desempeño acumulado.</p>
        </div>
        <a href="{{ route('recruitment.show', $vacancy) }}" class="btn btn-outline-custom">
            <i class="bi bi-arrow-left me-2"></i>Volver a la Vacante
        </a>
    </div>
</div>

<div class="row g-4">
    @foreach($candidates->take(3) as $candidate)
    <div class="col-md-4">
        <div class="card ranking-top-{{ $loop->iteration }} h-100">
            <div class="card-body text-center py-4">
                <div class="d-flex justify-content-center mb-3">
                    <div class="trophy-badge bg-{{ $loop->iteration === 1 ? 'warning' : ($loop->iteration === 2 ? 'secondary' : 'danger') }} text-white">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                </div>
                <h4 class="text-white mb-1">{{ $candidate->name }}</h4>
                <p class="text-muted small mb-3">Puesto #{{ $loop->iteration }}</p>
                <div class="display-5 fw-bold text-white mb-3">{{ $candidate->total_points }}</div>
                <p class="text-white small mb-0">puntos acumulados</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card mt-4">
    <div class="card-header">
        <span class="text-white">Listado Completo</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">Posición</th>
                        <th>Candidato</th>
                        <th>Desglose por Pasos</th>
                        <th class="text-end">Puntaje Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates as $candidate)
                    <tr>
                        <td class="text-center">
                            @if($loop->iteration <= 3)
                                <i class="bi bi-star-fill text-{{ $loop->iteration === 1 ? 'warning' : ($loop->iteration === 2 ? 'secondary' : 'danger') }}"></i>
                            @else
                                <span class="text-muted">#{{ $loop->iteration }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-white fw-bold">{{ $candidate->name }}</div>
                            <small class="text-muted">{{ $candidate->email }}</small>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                @foreach($vacancy->steps as $step)
                                    @php 
                                        $prog = $candidate->progress->firstWhere('recruitment_step_id', $step->id);
                                    @endphp
                                    <div class="badge bg-dark-3 text-muted" style="font-size: 0.65rem;">
                                        {{ $step->name }}: {{ $prog ? ($prog->score ?? 0) : 0 }}
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="text-end">
                            <span class="text-white fw-bold h5 mb-0">{{ $candidate->total_points }}</span>
                            <small class="text-muted px-1">/ {{ $vacancy->steps->sum('points') }}</small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No hay candidatos para mostrar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
