@extends('layouts.app')
@section('title', 'Registro de Accesos')
@section('page-title', 'Registro de Accesos')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('access-logs.index') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Usuario</label>
                <select class="form-select form-select-sm" name="user_id">
                    <option value="">Todos</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Desde</label>
                <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Hasta</label>
                <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary-custom btn-sm w-100"><i class="bi bi-search me-1"></i> Filtrar</button>
            </div>
        </form>
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
                        <td><span class="badge-status badge-{{ $log->user->role ?? '' }}">{{ ucfirst($log->user->role ?? '') }}</span></td>
                        <td>{{ $log->login_at->format('d/m/Y H:i:s') }}</td>
                        <td>
                            @php
                                $status = $log->attendance_status;
                                $badgeClass = $status === 'Puntual' ? 'success' : ($status === 'Tarde' ? 'warning' : 'danger');
                            @endphp
                            <span class="badge bg-{{ $badgeClass }} bg-opacity-10 text-{{ $badgeClass }} border border-{{ $badgeClass }} border-opacity-25" style="font-size: 0.7rem;">
                                {{ $status }}
                            </span>
                        </td>
                        <td>{!! $log->logout_at ? $log->logout_at->format('d/m/Y H:i:s') : '<span class="badge-status badge-active">Activo</span>' !!}</td>
                        <td>
                            @if($log->logout_at)
                                {{ $log->login_at->diff($log->logout_at)->format('%Hh %Im') }}
                            @else
                                —
                            @endif
                        </td>
                        <td style="font-size: 0.85rem;">
                            @if($log->device_name !== $log->ip_address)
                                <div class="fw-semibold text-secondary"><i class="bi bi-laptop me-1 small "></i>{{ $log->device_name }}</div>
                                <div class="text-muted small" style="font-family: monospace; font-size: 0.7rem;">{{ $log->ip_address }}</div>
                            @else
                                <span style="font-family: monospace; color: #94a3b8;">{{ $log->ip_address ?? '—' }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay registros</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $logs->links() }}</div>
@endsection
