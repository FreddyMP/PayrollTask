@extends('layouts.app')
@section('title', 'Reporte de Accesos')
@section('page-title', 'Reporte de Accesos')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.access') }}" class="row g-2 align-items-end">
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
                <button type="submit" class="btn btn-primary-custom btn-sm"><i class="bi bi-search me-1"></i> Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach($userStats as $stat)
    <div class="col-md-3">
        <div class="stat-card blue">
            <div class="stat-value" style="font-size: 1.2rem;">{{ $stat['user']->name ?? '' }}</div>
            <div class="stat-label">{{ $stat['total_sessions'] }} sesiones — Último: {{ $stat['last_login']->format('d/m H:i') }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Inicio de Sesión</th>
                        <th>Cierre</th>
                        <th>Duración</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="fw-semibold">{{ $log->user->name ?? '—' }}</td>
                        <td>{{ $log->login_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->logout_at ? $log->logout_at->format('d/m/Y H:i') : 'Activo' }}</td>
                        <td>{{ $log->logout_at ? $log->login_at->diff($log->logout_at)->format('%Hh %Im') : '—' }}</td>
                        <td style="font-family: monospace; font-size: 0.8rem;">{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Sin registros</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3"><a href="{{ route('reports.index') }}" class="btn btn-outline-custom"><i class="bi bi-arrow-left me-1"></i> Volver a Reportes</a></div>
@endsection
