@extends('layouts.app')
@section('title', 'Reporte de Nómina')
@section('page-title', 'Reporte de Gastos Nominales')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.payroll') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Período</label>
                <input type="text" class="form-control form-control-sm" name="period" placeholder="2024-03" value="{{ request('period') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary-custom btn-sm"><i class="bi bi-search me-1"></i> Filtrar</button>
                <a href="{{ route('reports.payroll') }}" class="btn btn-outline-custom btn-sm">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card green">
            <div class="stat-icon"><i class="bi bi-cash"></i></div>
            <div class="stat-value">RD$ {{ number_format($totalGross, 2) }}</div>
            <div class="stat-label">Total Bruto</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card orange">
            <div class="stat-icon"><i class="bi bi-dash-circle"></i></div>
            <div class="stat-value">RD$ {{ number_format($totalDeductions, 2) }}</div>
            <div class="stat-label">Total Deducciones</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card purple">
            <div class="stat-icon"><i class="bi bi-wallet2"></i></div>
            <div class="stat-value">RD$ {{ number_format($totalNet, 2) }}</div>
            <div class="stat-label">Total Neto</div>
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
                        <th>Período</th>
                        <th>Bruto</th>
                        <th>Deducciones</th>
                        <th>Neto</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $p)
                    <tr>
                        <td class="fw-semibold">{{ $p->employee->user->name ?? '—' }}</td>
                        <td>{{ $p->period }}</td>
                        <td>RD$ {{ number_format($p->gross_salary, 2) }}</td>
                        <td style="color: #f87171;">-RD$ {{ number_format($p->deductions, 2) }}</td>
                        <td style="color: var(--success);">RD$ {{ number_format($p->net_salary, 2) }}</td>
                        <td><span class="badge-status badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Sin datos</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3"><a href="{{ route('reports.index') }}" class="btn btn-outline-custom"><i class="bi bi-arrow-left me-1"></i> Volver a Reportes</a></div>
@endsection
