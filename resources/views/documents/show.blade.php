@extends('layouts.app')

@section('title', 'Gestionar Plantilla')
@section('page-title', 'Plantilla: ' . $template->title)

@section('content')
<div class="row">
<div class="col-lg-8">
        <ul class="nav nav-tabs border-0 mb-3" id="templateTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active text-white fw-bold border-0 bg-transparent" id="vars-tab" data-bs-toggle="tab" data-bs-target="#vars" type="button" role="tab">
                    <i class="bi bi-tags-fill me-2"></i>Variables de Plantilla
                </button>
            </li>
            <!--
            <li class="nav-item">
                <button class="nav-link text-white fw-bold border-0 bg-transparent opacity-50" id="content-tab" data-bs-toggle="tab" data-bs-target="#contentTemplate" type="button" role="tab">
                    <i class="bi bi-file-earmark-text me-2"></i>Contenido de Plantilla
                </button>
            </li>
            -->
        </ul>

        <div class="tab-content" id="templateTabsContent">
            <!-- Variables Tab -->
            <div class="tab-pane fade show active" id="vars" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark-2 py-3 d-flex justify-content-between align-items-center">
                        <span class="text-white fw-bold">Gestión de Variables de Plantilla</span>
                        <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addVariableModal">
                            <i class="bi bi-plus-circle me-1"></i>Nueva
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Valor</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($fields as $field)
                                    <tr>
                                        <td><code class="text-primary-light">&lt;# {{ $field->name }} #&gt;</code></td>
                                        <td>{{ Str::limit($field->value, 40) }}</td>
                                        <td class="text-end">
                                            <button class="btn btn-outline-custom btn-sm border-0" data-bs-toggle="modal" data-bs-target="#editModal{{ $field->id }}">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">No hay variables configuradas.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Tab -->
            <div class="tab-pane fade" id="contentTemplate" role="tabpanel">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark-2 py-3 d-flex justify-content-between align-items-center">
                        <span class="text-white fw-bold">Vista Previa del Contenido</span>
                        <span class="badge bg-primary">{{ ucfirst($template->category) }}</span>
                    </div>
                    <div class="card-body">
                        <div class="bg-dark-3 p-4 rounded-3 text-white border border-secondary" style="min-height: 400px; white-space: pre-wrap;">{{ $template->content }}</div>
                    </div>
                </div>
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
                <h6 class="text-white small fw-bold mb-2">Variables de Plantilla</h6>
                <p class="text-white  extra-small mb-3">Configuradas en la pestaña de variables.</p>
                
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

<!-- Modals for Variables -->
@foreach($fields as $field)
<div class="modal fade" id="editModal{{ $field->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('company-fields.update', $field) }}" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title text-white">Editar Variable</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-control" value="{{ $field->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valor</label>
                        <textarea name="value" class="form-control" rows="3">{{ $field->value }}</textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_bold" class="form-check-input" id="isBoldCheck{{ $field->id }}" {{ $field->is_bold ? 'checked' : '' }}>
                        <label class="form-check-label text-white small" for="isBoldCheck{{ $field->id }}">Mostrar en Negrita</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<div class="modal fade" id="addVariableModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('company-fields.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-white">Nueva Variable Global</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="document_template_id" value="{{ $template->id }}">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Variable</label>
                        <input type="text" name="name" class="form-control" placeholder="Ej: Representante Legal" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valor</label>
                        <textarea name="value" class="form-control" rows="3" placeholder="Ej: Juan Pérez"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_bold" class="form-check-input" id="isBoldCheckNew">
                        <label class="form-check-label text-white small" for="isBoldCheckNew">Mostrar en Negrita</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Guardar Variable</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
