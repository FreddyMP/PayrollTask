@extends('layouts.app')
@section('title', 'Nueva Solicitud')
@section('page-title', 'Nueva Solicitud')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-send me-2"></i>Crear Solicitud</div>
            <div class="card-body">
                <form method="POST" action="{{ route('requests.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tipo de Solicitud</label>
                        <select class="form-select" name="type" id="requestType" required>
                            <option value="vacation" {{ old('type') == 'vacation' ? 'selected' : '' }}>Vacaciones</option>
                            <option value="permission" {{ old('type') == 'permission' ? 'selected' : '' }}>Permiso</option>
                            <option value="work_letter" {{ old('type') == 'work_letter' ? 'selected' : '' }}>Carta de Trabajo</option>
                            <option value="overtime" {{ old('type') == 'overtime' ? 'selected' : '' }}>Horas Extra</option>
                        </select>
                    </div>

                    {{-- Campos de fechas (Vacaciones / Permiso) --}}
                    <div id="dateFields">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" name="start_date" value="{{ old('start_date') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" name="end_date" value="{{ old('end_date') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Campos de Horas Extra --}}
                    <div id="overtimeFields" style="display: none;">
                        <div class="p-3 rounded-3 mb-3" style="background: rgba(251,146,60,0.07); border: 1px solid rgba(251,146,60,0.2);">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-clock-fill" style="color: #fb923c;"></i>
                                <span class="fw-semibold" style="color: #fb923c; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em;">Datos de Horas Extra</span>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-12">
                                    <label class="form-label">Día de las Horas Extra</label>
                                    <input type="date" class="form-control" name="overtime_date" id="overtimeDate" value="{{ old('overtime_date') }}">
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-5">
                                    <label class="form-label">Hora de Inicio</label>
                                    <input type="time" class="form-control" name="overtime_start" id="overtimeStart" value="{{ old('overtime_start') }}">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Hora de Fin</label>
                                    <input type="time" class="form-control" name="overtime_end" id="overtimeEnd" value="{{ old('overtime_end') }}">
                                </div>
                                <div class="col-md-2 d-flex flex-column justify-content-end">
                                    <label class="form-label text-center" style="color: #fb923c;">Total</label>
                                    <div id="hoursDisplay" class="text-center rounded-2 py-2 px-1 fw-bold" style="background: rgba(251,146,60,0.15); color: #fb923c; font-size: 1.1rem; border: 1px solid rgba(251,146,60,0.3);">
                                        0:00 h
                                    </div>
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Aprobador <span class="text-danger">*</span></label>
                                @if($approvers->count() > 0)
                                    <select class="form-select" name="approved_by_user_id" id="approvedBy">
                                        <option value="">— Selecciona quién aprobará las horas extra —</option>
                                        @foreach($approvers as $approver)
                                            <option value="{{ $approver->id }}" {{ old('approved_by_user_id') == $approver->id ? 'selected' : '' }}>
                                                {{ $approver->name }} ({{ ucfirst($approver->role) }})
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="alert alert-danger py-2 mb-0" style="font-size: 0.82rem;">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        No hay usuarios con rol superior disponibles para aprobar. Contacta a tu administrador.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Adjuntar Imágenes o Videos (Máx 30MB por archivo)</label>
                        <input type="file" class="form-control" name="attachments[]" accept="image/*,video/*" multiple>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción / Motivo</label>
                        <textarea class="form-control" name="description" rows="4" placeholder="Explique el motivo de su solicitud...">{{ old('description') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-send me-1"></i> Enviar Solicitud</button>
                        <a href="{{ route('requests.index') }}" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const requestType  = document.getElementById('requestType');
const dateFields   = document.getElementById('dateFields');
const overtimeFields = document.getElementById('overtimeFields');
const overtimeStart  = document.getElementById('overtimeStart');
const overtimeEnd    = document.getElementById('overtimeEnd');
const hoursDisplay   = document.getElementById('hoursDisplay');

function toggleFields() {
    const type = requestType.value;
    if (type === 'work_letter') {
        dateFields.style.display = 'none';
        overtimeFields.style.display = 'none';
    } else if (type === 'overtime') {
        dateFields.style.display = 'none';
        overtimeFields.style.display = 'block';
    } else {
        dateFields.style.display = 'block';
        overtimeFields.style.display = 'none';
    }
}

function calcHours() {
    const start = overtimeStart.value;
    const end   = overtimeEnd.value;
    if (!start || !end) { hoursDisplay.textContent = '0:00 h'; return; }

    const [sh, sm] = start.split(':').map(Number);
    const [eh, em] = end.split(':').map(Number);
    let totalMin = (eh * 60 + em) - (sh * 60 + sm);
    if (totalMin <= 0) { hoursDisplay.textContent = '—'; hoursDisplay.style.color = '#f87171'; return; }

    const hours = Math.floor(totalMin / 60);
    const mins  = totalMin % 60;
    hoursDisplay.textContent = `${hours}:${String(mins).padStart(2, '0')} h`;
    hoursDisplay.style.color = '#fb923c';
}

requestType.addEventListener('change', toggleFields);
overtimeStart.addEventListener('change', calcHours);
overtimeEnd.addEventListener('change', calcHours);

// Init
toggleFields();
calcHours();
</script>
@endpush
