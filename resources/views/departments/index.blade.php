@extends('layouts.app')
@section('title', 'Departamentos')
@section('page-title', 'Departamentos')

@section('content')
    @include('departments.partials.tabs', ['activeTab' => 'departments'])

    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="text-white mb-1">Departamentos</h5>
                <p class="small mb-0">Gestión de departamentos y su jerarquía</p>
            </div>
            <a href="{{ route('departments.create') }}" class="btn btn-primary-custom">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Departamento
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Filtros Colapsables -->
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-dark-2 py-3 border-0">
                    <button class="btn btn-link text-white text-decoration-none p-0 w-100 text-start" type="button"
                        data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="false"
                        aria-controls="filtersCollapse">
                        <i class="bi bi-funnel me-2"></i>
                        <span class="fw-bold">Filtros</span>
                        <i class="bi bi-chevron-down float-end"></i>
                    </button>
                </div>
                <div class="collapse" id="filtersCollapse">
                    <div class="card-body">
                        <form method="GET" action="{{ route('departments.index') }}">
                            <div class="row g-3">
                                <!-- Búsqueda por nombre -->
                                <div class="col-md-4">
                                    <label for="search" class="form-label">Buscar por nombre</label>
                                    <input type="text" class="form-control" id="search" name="search"
                                        value="{{ request('search') }}" placeholder="Nombre del departamento">
                                </div>

                                <!-- Filtro por Estado -->
                                <div class="col-md-4">
                                    <label for="status" class="form-label">Estado</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">Todos</option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                </div>

                                <!-- Filtro por Departamento Padre -->
                                <div class="col-md-4">
                                    <label for="parent_id" class="form-label">Departamento Padre</label>
                                    <select class="form-select" id="parent_id" name="parent_id">
                                        <option value="">Todos</option>
                                        <option value="null" {{ request('parent_id') === 'null' ? 'selected' : '' }}>Sin padre
                                            (Raíz)</option>
                                        @foreach($allDepartments as $dept)
                                            <option value="{{ $dept->id }}" {{ request('parent_id') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary-custom me-2">
                                        <i class="bi bi-search me-1"></i>Buscar
                                    </button>
                                    <a href="{{ route('departments.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Limpiar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabla de Departamentos -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark-2 py-3 border-0">
                    <i class="bi bi-diagram-3 me-2"></i>
                    <span class="text-white fw-bold">Listado de Departamentos</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-dark-2">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Departamento Padre</th>
                                    <th>Descripción</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($departments->count() > 0)
                                    <tr class="bg-dark-2">
                                        <td colspan="5" class="py-2">
                                            <small class="text-white">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Mostrando {{ $departments->count() }} departamento(s)
                                            </small>
                                        </td>
                                    </tr>
                                @endif
                                @forelse($departments as $department)
                                    <tr>
                                        <td>
                                            <span class="d-block  fw-semibold">{{ $department->name }}</span>
                                            @if($department->childDepartments->count() > 0)
                                                <small class="text-muted">{{ $department->childDepartments->count() }}
                                                    subdepartamento(s)</small>
                                            @endif
                                        </td>
                                        <td>{{ $department->parentDepartment ? $department->parentDepartment->name : 'N/A' }}
                                        </td>
                                        <td>{{ $department->description ? Str::limit($department->description, 50) : 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $department->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $department->is_active ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('departments.edit', $department) }}"
                                                    class="btn btn-sm btn-outline-custom">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('departments.destroy', $department) }}" method="POST"
                                                    onsubmit="return confirm('¿Está seguro de eliminar este departamento?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-dark">
                                            No hay departamentos registrados.
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