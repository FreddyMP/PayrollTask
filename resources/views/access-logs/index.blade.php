@extends('layouts.app')
@section('title', 'Registro de Accesos')
@section('page-title', 'Registro de Accesos')

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between text-white align-items-center">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filtros de Búsqueda</h6>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
                data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body">
                <form method="GET" action="{{ route('access-logs.index') }}" id="filterForm">
                    <div class="row g-3">
                        <!-- Búsqueda por Usuario -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-person me-1"></i>Buscar Usuario</label>
                            <input type="text" class="form-control form-control-sm" name="search_user"
                                value="{{ request('search_user') }}" placeholder="Nombre o email del usuario...">
                        </div>

                        <!-- Filtro por Acción/Evento -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-activity me-1"></i>Acción/Evento</label>
                            <select class="form-select form-select-sm" name="action">
                                <option value="">Todas las acciones</option>
                                <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                                <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                                <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Crear</option>
                                <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Actualizar
                                </option>
                                <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Eliminar</option>
                            </select>
                        </div>

                        <!-- Filtro por Usuario (select) -->
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-people me-1"></i>Usuario</label>
                            <select class="form-select form-select-sm" name="user_id">
                                <option value="">Todos</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Rango de Fechas -->
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-calendar-range me-1"></i>Desde</label>
                            <input type="date" class="form-control form-control-sm" name="date_from"
                                value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-calendar-check me-1"></i>Hasta</label>
                            <input type="date" class="form-control form-control-sm" name="date_to"
                                value="{{ request('date_to') }}">
                        </div>

                        <!-- Filtro por IP -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-hdd-network me-1"></i>Dirección IP</label>
                            <input type="text" class="form-control form-control-sm" name="ip_address"
                                value="{{ request('ip_address') }}" placeholder="Ej: 192.168.1.1">
                        </div>

                        <!-- Botones -->
                        <div class="col-md-6 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary-custom btn-sm flex-grow-1">
                                <i class="bi bi-search me-1"></i> Filtrar
                            </button>
                            <a href="{{ route('access-logs.index') }}" class="btn btn-outline-secondary btn-sm flex-grow-1">
                                <i class="bi bi-x-circle me-1"></i> Limpiar
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
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Inicio de Sesión</th>
                            <th>Estado</th>
                            <th>Cierre de Sesión</th>
                            <th>Duración</th>
                            <th>Dispositivo / IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="fw-semibold">{{ $log->user->name ?? '—' }}</td>
                                <td><span
                                        class="badge-status badge-{{ $log->user->role ?? '' }}">{{ ucfirst($log->user->role ?? '') }}</span>
                                </td>
                                <td>{{ $log->login_at->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    @php
                                        $status = $log->attendance_status;
                                        $badgeClass = $status === 'Puntual' ? 'success' : ($status === 'Tarde' ? 'warning' : 'danger');
                                    @endphp
                                    <span
                                        class="badge bg-{{ $badgeClass }} bg-opacity-10 text-{{ $badgeClass }} border border-{{ $badgeClass }} border-opacity-25"
                                        style="font-size: 0.7rem;">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td>{!! $log->logout_at ? $log->logout_at->format('d/m/Y H:i:s') : '<span class="badge-status badge-active">Activo</span>' !!}
                                </td>
                                <td>
                                    @if($log->logout_at)
                                        {{ $log->login_at->diff($log->logout_at)->format('%Hh %Im') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td style="font-size: 0.85rem;">
                                    @if($log->device_name !== $log->ip_address)
                                        <div class="fw-semibold text-secondary"><i
                                                class="bi bi-laptop me-1 small "></i>{{ $log->device_name }}</div>
                                        <div class="text-white small" style="font-family: monospace; font-size: 0.7rem;">
                                            {{ $log->ip_address }}</div>
                                    @else
                                        <span style="font-family: monospace; color: #94a3b8;">{{ $log->ip_address ?? '—' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-white py-4">No hay registros</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $logs->links() }}</div>
@endsection