@extends('layouts.app')
@section('title', 'Posiciones')
@section('page-title', 'Posiciones')

@section('content')
<ul class="nav nav-tabs mb-4 px-3" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('departments.index') }}" style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
            Departamentos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('positions.index') }}" style="color: white; border-bottom: 2px solid var(--primary); background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
            Posiciones
        </a>
    </li>
</ul>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="text-white mb-1">Posiciones</h5>
            <p class="small mb-0">Gestión de posiciones y puestos de trabajo</p>
        </div>
        <a href="{{ route('positions.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-2"></i>Nueva Posición
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark-2 py-3 border-0">
                <i class="bi bi-person-badge me-2"></i>
                <span class="text-white fw-bold">Listado de Posiciones</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-dark-2">
                            <tr>
                                <th>Título</th>
                                <th>Departamento</th>
                                <th>Código</th>
                                <th>Salario Base</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($positions as $position)
                            <tr>
                                <td>
                                    <span class="d-block fw-semibold">{{ $position->title }}</span>
                                    @if($position->description)
                                    <small class="text-muted">{{ Str::limit($position->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $position->department ? $position->department->name : 'N/A' }}</td>
                                <td>{{ $position->code ?? 'N/A' }}</td>
                                <td>{{ $position->base_salary ? '$' . number_format($position->base_salary, 2) : 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $position->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $position->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('positions.edit', $position) }}" class="btn btn-sm btn-outline-custom">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('positions.destroy', $position) }}" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta posición?');">
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
                                <td colspan="6" class="text-center py-5">
                                    No hay posiciones registradas.
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
