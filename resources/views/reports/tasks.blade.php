@extends('layouts.app')
@section('title', 'Reporte de Tareas')
@section('page-title', 'Reporte de Cumplimiento de Tareas')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.tasks') }}" class="row g-2 align-items-end">
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
    @php $statusLabels = ['pending' => 'Pendiente', 'in_progress' => 'En Progreso', 'review' => 'Revisión', 'completed' => 'Completada', 'cancelled' => 'Cancelada']; @endphp
    @foreach($statusLabels as $key => $label)
    <div class="col">
        <div class="stat-card {{ ['pending'=>'orange','in_progress'=>'blue','review'=>'purple','completed'=>'green','cancelled'=>'orange'][$key] }}">
            <div class="stat-value">{{ $statusCounts[$key] ?? 0 }}</div>
            <div class="stat-label">{{ $label }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header text-secondary"><i class="bi bi-people me-2"></i>Cumplimiento por Usuario</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Total Tareas</th>
                        <th>Completadas</th>
                        <th>% Cumplimiento</th>
                        <th>Progreso</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($userTasks as $ut)
                    @if($ut->assignedUser)
                    @php $pct = $ut->total > 0 ? round(($ut->completed / $ut->total) * 100) : 0; @endphp
                    <tr>
                        <td class="fw-semibold">{{ $ut->assignedUser->name }}</td>
                        <td>{{ $ut->total }}</td>
                        <td>{{ $ut->completed }}</td>
                        <td>{{ $pct }}%</td>
                        <td style="width: 200px;">
                            <div style="background: var(--dark-3); border-radius: 10px; height: 8px; overflow: hidden;">
                                <div style="width: {{ $pct }}%; height: 100%; background: var(--gradient-1); border-radius: 10px; transition: width 0.5s;"></div>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Sin datos</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3"><a href="{{ route('reports.index') }}" class="btn btn-outline-custom"><i class="bi bi-arrow-left me-1"></i> Volver a Reportes</a></div>
@endsection
