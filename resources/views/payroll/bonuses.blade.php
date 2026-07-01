@extends('layouts.app')
@section('title', 'Bonificaciones de Ley')
@section('page-title', 'Bonificaciones de Ley (Utilidades)')

@section('content')

@if(session('payroll_exists_error'))
<!-- Modal de Error de Nómina Existente -->
<div class="modal fade" id="payrollExistsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Nómina Ya Generada
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger border-0 mb-3">
                    <i class="bi bi-shield-exclamation me-2"></i>
                    <strong>¡Atención!</strong> No se puede agregar bonificaciones.
                </div>

                <h6 class="text-white mb-3">Razón del Error:</h6>
                <p class="text-white mb-3">
                    La <strong>nómina del período actual ya ha sido generada</strong>. Para agregar bonificaciones de ley a la nómina, esta <strong>NO debe estar generada previamente</strong>.
                </p>

                <div class="card bg-dark-3 border-warning mb-3">
                    <div class="card-body">
                        <h6 class="text-warning mb-2">
                            <i class="bi bi-lightbulb me-2"></i>Soluciones:
                        </h6>
                        <ol class="text-white small mb-0">
                            <li class="mb-2">
                                <strong>Eliminar las nóminas del período actual</strong>
                                <br>
                                <small class="text-muted">Ve a "Registros de Nómina" y elimina las nóminas del período {{ now()->format('Y-m') }} que aún no estén marcadas como pagadas.</small>
                            </li>
                            <li class="mb-2">
                                <strong>Agregar bonificaciones manualmente</strong>
                                <br>
                                <small class="text-muted">Edita cada nómina y agrega el monto en el campo "Extras" o "Bonificaciones".</small>
                            </li>
                            <li>
                                <strong>Usar pago separado</strong>
                                <br>
                                <small class="text-muted">Cambia la configuración en "Empresa" para pagar bonificaciones de forma separada.</small>
                            </li>
                        </ol>
                    </div>
                </div>

                <p class="text-muted small mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Nota:</strong> Las bonificaciones deben agregarse ANTES de generar la nómina del mes para que se calculen correctamente los impuestos y deducciones.
                </p>
            </div>
            <div class="modal-footer">
                <a href="{{ route('payroll.index') }}" class="btn btn-outline-custom">
                    <i class="bi bi-list-ul me-2"></i>Ver Nóminas
                </a>
                <button type="button" class="btn btn-primary-custom" data-bs-dismiss="modal">
                    Entendido
                </button>
            </div>
        </div>
    </div>
</div>
@endif
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
        <a class="nav-link" href="{{ route('payroll.ir17') }}" style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
            IR-17
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
                <small class="text-white mt-2 d-block">Monto total de utilidades a distribuir (se tomará el 10%)</small>
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
    <div class="card-header d-flex justify-content-between align-items-center text-white">
        <span>Cálculo por Empleado</span>
        <div>
            <span class="badge badge-status badge-info me-2" id="employeeCount">{{ count($employees) }} Empleados</span>
            @if(Auth::user()->company->bonus_payment_method === 'payroll')
            <form id="addBonusesForm" action="{{ route('payroll.bonuses.addToPayroll') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary-custom" id="addBonusesBtn">
                    <i class="bi bi-plus-circle me-1"></i> Agregar a la Nómina
                </button>
            </form>
            @endif
        </div>
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
                        @if(Auth::user()->company->bonus_payment_method === 'separate')
                        <th class="text-center">Acción</th>
                        @endif
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
                        <td class="text-end fw-bold text-success final-bonus-cell" data-employee-id="{{ $employee->id }}">RD$ 0.00</td>
                        @if(Auth::user()->company->bonus_payment_method === 'separate')
                        <td class="text-center">
                            <form action="{{ route('payroll.bonuses.paySeparate', $employee) }}" method="POST" class="d-inline separate-pay-form" onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerHTML = '<span class=\'spinner-border spinner-border-sm\' role=\'status\' aria-hidden=\'true\'></span>';">
                                @csrf
                                <input type="hidden" name="amount" class="separate-amount-input" value="0">
                                <button type="submit" class="btn btn-sm btn-outline-custom" title="Pagar y enviar correo">
                                    <i class="bi bi-envelope-check me-1"></i> Pagar
                                </button>
                            </form>
                        </td>
                        @endif
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
@if(session('payroll_exists_error'))
$(document).ready(function() {
    var modal = new bootstrap.Modal(document.getElementById('payrollExistsModal'));
    modal.show();
});
@endif
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
            const finalBonusFormatted = finalBonus.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

            $(this).find('.final-bonus-cell').text('RD$ ' + finalBonusFormatted);

            // Update hidden inputs for separate payments
            $(this).find('.separate-amount-input').val(finalBonus.toFixed(2));
        });
    }

    $utilidadInput.on('input', calculate);

    // Handle form submit for adding to payroll
    $('#addBonusesForm').on('submit', function(e) {
        // Prevent default to append hidden inputs
        e.preventDefault();

        const utilidad = parseFloat($utilidadInput.val()) || 0;
        if (utilidad <= 0) {
            alert('Por favor ingrese el monto de utilidades a distribuir.');
            return;
        }

        const form = $(this);
        // Clear previous hidden inputs if any
        form.find('.dynamic-input').remove();

        $rows.each(function() {
            const employeeId = $(this).find('.final-bonus-cell').data('employee-id');
            const baseBonus = parseFloat($(this).data('base-bonus')) || 0;
            const utilidadVal = parseFloat($utilidadInput.val()) || 0;

            // Recalculate just to be sure
            let totalBaseBonus = 0;
            $rows.each(function() { totalBaseBonus += parseFloat($(this).data('base-bonus')) || 0; });
            const distribucion = utilidadVal * 0.10;
            const convertidor = totalBaseBonus > 0 ? (distribucion / totalBaseBonus) : 0;
            const finalBonus = (baseBonus * convertidor).toFixed(2);

            form.append(`<input type="hidden" name="bonuses[${employeeId}]" value="${finalBonus}" class="dynamic-input">`);
        });

        // Submit form programmatically
        const btn = $('#addBonusesBtn');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Agregando...');
        this.submit();
    });

    // Initial calculation with 0
    calculate();
});
</script>
@endpush
