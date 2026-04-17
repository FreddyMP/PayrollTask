@extends('layouts.app')
@section('title', 'Reportes')
@section('page-title', 'Centro de Reportes')

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <a href="{{ route('reports.payroll') }}" class="text-decoration-none">
            <div class="stat-card green" style="cursor: pointer;">
                <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-value" style="font-size: 1.3rem;">Gastos Nominales</div>
                <div class="stat-label">Reporte de nóminas y gastos</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('reports.tasks') }}" class="text-decoration-none">
            <div class="stat-card purple" style="cursor: pointer;">
                <div class="stat-icon"><i class="bi bi-kanban"></i></div>
                <div class="stat-value" style="font-size: 1.3rem;">Cumplimiento Tareas</div>
                <div class="stat-label">Reporte de productividad</div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('reports.access') }}" class="text-decoration-none">
            <div class="stat-card blue" style="cursor: pointer;">
                <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                <div class="stat-value" style="font-size: 1.3rem;">Accesos</div>
                <div class="stat-label">Reporte de sesiones</div>
            </div>
        </a>
    </div>
</div>

<div class="row g-3 mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-pie-chart-fill me-2"></i>Distribución de Tareas</div>
            <div class="card-body" style="height: 300px;">
                <canvas id="taskChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-bar-chart-fill me-2"></i>Nómina por Período</div>
            <div class="card-body" style="height: 300px;">
                <canvas id="payrollChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.color = '#94a3b8';
Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';

// Task chart
$.get('/api/reports/chart/tasks', function(data) {
    var labels = { pending: 'Pendiente', in_progress: 'En Progreso', review: 'Revisión', completed: 'Completada', cancelled: 'Cancelada' };
    new Chart($('#taskChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(data).map(k => labels[k] || k),
            datasets: [{
                data: Object.values(data),
                backgroundColor: ['#f59e0b', '#3b82f6', '#a855f7', '#10b981', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });
});

// Payroll chart
$.get('/api/reports/chart/payroll', function(data) {
    new Chart($('#payrollChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(data),
            datasets: [{
                label: 'Nómina Neta',
                data: Object.values(data),
                backgroundColor: 'rgba(99, 102, 241, 0.5)',
                borderColor: '#6366f1',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } } }
    });
});
</script>
@endpush
