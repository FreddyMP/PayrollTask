@extends('layouts.app')

@section('title', 'Resultados de Evaluación')
@section('page-title', 'Resultados: ' . $evaluation->title)

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="text-white mb-1">Resultados Recibidos</h5>
            <p class="small text-muted">Total de respuestas: {{ $evaluation->responses->count() }}</p>
        </div>
        <a href="{{ route('evaluations.show', $evaluation) }}" class="btn btn-outline-custom">
            <i class="bi bi-arrow-left me-2"></i>Volver a Gestión
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-dark-2">
                            <tr>
                                <th>Empleado</th>
                                <th>Fecha</th>
                                @foreach($evaluation->questions as $question)
                                    <th>P{{ $loop->iteration }} <span class="text-muted fw-normal" style="font-size: 0.7rem;" title="{{ $question->question_text }}">({{ Str::limit($question->question_text, 15) }})</span></th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($evaluation->responses as $response)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary-light text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            {{ substr($response->employee->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="d-block text-white">{{ $response->employee->user->name }}</span>
                                            <span class="small text-muted">{{ $response->employee->department }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted small">{{ $response->created_at->format('d/m/Y H:i') }}</td>
                                @foreach($evaluation->questions as $question)
                                    @php
                                        $answer = $response->answers->where('evaluation_question_id', $question->id)->first();
                                    @endphp
                                    <td>
                                        @if($answer)
                                            @if($question->type === 'scale')
                                                <span class="badge {{ $answer->answer_scale >= 7 ? 'bg-success' : ($answer->answer_scale >= 4 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                    {{ $answer->answer_scale }} / 10
                                                </span>
                                            @else
                                                <span class="d-inline-block text-truncate text-white" style="max-width: 150px;" title="{{ $answer->answer_text }}">
                                                    {{ $answer->answer_text }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted fst-italic small">N/A</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 2 + $evaluation->questions->count() }}" class="text-center py-5 text-muted">
                                    Aún no hay respuestas para esta evaluación.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
