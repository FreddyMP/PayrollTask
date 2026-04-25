@extends('layouts.app')

@section('title', 'Gestionar Plantilla')
@section('page-title', 'Plantilla: ' . $template->title)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark-2 py-3 d-flex justify-content-between align-items-center">
                <span class="text-white fw-bold">Contenido de la Plantilla</span>
                <span class="badge bg-primary">{{ ucfirst($template->category) }}</span>
            </div>
            <div class="card-body">
                <div class="bg-dark-3 p-4 rounded-3 text-white border border-secondary" style="min-height: 400px; white-space: pre-wrap;">{{ $template->content }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark-2 py-3">
                <span class="text-white fw-bold">Generar Documento</span>
            </div>
            <div class="card-body">
                <form action="{{ route('documents.generate', $template) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label">Contexto (Opcional)</label>
                        <select name="employee_id" class="form-select">
                            <option value="">Ninguno (Solo variables globales)</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->user->name }} ({{ $employee->id_number }})</option>
                            @endforeach
                        </select>
                        <small class=" text-white d-block mt-2">
                            Si seleccionas un empleado, podrás usar tags como <code>&lt;# salary #&gt;</code>, <code>&lt;# contract_type #&gt;</code>, etc.
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Formato de Salida</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="formatPreview" value="preview" checked>
                                <label class="form-check-label text-white" for="formatPreview">Vista Previa</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="formatPdf" value="pdf">
                                <label class="form-check-label text-white" for="formatPdf">Descargar PDF</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary-custom py-2">
                            <i class="bi bi-play-fill me-2"></i>Generar Documento
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark-2 py-3">
                <span class="text-white fw-bold">Ayuda de Variables</span>
            </div>
            <div class="card-body">
                <h6 class="text-white small fw-bold mb-2">Variables Globales</h6>
                <p class="text-white  extra-small mb-3">Configuradas en <a href="{{ route('company-fields.index') }}" class="text-decoration-none">Variables Globales</a>.</p>
                
                <h6 class="text-white  small fw-bold mb-2">Variables de Sistema</h6>
                <ul class="list-unstyled extra-small mb-0">
                    <li><code>&lt;# empresa_nombre #&gt;</code></li>
                    <li><code>&lt;# empresa_rnc #&gt;</code></li>
                    <li><code>&lt;# empresa_direccion #&gt;</code></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.75rem; }
</style>
@endsection
