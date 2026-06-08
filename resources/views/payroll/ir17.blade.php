@extends('layouts.app')
@section('title', 'ISR IR-17 - Retenciones de Asalariados')
@section('page-title', 'Reporte IR-17 (DGII)')

@section('content')
<ul class="nav nav-tabs mb-4 px-3" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('payroll.index') }}" style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
            Registros de Nómina
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('payroll.bonuses') }}" style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
            Bonificaciones de Ley
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('payroll.benefits') }}" style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
            Prestaciones Laborales
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('payroll.christmas') }}" style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
            Salario Navidad
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('payroll.tss') }}" style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
            TSS
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('payroll.ir17') }}" style="color: white; border-bottom: 2px solid var(--primary); background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
            IR-17
        </a>
    </li>
</ul>

<div class="d-flex justify-content-between align-items-center mb-4">
    <form action="{{ route('payroll.ir17') }}" method="GET" class="d-flex gap-2 align-items-center">
        <label class="text-secondary small me-2">Período:</label>
        <select name="period" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
            @foreach($availablePeriods as $p)
            @php
                $basePeriod = preg_replace('/-Q[12]$/', '', $p);
                $periodLabel = \Carbon\Carbon::parse($basePeriod)->translatedFormat('F Y');
                if (str_ends_with($p, '-Q1')) {
                    $periodLabel .= ' — 1ª Quincena';
                } elseif (str_ends_with($p, '-Q2')) {
                    $periodLabel .= ' — 2ª Quincena';
                }
            @endphp
            <option value="{{ $p }}" {{ $period == $p ? 'selected' : '' }}>
                {{ $periodLabel }}
            </option>
            @endforeach
        </select>
    </form>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-custom btn-sm">
            <i class="bi bi-printer me-1"></i> Imprimir
        </button>
        <button onclick="exportCSV()" class="btn btn-primary-custom btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar CSV
        </button>
    </div>
</div>

{{-- Encabezado del formulario IR-17 --}}
<div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(168, 85, 247, 0.05));">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="text-white mb-1"><i class="bi bi-bank me-2"></i>Declaración Jurada IR-17</h5>
                <p class="small mb-0" style="color: #94a3b8;">Declaración Jurada Mensual del Impuesto Sobre la Renta — Retenciones de Asalariados (DGII)</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="small text-white"><strong>RNC:</strong> {{ $company->rnc ?? '—' }}</div>
                <div class="small text-white"><strong>Empresa:</strong> {{ $company->name }}</div>
                @php
                    $basePeriodDisplay = preg_replace('/-Q[12]$/', '', $period);
                    $periodLabelDisplay = \Carbon\Carbon::parse($basePeriodDisplay)->translatedFormat('F Y');
                    if (str_ends_with($period, '-Q1')) {
                        $periodLabelDisplay .= ' — 1ª Quincena';
                    } elseif (str_ends_with($period, '-Q2')) {
                        $periodLabelDisplay .= ' — 2ª Quincena';
                    }
                @endphp
                <div class="small" style="color: #94a3b8;"><strong>Período:</strong> {{ $periodLabelDisplay }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Info de escala ISR --}}
<div class="alert border-0 shadow-sm mb-4" style="background: rgba(251, 191, 36, 0.08); color: #fbbf24;">
    <div class="d-flex">
        <i class="bi bi-calculator-fill me-3 fs-4"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1" style="color: #fbbf24;">Escala Progresiva ISR Vigente (Anualizada)</h6>
            <p class="mb-0 small" style="color: #d4a534;">
                <strong>Exento:</strong> Hasta RD$ 416,220.00 |
                <strong>15%:</strong> RD$ 416,220.01 – RD$ 624,329.00 |
                <strong>20%:</strong> RD$ 624,329.01 – RD$ 867,123.00 |
                <strong>25%:</strong> RD$ 867,123.01 en adelante
            </p>
        </div>
    </div>
</div>

{{-- Tabla principal IR-17 --}}
<div class="card shadow-sm">
    <div class="card-body p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="color: #e2e8f0;" id="ir17Table">
                <thead style="background: rgba(255,255,255,0.02); border-bottom: 2px solid rgba(255,255,255,0.05);">
                    <tr>
                        <th class="ps-4" style="min-width: 120px;">Cédula / RNC</th>
                        <th style="min-width: 180px;">Nombre del Asalariado</th>
                        <th class="text-end">Remuneración Bruta</th>
                        <th class="text-end">Otros Ingresos</th>
                        <th class="text-end" style="background: rgba(52, 211, 153, 0.05);">SFS (Emp.)</th>
                        <th class="text-end" style="background: rgba(96, 165, 250, 0.05);">AFP (Emp.)</th>
                        <th class="text-end">Total TSS</th>
                        <th class="text-end">Ingreso Gravable</th>
                        <th class="text-center">Tramo</th>
                        <th class="text-end pe-4" style="background: rgba(251, 191, 36, 0.05);">ISR Retenido</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report as $row)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="ps-4">
                            <span class="badge bg-dark bg-opacity-50 fw-normal" style="font-size: 0.8rem;">{{ $row['cedula'] }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $row['nombre'] }}</div>
                        </td>
                        <td class="text-end">RD$ {{ number_format($row['remuneracion_bruta'], 2) }}</td>
                        <td class="text-end">
                            @if($row['otros_ingresos'] > 0)
                                <span style="color: #34d399;">RD$ {{ number_format($row['otros_ingresos'], 2) }}</span>
                            @else
                                <span class="text-white">—</span>
                            @endif
                        </td>
                        <td class="text-end" style="background: rgba(52, 211, 153, 0.02);">{{ number_format($row['sfs'], 2) }}</td>
                        <td class="text-end" style="background: rgba(96, 165, 250, 0.02);">{{ number_format($row['afp'], 2) }}</td>
                        <td class="text-end">{{ number_format($row['total_tss'], 2) }}</td>
                        <td class="text-end fw-semibold">RD$ {{ number_format($row['ingreso_gravable'], 2) }}</td>
                        <td class="text-center">
                            @php
                                $tramoBadge = match($row['tramo']) {
                                    'Exento' => 'bg-success bg-opacity-10 text-success',
                                    '15%' => 'bg-info bg-opacity-10 text-info',
                                    '20%' => 'bg-warning bg-opacity-10 text-warning',
                                    '25%' => 'bg-danger bg-opacity-10 text-danger',
                                    default => 'bg-secondary bg-opacity-10 text-secondary',
                                };
                            @endphp
                            <span class="badge {{ $tramoBadge }}" style="font-size: 0.8rem;">{{ $row['tramo'] }}</span>
                        </td>
                        <td class="text-end pe-4" style="background: rgba(251, 191, 36, 0.02);">
                            @if($row['isr_retenido'] > 0)
                                <span class="fw-bold" style="color: #fbbf24;">RD$ {{ number_format($row['isr_retenido'], 2) }}</span>
                            @else
                                <span class="text-success fw-semibold">Exento</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-white">
                            <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                            No hay registros de nómina para el período {{ $periodLabelDisplay }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($report->count() > 0)
                <tfoot style="background: rgba(255,255,255,0.03); border-top: 2px solid rgba(255,255,255,0.1);">
                    <tr class="fw-bold border-0">
                        <td class="ps-4" colspan="2">TOTALES ({{ $report->count() }} empleado{{ $report->count() > 1 ? 's' : '' }})</td>
                        <td class="text-end">RD$ {{ number_format($report->sum('remuneracion_bruta'), 2) }}</td>
                        <td class="text-end">RD$ {{ number_format($report->sum('otros_ingresos'), 2) }}</td>
                        <td class="text-end">{{ number_format($report->sum('sfs'), 2) }}</td>
                        <td class="text-end">{{ number_format($report->sum('afp'), 2) }}</td>
                        <td class="text-end">{{ number_format($report->sum('total_tss'), 2) }}</td>
                        <td class="text-end">RD$ {{ number_format($report->sum('ingreso_gravable'), 2) }}</td>
                        <td></td>
                        <td class="text-end pe-4 text-warning" style="font-size: 1.1rem;">
                            RD$ {{ number_format($report->sum('isr_retenido'), 2) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- Resumen de retenciones --}}
@if($report->count() > 0)
<div class="row g-3 mt-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background: rgba(52, 211, 153, 0.05);">
            <div class="card-body text-center py-3">
                <div class="small text-white mb-1">Empleados Exentos</div>
                <div class="fs-4 fw-bold text-success">{{ $report->where('tramo', 'Exento')->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background: rgba(96, 165, 250, 0.05);">
            <div class="card-body text-center py-3">
                <div class="small text-white mb-1">Tramo 15%</div>
                <div class="fs-4 fw-bold text-info">{{ $report->where('tramo', '15%')->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background: rgba(251, 191, 36, 0.05);">
            <div class="card-body text-center py-3">
                <div class="small text-white mb-1">Tramo 20%</div>
                <div class="fs-4 fw-bold text-warning">{{ $report->where('tramo', '20%')->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm" style="background: rgba(248, 113, 113, 0.05);">
            <div class="card-body text-center py-3">
                <div class="small text-white mb-1">Tramo 25%</div>
                <div class="fs-4 fw-bold" style="color: #f87171;">{{ $report->where('tramo', '25%')->count() }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="background: rgba(251, 191, 36, 0.08);">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-white">Total ISR a Retener (Período)</div>
                        <div class="fs-3 fw-bold text-warning">RD$ {{ number_format($report->sum('isr_retenido'), 2) }}</div>
                    </div>
                    <i class="bi bi-cash-coin fs-1 text-warning opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="background: rgba(99, 102, 241, 0.08);">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-white">Total Nómina Gravable</div>
                        <div class="fs-3 fw-bold text-primary">RD$ {{ number_format($report->sum('ingreso_gravable'), 2) }}</div>
                    </div>
                    <i class="bi bi-graph-up-arrow fs-1 text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="mt-4 text-secondary small">
    <i class="bi bi-shield-check me-1"></i> Reporte generado conforme al formulario IR-17 de la Dirección General de Impuestos Internos (DGII) de República Dominicana.
</div>
@endsection

@push('scripts')
<script>
function exportCSV() {
    const table = document.getElementById('ir17Table');
    if (!table) return;

    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        headers.push('"' + th.textContent.trim().replace(/"/g, '""') + '"');
    });

    const rows = [headers.join(',')];
    table.querySelectorAll('tbody tr').forEach(tr => {
        const cols = [];
        tr.querySelectorAll('td').forEach(td => {
            let text = td.textContent.trim().replace(/\s+/g, ' ').replace(/"/g, '""');
            text = text.replace(/RD\$\s*/g, '').replace(/,/g, '');
            cols.push('"' + text + '"');
        });
        if (cols.length > 0) rows.push(cols.join(','));
    });

    // Add totals row
    const tfoot = table.querySelector('tfoot tr');
    if (tfoot) {
        const cols = [];
        tfoot.querySelectorAll('td').forEach(td => {
            let text = td.textContent.trim().replace(/\s+/g, ' ').replace(/"/g, '""');
            text = text.replace(/RD\$\s*/g, '').replace(/,/g, '');
            cols.push('"' + text + '"');
        });
        rows.push(cols.join(','));
    }

    const csv = '\uFEFF' + rows.join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'IR17_{{ $period }}.csv';
    a.click();
    URL.revokeObjectURL(url);
}
</script>
@endpush
