@extends('layouts.app')
@section('title', 'Dispositivos')
@section('page-title', 'Dispositivos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="text-white small">
        <i class="bi bi-info-circle me-1"></i> Registre las IPs de los dispositivos autorizados para identificarlos en los registros de acceso.
    </div>
    <a href="{{ route('devices.create') }}" class="btn btn-primary-custom">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Dispositivo
    </a>
</div>

{{-- Card de Filtros Colapsable --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#filtersCard" aria-expanded="false">
        <div>
            <i class="bi bi-funnel text-white me-2"></i>
            <strong class="text-white">Filtros</strong>
            @if(request()->hasAny(['search', 'type', 'status', 'assignment', 'employee_id']))
                <span class="badge bg-primary ms-2">Activos</span>
            @endif
        </div>
        <i class="bi bi-chevron-down"></i>
    </div>
    <div class="collapse" id="filtersCard">
        <div class="card-body">
            <form method="GET" action="{{ route('devices.index') }}" id="filterForm">
                <div class="row g-3">
                    {{-- Búsqueda por nombre/marca --}}
                    <div class="col-md-3">
                        <label for="search" class="form-label small">Buscar</label>
                        <input
                            type="text"
                            class="form-control"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Nombre o marca..."
                        >
                    </div>

                    {{-- Filtro por Tipo --}}
                    <div class="col-md-2">
                        <label for="type" class="form-label small">Tipo</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">Todos</option>
                            <option value="laptop" {{ request('type') == 'laptop' ? 'selected' : '' }}>Laptop</option>
                            <option value="desktop" {{ request('type') == 'desktop' ? 'selected' : '' }}>Desktop</option>
                            <option value="tablet" {{ request('type') == 'tablet' ? 'selected' : '' }}>Tablet</option>
                            <option value="phone" {{ request('type') == 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="otros" {{ request('type') == 'otros' ? 'selected' : '' }}>Otros</option>
                        </select>
                    </div>

                    {{-- Filtro por Estado --}}
                    <div class="col-md-2">
                        <label for="status" class="form-label small">Estado</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="activo" {{ request('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ request('status') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            <option value="mantenimiento" {{ request('status') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                        </select>
                    </div>

                    {{-- Filtro por Asignación --}}
                    <div class="col-md-2">
                        <label for="assignment" class="form-label small">Asignación</label>
                        <select class="form-select" id="assignment" name="assignment">
                            <option value="">Todos</option>
                            <option value="assigned" {{ request('assignment') == 'assigned' ? 'selected' : '' }}>Asignados</option>
                            <option value="unassigned" {{ request('assignment') == 'unassigned' ? 'selected' : '' }}>No asignados</option>
                        </select>
                    </div>

                    {{-- Filtro por Empleado --}}
                    <div class="col-md-3">
                        <label for="employee_id" class="form-label small">Empleado</label>
                        <select class="form-select" id="employee_id" name="employee_id">
                            <option value="">Todos los empleados</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-search me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('devices.index') }}" class="btn btn-outline-custom">
                        <i class="bi bi-x-circle me-1"></i> Limpiar
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
                        <th>Nombre del Dispositivo</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Asignado a</th>
                        <th>Dirección IP</th>
                        <th>Descripción</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $device)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3 sm" style="width: 32px; height: 32px; background: rgba(99, 102, 241, 0.1); color: var(--primary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    @switch($device->type)
                                        @case('laptop')
                                            <i class="bi bi-laptop"></i>
                                            @break
                                        @case('desktop')
                                            <i class="bi bi-pc-display"></i>
                                            @break
                                        @case('tablet')
                                            <i class="bi bi-tablet"></i>
                                            @break
                                        @case('phone')
                                            <i class="bi bi-phone"></i>
                                            @break
                                        @default
                                            <i class="bi bi-cpu"></i>
                                    @endswitch
                                </div>
                                <div>
                                    <span class="fw-semibold d-block">{{ $device->name }}</span>
                                    @if($device->brand)
                                        <small class="text-white">{{ $device->brand }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background: rgba(99, 102, 241, 0.1); color: var(--primary);">
                                {{ ucfirst($device->type) }}
                            </span>
                        </td>
                        <td>
                            @switch($device->status)
                                @case('activo')
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                        <i class="bi bi-check-circle me-1"></i>Activo
                                    </span>
                                    @break
                                @case('inactivo')
                                    <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                        <i class="bi bi-x-circle me-1"></i>Inactivo
                                    </span>
                                    @break
                                @case('mantenimiento')
                                    <span class="badge" style="background: rgba(251, 191, 36, 0.1); color: #fbbf24;">
                                        <i class="bi bi-tools me-1"></i>Mantenimiento
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            @if($device->employee)
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-circle me-2" style="color: var(--primary);"></i>
                                    <span class="small">{{ $device->employee->name }}</span>
                                </div>
                            @else
                                <span class="text-white small opacity-50">
                                    <i class="bi bi-dash-circle me-1"></i>Sin asignar
                                </span>
                            @endif
                        </td>
                        <td><code>{{ $device->ip_address }}</code></td>
                        <td class="text-white small">{{ Str::limit($device->description, 30) }}</td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('devices.edit', $device) }}" class="btn btn-outline-custom btn-sm" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('devices.destroy', $device) }}" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este dispositivo?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-custom btn-sm" title="Eliminar" style="color: #f87171;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-dark py-5">
                            <i class="bi bi-cpu mb-3 d-block" style="font-size: 3rem; opacity: 0.2;"></i>
                            No hay dispositivos registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $devices->links() }}</div>
@endsection
