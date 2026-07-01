@extends('layouts.app')

@section('title', 'Generación de Documentos')
@section('page-title', 'Generación de Documentos')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="text-white mb-1">Plantillas Disponibles</h5>
            <p class="small">Gestiona tus borradores y genera documentos con variables automáticas.</p>
        </div>
        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
            <i class="bi bi-file-earmark-plus me-2"></i>Nueva Plantilla
        </button>
    </div>
</div>

<!-- Filtros Colapsables -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <button class="btn btn-link text-decoration-none text-white w-100 text-start p-0"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#filterCollapse"
                        aria-expanded="false"
                        aria-controls="filterCollapse">
                    <div class="d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-funnel me-2"></i>Filtros</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                </button>
            </div>
            <div class="collapse" id="filterCollapse">
                <div class="card-body">
                    <form action="{{ route('documents.index') }}" method="GET">
                        <div class="row g-3">
                            <!-- Búsqueda por título -->
                            <div class="col-md-4">
                                <label class="form-label small">Buscar por Título</label>
                                <input type="text"
                                       name="search"
                                       class="form-control"
                                       placeholder="Título del documento..."
                                       value="{{ request('search') }}">
                            </div>

                            <!-- Filtro por categoría -->
                            <div class="col-md-4">
                                <label class="form-label small">Categoría</label>
                                <select name="category" class="form-select">
                                    <option value="">Todas las categorías</option>
                                    <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>General</option>
                                    <option value="contrato" {{ request('category') === 'contrato' ? 'selected' : '' }}>Contrato</option>
                                    <option value="certificacion" {{ request('category') === 'certificacion' ? 'selected' : '' }}>Certificación</option>
                                    <option value="amonestacion" {{ request('category') === 'amonestacion' ? 'selected' : '' }}>Amonestación</option>
                                </select>
                            </div>

                            <!-- Filtro por fecha -->
                            <div class="col-md-4">
                                <label class="form-label small">Fecha de Creación</label>
                                <input type="date"
                                       name="date"
                                       class="form-control"
                                       value="{{ request('date') }}">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12 d-flex gap-2 justify-content-end">
                                <a href="{{ route('documents.index') }}" class="btn btn-outline-custom btn-sm">
                                    <i class="bi bi-x-circle me-1"></i>Limpiar
                                </a>
                                <button type="submit" class="btn btn-primary-custom btn-sm">
                                    <i class="bi bi-search me-1"></i>Aplicar Filtros
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @forelse($templates as $template)
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon bg-primary-light p-2 rounded-3 me-3">
                        <i class="bi bi-file-earmark-text-fill text-white"></i>
                    </div>
                    <div>
                        <h6 class="text-white mb-0">{{ $template->title }}</h6>
                        <small class="badge badge-status badge-supervisor">{{ ucfirst($template->category) }}</small>
                    </div>
                </div>
                <p class="text-white small mb-4">
                    {{ Str::limit(strip_tags($template->content), 100) }}
                </p>
                <div class="d-flex gap-2">
                    <a href="{{ route('documents.show', $template) }}" class="btn btn-primary-custom btn-sm flex-grow-1">
                        <i class="bi bi-gear-fill me-1"></i> Gestionar
                    </a>
                    <form action="{{ route('documents.destroy', $template) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar esta plantilla?')">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <div class="">
            <i class="bi bi-file-earmark-break display-1 opacity-25"></i>
            <p class="mt-3">No tienes plantillas creadas.</p>
        </div>
    </div>
    @endforelse
</div>

<!-- Create Template Modal -->
<div class="modal fade" id="createTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-white">Nueva Plantilla de Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Título del Documento</label>
                            <input type="text" name="title" class="form-control" placeholder="Ej: Contrato de Trabajo Indefinido" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoría</label>
                            <select name="category" class="form-select">
                                <option value="general">General</option>
                                <option value="contrato">Contrato</option>
                                <option value="certificacion">Certificación</option>
                                <option value="amonestacion">Amonestación</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Archivo de Plantilla (.docx)</label>
                        <input type="file" name="file" class="form-control" accept=".docx">
                        <div class="mt-1 extra-small ">Sube un archivo Word con etiquetas tipo <code>&lt;# variable #&gt;</code>.</div>
                    </div>

                    <div class="text-center my-3">
                        <span class=" small">--- O escribe el contenido ---</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contenido HTML/Texto</label>
                        <textarea name="content" class="form-control" rows="8" placeholder="O pega el contenido aquí..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Guardar Plantilla</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
