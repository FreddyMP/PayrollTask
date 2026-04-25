@extends('layouts.app')
@section('title', 'TSS - Tesorería de la Seguridad Social')
@section('page-title', 'Reporte TSS')

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
        <a class="nav-link active" href="{{ route('payroll.tss') }}" style="color: white; border-bottom: 2px solid var(--primary); background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
            TSS
        </a>
    </li>
</ul>

<div class="d-flex justify-content-between align-items-center mb-4">
    <form action="{{ route('payroll.tss') }}" method="GET" class="d-flex gap-2 align-items-center">
        <label class="text-secondary small me-2">Período:</label>
        <select name="period" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
            @foreach($availablePeriods as $p)
            <option value="{{ $p }}" {{ $period == $p ? 'selected' : '' }}>
                {{ \Carbon\Carbon::parse($p)->translatedFormat('F Y') }}
            </option>
            @endforeach
        </select>
    </form>
    <div>
        <button onclick="window.print()" class="btn btn-outline-custom btn-sm">
            <i class="bi bi-printer me-1"></i> Imprimir Reporte
        </button>
    </div>
</div>

<div class="alert alert-info border-0 shadow-sm mb-4" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
    <div class="d-flex">
        <i class="bi bi-info-circle-fill me-3 fs-4"></i>
        <div>
            <h6 class="alert-heading fw-bold mb-1">Información de Tasas y Topes (Ley 2026)</h6>
            <p class="mb-0 small">
                <strong>SFS:</strong> 3.04%/7.09% (Tope RD$ 232,230) | 
                <strong>AFP:</strong> 2.87%/7.10% (Tope RD$ 464,460) | 
                <strong>SRL:</strong> {{ $company->srl_rate ?? '1.10' }}% (Tope RD$ 92,892) | 
                <strong>INFOTEP:</strong> 1%
            </p>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="color: #e2e8f0;">
                <thead style="background: rgba(255,255,255,0.02); border-bottom: 2px solid rgba(255,255,255,0.05);">
                    <tr>
                        <th class="ps-4">Empleado</th>
                        <th>Salario Bruto</th>
                        <th class="text-center" style="background: rgba(52, 211, 153, 0.05);">SFS (Emp/Pat)</th>
                        <th class="text-center" style="background: rgba(96, 165, 250, 0.05);">AFP (Emp/Pat)</th>
                        <th class="text-center">SRL</th>
                        <th class="text-center">INFOTEP</th>
                        <th class="pe-4 text-end">Total TSS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report as $row)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="ps-4">
                            <div class="fw-semibold">{{ $row['employee'] }}</div>
                        </td>
                        <td>RD$ {{ number_format($row['salary'], 2) }}</td>
                        <td class="text-center" style="background: rgba(52, 211, 153, 0.02);">
                            <div class="small">E: {{ number_format($row['sfs_emp'], 2) }}</div>
                            <div class="fw-bold">P: {{ number_format($row['sfs_pat'], 2) }}</div>
                        </td>
                        <td class="text-center" style="background: rgba(96, 165, 250, 0.02);">
                            <div class="small">E: {{ number_format($row['afp_emp'], 2) }}</div>
                            <div class="fw-bold">P: {{ number_format($row['afp_pat'], 2) }}</div>
                        </td>
                        <td class="text-center">
                            {{ number_format($row['srl_pat'], 2) }}
                        </td>
                        <td class="text-center">
                            {{ number_format($row['infotep_pat'], 2) }}
                        </td>
                        <td class="pe-4 text-end">
                            @php 
                                $total = $row['sfs_emp'] + $row['sfs_pat'] + $row['afp_emp'] + $row['afp_pat'] + $row['srl_pat'] + $row['infotep_pat'];
                            @endphp
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold" style="font-size: 0.9rem;">
                                RD$ {{ number_format($total, 2) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                            No hay registros de nómina para el período {{ $period }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($report->count() > 0)
                <tfoot style="background: rgba(255,255,255,0.03); border-top: 2px solid rgba(255,255,255,0.1);">
                    <tr class="fw-bold border-0">
                        <td class="ps-4">TOTALES</td>
                        <td>RD$ {{ number_format($report->sum('salary'), 2) }}</td>
                        <td class="text-center">
                            RD$ {{ number_format($report->sum('sfs_emp') + $report->sum('sfs_pat'), 2) }}
                        </td>
                        <td class="text-center">
                            RD$ {{ number_format($report->sum('afp_emp') + $report->sum('afp_pat'), 2) }}
                        </td>
                        <td class="text-center">RD$ {{ number_format($report->sum('srl_pat'), 2) }}</td>
                        <td class="text-center">RD$ {{ number_format($report->sum('infotep_pat'), 2) }}</td>
                        <td class="pe-4 text-end text-primary" style="font-size: 1.1rem;">
                            @php 
                                $grandTotal = $report->sum('sfs_emp') + $report->sum('sfs_pat') + 
                                             $report->sum('afp_emp') + $report->sum('afp_pat') + 
                                             $report->sum('srl_pat') + $report->sum('infotep_pat');
                            @endphp
                            RD$ {{ number_format($grandTotal, 2) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<div class="mt-4 text-secondary small">
    <i class="bi bi-shield-check me-1"></i> Reporte generado según parámetros de la Tesorería de la Seguridad Social (TSS) de República Dominicana.
</div>
@endsection
