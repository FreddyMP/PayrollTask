@extends('layouts.app')
@section('title', 'Nómina')
@section('page-title', 'Nómina')

@section('content')
<ul class="nav nav-tabs mb-4 px-3" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('payroll.index') }}" style="color: white; border-bottom: 2px solid var(--primary); background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
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
           Exportar TSS
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('payroll.ir17') }}" style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
           Generar IR-17
        </a>
    </li>
</ul>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
        <select id="filterPeriod" class="form-select form-select-sm" style="width: auto;" onchange="filterPayroll()">
            <option value="">Todos los períodos</option>
            @php $periods = \App\Models\Payroll::where('company_id', auth()->user()->company_id)->distinct()->pluck('period'); @endphp
            @foreach($periods as $p)
            <option value="{{ $p }}" {{ request('period') == $p ? 'selected' : '' }}>{{ $p }}</option>
            @endforeach
        </select>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#autoGenerateModal">
            <i class="bi bi-magic me-1"></i> Generar Automáticamente
        </button>
        <a href="{{ route('payroll.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i> Nueva Nómina
        </a>
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
                        <th>Salario Bruto</th>
                        <th>Deducciones</th>
                        <th>Salario Neto</th>
                        <th>Fecha Pago</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $payroll)
                    <tr>
                        <td class="fw-semibold">{{ $payroll->employee->user->name ?? '—' }}</td>
                        <td>{{ $payroll->period }}</td>
                        <td>RD$ {{ number_format($payroll->gross_salary, 2) }}</td>
                        <td style="color: #f87171;">-RD$ {{ number_format($payroll->deductions, 2) }}</td>
                        <td class="fw-semibold" style="color: var(--success);">RD$ {{ number_format($payroll->net_salary, 2) }}</td>
                        <td>{{ $payroll->payment_date?->format('d/m/Y') ?? '—' }}</td>
                        <td><span class="badge-status badge-{{ $payroll->status }}">{{ ucfirst($payroll->status) }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                @if($payroll->status === 'pending')
                                <a href="{{ route('payroll.edit', $payroll) }}" class="btn btn-outline-custom btn-sm" style="color: #60a5fa;" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('payroll.markPaid', $payroll) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline-custom btn-sm" style="color: #34d399;" title="Marcar como pagado">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('payroll.destroy', $payroll) }}" class="d-inline" onsubmit="return confirm('¿Eliminar este registro?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-custom btn-sm" style="color: #f87171;" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-white py-4">No hay registros de nómina</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $payrolls->links() }}</div>
@endsection

<!-- Modal para Generar Nómina Automáticamente -->
<div class="modal fade" id="autoGenerateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generar Nómina Automáticamente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('payroll.autoGenerate') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Período</label>
                        <select class="form-select" name="period" required>
                            @php
                                $company = auth()->user()->company;
                                $isBiweekly = $company->payroll_frequency === 'biweekly';
                                $currentPeriod = $isBiweekly
                                    ? date('Y-m') . (date('j') <= 15 ? '-Q1' : '-Q2')
                                    : date('Y-m');
                                $periods = [];
                                for ($i = -3; $i <= 1; $i++) {
                                    $date = \Carbon\Carbon::now()->addMonths($i);
                                    if ($isBiweekly) {
                                        $periods[] = [
                                            'value' => $date->format('Y-m') . '-Q1',
                                            'label' => ucfirst($date->translatedFormat('F Y')) . ' — 1ª Quincena (1-15)',
                                        ];
                                        $periods[] = [
                                            'value' => $date->format('Y-m') . '-Q2',
                                            'label' => ucfirst($date->translatedFormat('F Y')) . ' — 2ª Quincena (16-fin)',
                                        ];
                                    } else {
                                        $periods[] = [
                                            'value' => $date->format('Y-m'),
                                            'label' => ucfirst($date->translatedFormat('F Y')),
                                        ];
                                    }
                                }
                                $periods = array_reverse($periods);
                            @endphp
                            @foreach($periods as $period)
                            <option value="{{ $period['value'] }}" {{ $period['value'] == $currentPeriod ? 'selected' : '' }}>
                                {{ $period['label'] }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Esto generará automáticamente la nómina para todos los empleados activos que no tengan nómina registrada para este período. Se incluirán las horas extra aprobadas automáticamente.
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-magic me-1"></i> Generar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterPayroll() {
    var params = new URLSearchParams(window.location.search);
    var period = document.getElementById('filterPeriod').value;
    if (period) params.set('period', period); else params.delete('period');
    window.location.search = params.toString();
}
</script>
@endpush
