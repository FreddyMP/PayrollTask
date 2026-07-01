@extends('layouts.app')

@section('title', 'Reclutamiento')
@section('page-title', 'Reclutamiento')

@section('content')
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="text-white mb-1">Reclutamiento</h5>
                <p class="text-white small mb-0">Gestiona vacantes y configura la hoja de solicitud.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('recruitment.application-form.print', $applicationForm) }}"
                    class="btn btn-outline-custom">
                    <i class="bi bi-printer me-2"></i>Imprimir Hoja
                </a>
                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createVacancyModal">
                    <i class="bi bi-plus-lg me-2"></i>Nueva Vacante
                </button>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs border-dark-3 mb-4" id="recruitmentTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="vacancies-tab" data-bs-toggle="tab" data-bs-target="#vacancies-pane"
                type="button">
                <i class="bi bi-briefcase me-2"></i>Vacantes
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="form-tab" data-bs-toggle="tab" data-bs-target="#form-pane" type="button">
                <i class="bi bi-file-earmark-text me-2"></i>Hoja de Solicitud
            </button>
        </li>
        <div class="tab-content" id="recruitmentTabsContent">
            <!-- Vacancies Tab -->
            <div class="tab-pane fade show active" id="vacancies-pane">
                <!-- Filters Card -->
                <div class="card mb-3">
                    <div class="card-header bg-dark-2 py-3">
                        <button
                            class="btn btn-link text-white text-decoration-none p-0 w-100 text-start d-flex justify-content-between align-items-center"
                            type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="false">
                            <span>
                                <i class="bi bi-funnel me-2"></i>Filtros
                                @if(request()->hasAny(['search', 'status', 'department', 'date_from', 'date_until']))
                                    <span class="badge bg-primary ms-2">Activos</span>
                                @endif
                            </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                    <div class="collapse" id="filtersCollapse">
                        <div class="card-body">
                            <form method="GET" action="{{ route('recruitment.index') }}" id="filterForm">
                                <input type="hidden" name="tab" value="vacancies">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Buscar por título</label>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Buscar vacante..." value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Estado</label>
                                        <select name="status" class="form-select">
                                            <option value="">Todos</option>
                                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Abierta
                                            </option>
                                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>
                                                Cerrada</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Departamento</label>
                                        <select name="department" class="form-select">
                                            <option value="">Todos</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->name }}" {{ request('department') === $dept->name ? 'selected' : '' }}>
                                                    {{ $dept->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Fecha desde</label>
                                        <input type="date" name="date_from" class="form-control"
                                            value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Fecha hasta</label>
                                        <input type="date" name="date_until" class="form-control"
                                            value="{{ request('date_until') }}">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary-custom">
                                            <i class="bi bi-search me-2"></i>Aplicar Filtros
                                        </button>
                                        <a href="{{ route('recruitment.index') }}" class="btn btn-outline-custom">
                                            <i class="bi bi-x-circle me-2"></i>Limpiar Filtros
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Vacante</th>
                                        <th>Departamento</th>
                                        <th>Candidatos</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($vacancies as $vacancy)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="stat-icon bg-primary-light p-2 rounded-3 me-3">
                                                        <i class="bi bi-briefcase-fill"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $vacancy->title }}</div>
                                                        <small
                                                            class="text-dark">{{ Str::limit($vacancy->description, 40) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $vacancy->department ?? 'N/A' }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-status badge-in_progress">{{ $vacancy->candidates_count }}
                                                    postulados</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-status badge-{{ $vacancy->status === 'open' ? 'active' : 'rejected' }}">
                                                    {{ $vacancy->status === 'open' ? 'Abierta' : 'Cerrada' }}
                                                </span>
                                            </td>
                                            <td>{{ $vacancy->created_at->format('d/m/Y') }}</td>
                                            <td class="text-end">
                                                @if($vacancy->status === 'open')
                                                    <a href="{{ route('recruitment.show', $vacancy) }}"
                                                        class="btn btn-outline-custom btn-sm">
                                                        <i class="bi bi-eye me-1"></i> Gestionar
                                                    </a>
                                                @else
                                                    <button class="btn btn-outline-custom btn-sm" disabled>
                                                        <i class="bi bi-lock-fill me-1"></i> Cerrada
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-dark">
                                                <i class="bi bi-inboxes display-4 d-block mb-3 opacity-50"></i>
                                                No hay vacantes registradas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3">
                            {{ $vacancies->links() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Form Tab -->
            <div class="tab-pane fade" id="form-pane">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-dark-2 py-3"><span class="text-white fw-bold">Agregar Campo</span>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('recruitment.application-form.fields.store', $applicationForm) }}"
                                    method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Texto a mostrar (Etiqueta)</label>
                                        <input type="text" name="label" class="form-control"
                                            placeholder="Ej: Información Personal" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tipo de campo</label>
                                        <select name="type" id="fieldTypeSelect" class="form-select"
                                            onchange="toggleColumnsInput(this.value)" required>
                                            <option value="text">Texto corto (Línea)</option>
                                            <option value="long_text">Texto largo (Línea extendida)</option>
                                            <option value="textarea">Área de texto (Párrafo)</option>
                                            <option value="date">Fecha</option>
                                            <option value="integer">Número entero</option>
                                            <option value="decimal">Número decimal</option>
                                            <option value="table">Tabla de datos</option>
                                        </select>
                                    </div>
                                    <div id="columnsInputContainer" class="mb-4 d-none">
                                        <label class="form-label">Columnas de la tabla (Separadas por coma)</label>
                                        <input type="text" name="columns" class="form-control"
                                            placeholder="Ej: Empresa, Cargo, Responsabilidades">
                                    </div>
                                    <div class="d-grid mt-4">
                                        <button type="submit" class="btn btn-primary-custom py-2">
                                            <i class="bi bi-plus-circle me-2"></i>Agregar Campo
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-dark-2 py-3 d-flex justify-content-between align-items-center">
                                <span class="text-white fw-bold">Estructura de la Hoja</span>
                                <span class="badge bg-primary">{{ $applicationForm->fields->count() }} campos</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-dark-3">
                                            <tr>
                                                <th class="ps-3" style="width: 50px;">#</th>
                                                <th>Campo / Etiqueta</th>
                                                <th style="width: 150px;">Tipo</th>
                                                <th class="text-end pe-3">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($applicationForm->fields as $field)
                                                <tr>
                                                    <td class="ps-3 text-dark">{{ $loop->iteration }}</td>
                                                    <td>
                                                        <div class="text-dark fw-medium">{{ $field->label }}</div>
                                                        @if($field->type === 'table')
                                                            <div class="mt-1">
                                                                @foreach($field->options['columns'] ?? [] as $col)
                                                                    <span class="badge bg-dark-3 text-white border border-secondary"
                                                                        style="font-size: 0.65rem;">{{ $col }}</span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge badge-status badge-supervisor">{{ ucfirst(str_replace('_', ' ', $field->type)) }}</span>
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <form
                                                            action="{{ route('recruitment.application-form.fields.destroy', $field) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm border-0"
                                                                onclick="return confirm('¿Eliminar este campo?')">
                                                                <i class="bi bi-trash3-fill"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-5 text-white">No has configurado
                                                        campos para tu hoja de solicitud.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Vacancy Modal -->
        <div class="modal fade" id="createVacancyModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('recruitment.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title text-white">Nueva Vacante</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Título de la vacante</label>
                                <input type="text" name="title" class="form-control"
                                    placeholder="Ej: Desarrollador Laravel Senior" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Departamento</label>
                                <select name="department" class="form-select">
                                    <option value="">Seleccionar departamento...</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="description" class="form-control" rows="4"
                                    placeholder="Describe los requisitos y responsabilidades..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary-custom">Crear Vacante</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @push('scripts')
            <script>
                function toggleColumnsInput(val) {
                    if (val === 'table') {
                        document.getElementById('columnsInputContainer').classList.remove('d-none');
                    } else {
                        document.getElementById('columnsInputContainer').classList.add('d-none');
                    }
                }

                // Toggle chevron icon on collapse
                document.addEventListener('DOMContentLoaded', function () {
                    const collapseElement = document.getElementById('filtersCollapse');
                    const chevronIcon = document.querySelector('[data-bs-target="#filtersCollapse"] i.bi-chevron-down');

                    if (collapseElement && chevronIcon) {
                        collapseElement.addEventListener('show.bs.collapse', function () {
                            chevronIcon.classList.remove('bi-chevron-down');
                            chevronIcon.classList.add('bi-chevron-up');
                        });

                        collapseElement.addEventListener('hide.bs.collapse', function () {
                            chevronIcon.classList.remove('bi-chevron-up');
                            chevronIcon.classList.add('bi-chevron-down');
                        });
                    }

                    // Show filters if any filter is active
                    @if(request()->hasAny(['search', 'status', 'department', 'date_from', 'date_until']))
                        const bsCollapse = new bootstrap.Collapse(collapseElement, {
                            show: true
                        });
                    @endif
            });
            </script>
        @endpush
@endsection