@extends('layouts.app')

@section('title', 'Gestionar Evaluación')
@section('page-title', 'Evaluación: ' . $evaluation->title)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark-2 py-3 d-flex justify-content-between align-items-center">
                <span class="text-white fw-bold">Preguntas</span>
                <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                    <i class="bi bi-plus-circle me-1"></i>Añadir Pregunta
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pregunta</th>
                                <th>Tipo</th>
                                <th>Requerida</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($evaluation->questions as $question)
                            <tr>
                                <td>{{ $question->order }}</td>
                                <td>{{ $question->question_text }}</td>
                                <td>
                                    @if($question->type === 'scale') Escala 1-10
                                    @elseif($question->type === 'text') Texto Corto
                                    @else Texto Largo @endif
                                </td>
                                <td>
                                    @if($question->is_required) <span class="badge bg-danger">Sí</span>
                                    @else <span class="badge bg-secondary">No</span> @endif
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('evaluations.questions.destroy', [$evaluation, $question]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm border-0" onclick="return confirm('¿Eliminar pregunta?')">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No hay preguntas configuradas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark-2 py-3 d-flex justify-content-between align-items-center">
                <span class="text-white fw-bold">Asignar a Empleados</span>
            </div>
            <div class="card-body">
                <form action="{{ route('evaluations.assignments.store', $evaluation) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Seleccionar Empleados</label>
                        <select name="employee_ids[]" class="form-select" multiple size="6" required>
                            @foreach($employees as $employee)
                                @if(!$evaluation->assignments->contains('employee_id', $employee->id))
                                    <option value="{{ $employee->id }}">{{ $employee->user->name }} ({{ $employee->department }})</option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-2">Mantén presionado Ctrl/Cmd para seleccionar múltiples.</small>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100">
                        <i class="bi bi-send-fill me-2"></i>Asignar y Notificar
                    </button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark-2 py-3">
                <span class="text-white fw-bold">Empleados Asignados</span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush rounded-bottom">
                    @forelse($evaluation->assignments as $assignment)
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center border-secondary">
                        <div>
                            <span class="text-white">{{ $assignment->employee->user->name }}</span>
                            <br>
                            @if($assignment->is_completed)
                                <span class="badge bg-success" style="font-size:0.65rem">Completado</span>
                            @else
                                <span class="badge bg-warning text-dark" style="font-size:0.65rem">Pendiente</span>
                            @endif
                        </div>
                        <form action="{{ route('evaluations.assignments.destroy', [$evaluation, $assignment]) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm border-0 py-0" onclick="return confirm('¿Quitar asignación?')">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </form>
                    </li>
                    @empty
                    <li class="list-group-item bg-transparent text-center text-muted py-3">Ningún empleado asignado.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('evaluations.questions.store', $evaluation) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-white">Añadir Pregunta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pregunta</label>
                        <textarea name="question_text" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Respuesta</label>
                        <select name="type" class="form-select" required>
                            <option value="scale">Escala del 1 al 10</option>
                            <option value="text">Texto Corto (Una línea)</option>
                            <option value="textarea">Texto Largo (Párrafo)</option>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_required" class="form-check-input" id="isRequiredCheck" checked>
                        <label class="form-check-label text-white small" for="isRequiredCheck">
                            Obligatoria
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Añadir Pregunta</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
