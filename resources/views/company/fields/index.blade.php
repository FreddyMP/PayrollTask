@extends('layouts.app')

@section('title', 'Variables de la Empresa')
@section('page-title', 'Variables Globales')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h5 class="text-white mb-1">Configuración de Variables</h5>
        <p class="  small">Define valores que se usarán en tus documentos y contratos (ej: <# Representante Legal #>).</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark-2 py-3"><span class="text-white fw-bold">Agregar Variable</span></div>
            <div class="card-body">
                <form action="{{ route('company-fields.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Variable</label>
                        <input type="text" name="name" class="form-control" placeholder="Ej: Representante Legal" required>
                        <small class="text-muted">Usa este nombre exacto entre <# y #> en tus documentos.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Valor</label>
                        <textarea name="value" class="form-control" rows="3" placeholder="Ej: Juan Pérez"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_bold" class="form-check-input" id="isBoldCheck">
                        <label class="form-check-label text-white small" for="isBoldCheck">Mostrar en Negrita</label>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary-custom py-2">
                            <i class="bi bi-plus-circle me-2"></i>Guardar Variable
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark-2 py-3">
                <span class="text-white fw-bold">Variables Registradas</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nombre (Tag)</th>
                                <th>Valor Actual</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fields as $field)
                            <tr>
                                <td>
                                    <code class="text-primary-light">&lt;# {{ $field->name }} #&gt;</code>
                                    @if($field->is_bold)
                                        <span class="badge bg-dark-3 text-dark border border-secondary ms-2" style="font-size: 0.6rem;">Negrita</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($field->value, 50) }}</td>
                                <td class="text-end">
                                    <button class="btn btn-outline-custom btn-sm border-0" data-bs-toggle="modal" data-bs-target="#editModal{{ $field->id }}">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <form action="{{ route('company-fields.destroy', $field) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm border-0" onclick="return confirm('¿Eliminar esta variable?')">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
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
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">No has definido variables globales.</td>
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
