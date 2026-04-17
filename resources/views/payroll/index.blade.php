@extends('layouts.app')
@section('title', 'Nómina')
@section('page-title', 'Gestión de Nómina')

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
    <a href="{{ route('payroll.create') }}" class="btn btn-primary-custom">
        <i class="bi bi-plus-lg me-1"></i> Nueva Nómina
    </a>
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
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay registros de nómina</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $payrolls->links() }}</div>
@endsection

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
