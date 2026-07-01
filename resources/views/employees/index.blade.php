@extends('layouts.app')
@section('title', 'Empleados')
@section('page-title', 'Empleados')

@section('content')
    <!-- Botón de Filtros y Nuevo Empleado -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-outline-custom" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse"
            aria-expanded="false" aria-controls="filterCollapse">
            <i class="bi bi-funnel me-2"></i>Filtros
            @if(request()->hasAny(['search', 'department', 'role', 'contract_type', 'status']))
                <span
                    class="badge bg-primary ms-1">{{ collect(request()->only(['search', 'department', 'role', 'contract_type', 'status']))->filter()->count() }}</span>
            @endif
        </button>
        <a href="{{ route('employees.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-person-plus me-1"></i> Nuevo Empleado
        </a>
    </div>

    <!-- Área de Filtros Colapsable -->
    <div class="collapse {{ request()->hasAny(['search', 'department', 'role', 'contract_type', 'status']) ? 'show' : '' }}"
        id="filterCollapse">
        <div class="card mb-4" style="background-color: #1e293b; border: 1px solid #334155;">
            <div class="card-body">
                <form method="GET" action="{{ route('employees.index') }}" id="filterForm">
                    <div class="row g-3">
                        <!-- Búsqueda por nombre/email -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label" style="font-size: 0.875rem; color: #94a3b8;">
                                <i class="bi bi-search me-1"></i>Buscar
                            </label>
                            <input type="text" class="form-control" name="search" placeholder="Nombre o email..."
                                value="{{ request('search') }}"
                                style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                        </div>

                        <!-- Filtro por Departamento -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label" style="font-size: 0.875rem; color: #94a3b8;">
                                <i class="bi bi-building me-1"></i>Departamento
                            </label>
                            <select class="form-select" name="department"
                                style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                                <option value="">Todos los departamentos</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro por Rol -->
                        <div class="col-md-6 col-lg-2">
                            <label class="form-label" style="font-size: 0.875rem; color: #94a3b8;">
                                <i class="bi bi-person-badge me-1"></i>Rol
                            </label>
                            <select class="form-select" name="role"
                                style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                                <option value="">Todos los roles</option>
                                <option value="usuario" {{ request('role') == 'usuario' ? 'selected' : '' }}>Usuario</option>
                                <option value="supervisor" {{ request('role') == 'supervisor' ? 'selected' : '' }}>Supervisor
                                </option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="super" {{ request('role') == 'super' ? 'selected' : '' }}>Super</option>
                            </select>
                        </div>

                        <!-- Filtro por Tipo de Contrato -->
                        <div class="col-md-6 col-lg-2">
                            <label class="form-label" style="font-size: 0.875rem; color: #94a3b8;">
                                <i class="bi bi-file-text me-1"></i>Contrato
                            </label>
                            <select class="form-select" name="contract_type"
                                style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                                <option value="">Todos los tipos</option>
                                <option value="full_time" {{ request('contract_type') == 'full_time' ? 'selected' : '' }}>
                                    Tiempo Completo</option>
                                <option value="part_time" {{ request('contract_type') == 'part_time' ? 'selected' : '' }}>
                                    Medio Tiempo</option>
                                <option value="contractor" {{ request('contract_type') == 'contractor' ? 'selected' : '' }}>
                                    Contratista</option>
                            </select>
                        </div>

                        <!-- Filtro por Estado -->
                        <div class="col-md-6 col-lg-2">
                            <label class="form-label" style="font-size: 0.875rem; color: #94a3b8;">
                                <i class="bi bi-toggle-on me-1"></i>Estado
                            </label>
                            <select class="form-select" name="status"
                                style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                                <option value="">Todos los estados</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivo
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-search me-1"></i>Aplicar Filtros
                        </button>
                        <a href="{{ route('employees.index') }}" class="btn btn-outline-custom">
                            <i class="bi bi-x-circle me-1"></i>Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Rol</th>
                            <th>Cargo</th>
                            <th>Salario</th>
                            <th>Contrato</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $emp)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div
                                            style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #6366f1, #a855f7); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: white; font-weight: 700;">
                                            {{ strtoupper(substr($emp->user->name ?? '', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $emp->user->name ?? '—' }}</div>
                                            <div style="font-size: 0.75rem; color: #64748b;">{{ $emp->user->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span
                                        class="badge-status badge-{{ $emp->user->role ?? '' }}">{{ ucfirst($emp->user->role ?? '') }}</span>
                                </td>
                                <td>{{ $emp->position?->title ?? $emp->user->position ?? '—' }}</td>
                                <td style="font-weight: 600;">RD$ {{ number_format($emp->salary, 2) }}</td>
                                <td>
                                    @php
                                        $contractLabels = ['full_time' => 'Tiempo Completo', 'part_time' => 'Medio Tiempo', 'contractor' => 'Contratista'];
                                    @endphp
                                    {{ $contractLabels[$emp->contract_type] ?? $emp->contract_type }}
                                </td>
                                <td><span
                                        class="badge-status badge-{{ $emp->user->status ?? '' }}">{{ ucfirst($emp->user->status ?? '') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('employees.show', $emp) }}" class="btn btn-outline-custom btn-sm"
                                            title="Ver"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('employees.edit', $emp) }}" class="btn btn-outline-custom btn-sm"
                                            title="Editar"><i class="bi bi-pencil"></i></a>
                                        <form method="POST" action="{{ route('employees.destroy', $emp) }}" class="d-inline"
                                            onsubmit="return confirm('¿Eliminar este empleado?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-custom btn-sm" style="color: #f87171;"
                                                title="Eliminar"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-dark py-4">No hay empleados registrados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $employees->links() }}
    </div>

    <style>
        .form-select option {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        .collapse.show {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection