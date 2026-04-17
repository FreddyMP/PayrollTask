@extends('layouts.app')
@section('title', 'Nueva Actividad')
@section('page-title', 'Nueva Actividad')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-calendar-plus me-2"></i>Registrar Actividad</div>
            <div class="card-body">
                <form method="POST" action="{{ route('calendar.store') }}" id="eventForm">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="event_date" value="{{ old('event_date', $date) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hora <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" name="event_time" value="{{ old('event_time') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre de la actividad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" value="{{ old('title') }}" placeholder="Ej: Reunión de equipo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="description" rows="4" placeholder="Detalle de la actividad...">{{ old('description') }}</textarea>
                    </div>

                    <!-- Dynamic Links -->
                    <div class="mb-3">
                        <label class="form-label">Enlaces</label>
                        <div id="linksContainer">
                            @if(old('links'))
                                @foreach(old('links') as $i => $link)
                                <div class="link-row d-flex gap-2 mb-2 align-items-start">
                                    <div class="flex-grow-1">
                                        <input type="url" class="form-control form-control-sm mb-1" name="links[{{ $i }}][url]" placeholder="https://ejemplo.com" value="{{ $link['url'] ?? '' }}" required>
                                        <input type="text" class="form-control form-control-sm" name="links[{{ $i }}][label]" placeholder="Etiqueta (opcional)" value="{{ $link['label'] ?? '' }}">
                                    </div>
                                    <button type="button" class="btn btn-sm" style="color:#f87171;background:rgba(239,68,68,0.1);border-radius:8px;padding:6px 10px;" onclick="this.closest('.link-row').remove()">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-custom mt-1" onclick="addLink()">
                            <i class="bi bi-plus-circle me-1"></i>Agregar enlace
                        </button>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> Guardar Actividad</button>
                        <a href="{{ route('calendar.index') }}" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let linkIndex = {{ old('links') ? count(old('links')) : 0 }};

    function addLink() {
        const container = document.getElementById('linksContainer');
        const row = document.createElement('div');
        row.className = 'link-row d-flex gap-2 mb-2 align-items-start';
        row.innerHTML = `
            <div class="flex-grow-1">
                <input type="url" class="form-control form-control-sm mb-1" name="links[${linkIndex}][url]" placeholder="https://ejemplo.com" required>
                <input type="text" class="form-control form-control-sm" name="links[${linkIndex}][label]" placeholder="Etiqueta (opcional)">
            </div>
            <button type="button" class="btn btn-sm" style="color:#f87171;background:rgba(239,68,68,0.1);border-radius:8px;padding:6px 10px;" onclick="this.closest('.link-row').remove()">
                <i class="bi bi-trash"></i>
            </button>
        `;
        container.appendChild(row);
        linkIndex++;
    }
</script>
@endpush
@endsection
