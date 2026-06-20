@extends('layouts.app')
@section('title', 'Departamentos')
@section('page-title', 'Departamentos')

@section('content')
<ul class="nav nav-tabs mb-4 px-3" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('departments.index') }}" style="color: white; border-bottom: 2px solid var(--primary); background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
            Departamentos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('positions.index') }}" style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
            Posiciones
        </a>
    </li>
</ul>

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
                            @forelse($departments as $department)
                            <tr>
                                <td>
                                    <span class="d-block  fw-semibold">{{ $department->name }}</span>
                                    @if($department->childDepartments->count() > 0)
                                    <small class="text-muted">{{ $department->childDepartments->count() }} subdepartamento(s)</small>
                                    @endif
                                </td>
                                <td >{{ $department->parentDepartment ? $department->parentDepartment->name : 'N/A' }}</td>
                                <td >{{ $department->description ? Str::limit($department->description, 50) : 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $department->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $department->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-outline-custom">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('departments.destroy', $department) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar este departamento?');">
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
                                <td colspan="5" class="text-center py-5 text-white">
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
