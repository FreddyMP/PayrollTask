@extends('layouts.app')
@section('title', 'Bonificaciones de Ley')
@section('page-title', 'Bonificaciones de Ley (Utilidades)')

@section('content')
<ul class="nav nav-tabs mb-4 px-3" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('payroll.index') }}" style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
            Registros de Nómina
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="{{ route('payroll.bonuses') }}" style="color: white; border-bottom: 2px solid var(--primary); background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
            Bonificaciones de Ley
        </a>
    </li>
</ul>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <label class="form-label">Utilidades de la empresa / Ganancias del año fiscal</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark-3 text-white border-0">RD$</span>
                    <input type="number" id="utilidadInput" class="form-control" placeholder="0.00" step="0.01">
                </div>
                <small class="text-muted mt-2 d-block">Monto total de utilidades a distribuir (se tomará el 10%)</small>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="row h-100">
            <div class="col-md-4">
                <div class="stat-card purple h-100">
                    <div class="stat-label">Distribución (10%)</div>
                    <div class="stat-value" id="distribucionText">RD$ 0.00</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card blue h-100">
                    <div class="stat-label">Pre-Distribución (Total Base)</div>
                    <div class="stat-value" id="preDistribucionText">RD$ 0.00</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card green h-100">
                    <div class="stat-label">Convertidor</div>
                    <div class="stat-value" id="convertidorText">0.0000</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Cálculo por Empleado</span>
        <span class="badge badge-status badge-info" id="employeeCount">{{ count($employees) }} Empleados</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0" id="bonusesTable">
                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th>Fecha Ingreso</th>
                        <th>Antigüedad</th>
                        <th>Salario</th>
                        <th>Valor Día</th>
                        <th>Días Cal.</th>
                        <th>Bonificación Base</th>
                        <th class="text-end">Monto a Pagar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                    @php
                        $hireDate = $employee->hire_date;
                        $years = $hireDate ? $hireDate->diffInYears(now()) : 0;
                        $valorDia = $employee->salary / 23.83;
                        $dias = $years < 3 ? 45 : 60;
                        $baseBonus = $valorDia * $dias;
                    @endphp
                    <tr class="employee-row" 
                        data-salary="{{ $employee->salary }}" 
                        data-years="{{ $years }}"
                        data-base-bonus="{{ $baseBonus }}">
                        <td class="fw-semibold">{{ $employee->user->name ?? '—' }}</td>
                        <td>{{ $hireDate?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $years }} años</td>
                        <td>RD$ {{ number_format($employee->salary, 2) }}</td>
                        <td>RD$ {{ number_format($valorDia, 2) }}</td>
                        <td>{{ $dias }}</td>
                        <td>RD$ {{ number_format($baseBonus, 2) }}</td>
                        <td class="text-end fw-bold text-success final-bonus-cell">RD$ 0.00</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const $utilidadInput = $('#utilidadInput');
    const $distribucionText = $('#distribucionText');
    const $preDistribucionText = $('#preDistribucionText');
    const $convertidorText = $('#convertidorText');
    const $rows = $('.employee-row');

    function calculate() {
        const utilidad = parseFloat($utilidadInput.val()) || 0;
        const distribucion = utilidad * 0.10;
        
        let totalBaseBonus = 0;
        $rows.each(function() {
            totalBaseBonus += parseFloat($(this).data('base-bonus')) || 0;
        });

        const convertidor = totalBaseBonus > 0 ? (distribucion / totalBaseBonus) : 0;

        // Update summaries
        $distribucionText.text('RD$ ' + distribucion.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $preDistribucionText.text('RD$ ' + totalBaseBonus.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $convertidorText.text(convertidor.toFixed(6));

        // Update rows
        $rows.each(function() {
            const baseBonus = parseFloat($(this).data('base-bonus')) || 0;
            const finalBonus = baseBonus * convertidor;
            $(this).find('.final-bonus-cell').text('RD$ ' + finalBonus.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        });
    }

    $utilidadInput.on('input', calculate);
    
    // Initial calculation with 0
    calculate();
});
</script>
@endpush
