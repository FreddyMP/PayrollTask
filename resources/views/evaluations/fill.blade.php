@extends('layouts.app')

@section('title', 'Completar Evaluación')
@section('page-title', 'Evaluación: ' . $evaluation->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                @if($evaluation->description)
                <div class="alert alert-dark bg-dark-2 border-secondary text-white mb-4">
                    <i class="bi bi-info-circle me-2"></i> {{ $evaluation->description }}
                </div>
                @endif

                <form action="{{ route('evaluations.submit', $evaluation) }}" method="POST">
                    @csrf
                    
                    @foreach($evaluation->questions as $index => $question)
                    <div class="mb-4 pb-4 {{ !$loop->last ? 'border-bottom border-secondary' : '' }}">
                        <label class="form-label text-white fw-bold mb-3" style="font-size: 1.1rem;">
                            {{ $index + 1 }}. {{ $question->question_text }}
                            @if($question->is_required)
                                <span class="text-danger">*</span>
                            @endif
                        </label>

                        @if($question->type === 'scale')
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-muted small">1</span>
                                <input type="range" class="form-range flex-grow-1" name="q_{{ $question->id }}" min="1" max="10" step="1" id="range_{{ $question->id }}" oninput="document.getElementById('val_{{ $question->id }}').innerText = this.value" {{ $question->is_required ? 'required' : '' }}>
                                <span class="text-muted small">10</span>
                                <span class="badge bg-primary fs-6 ms-3" id="val_{{ $question->id }}" style="min-width: 40px;">5</span>
                            </div>
                            <script>
                                // Set initial value display
                                document.getElementById('range_{{ $question->id }}').value = 5;
                            </script>
                        @elseif($question->type === 'boolean')
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q_{{ $question->id }}" id="q_{{ $question->id }}_yes" value="1" {{ $question->is_required ? 'required' : '' }}>
                                    <label class="form-check-label text-white" for="q_{{ $question->id }}_yes">
                                        <i class="bi bi-check-circle text-success me-1"></i>Sí
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q_{{ $question->id }}" id="q_{{ $question->id }}_no" value="0">
                                    <label class="form-check-label text-white" for="q_{{ $question->id }}_no">
                                        <i class="bi bi-x-circle text-danger me-1"></i>No
                                    </label>
                                </div>
                            </div>
                        @elseif($question->type === 'text')
                            <input type="text" name="q_{{ $question->id }}" class="form-control" placeholder="Escribe tu respuesta aquí..." {{ $question->is_required ? 'required' : '' }}>
                        @elseif($question->type === 'textarea')
                            <textarea name="q_{{ $question->id }}" class="form-control" rows="4" placeholder="Escribe tu respuesta detallada aquí..." {{ $question->is_required ? 'required' : '' }}></textarea>
                        @endif
                    </div>
                    @endforeach

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-primary-custom py-3 fs-5 fw-bold">
                            <i class="bi bi-check-circle-fill me-2"></i>Enviar Respuestas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
