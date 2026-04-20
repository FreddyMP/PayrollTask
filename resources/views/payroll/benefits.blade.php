@extends('layouts.app')
@section('title', 'Prestaciones Laborales')
@section('page-title', 'Prestaciones Laborales (Cálculo)')

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
        <a class="nav-link active" href="{{ route('payroll.benefits') }}" style="color: white; border-bottom: 2px solid var(--primary); background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
            Prestaciones Laborales
        </a>
    </li>
</ul>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header text-white">
                <i class="bi bi-calculator-fill me-2"></i>Configuración del Cálculo
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Empleado</label>
                    <select id="employeeSelect" class="form-select">
                        <option value="">Seleccione un empleado...</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" 
                                    data-salary="{{ $emp->salary }}" 
                                    data-hire="{{ $emp->hire_date?->format('Y-m-d') }}">
                                {{ $emp->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Fecha Ingreso</label>
                        <input type="text" id="hireDateDisplay" class="form-control text-dark" readonly disabled>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Fecha Salida</label>
                        <input type="date" id="exitDate" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Salario Mensual</label>
                    <div class="input-group">
                        <span class="input-group-text bg-dark-3 text-dark">RD$</span>
                        <input type="number" id="salaryInput" class="form-control" step="0.01">
                    </div>
                </div>

                <h6 class="text-white mb-3" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary-light);">Incluir en cálculo:</h6>
                
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="checkPreaviso" checked>
                    <label class="form-check-label text-white" for="checkPreaviso">Preaviso</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="checkCesantia" checked>
                    <label class="form-check-label text-white" for="checkCesantia">Cesantía</label>
                </div>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="checkVacaciones" checked>
                    <label class="form-check-label text-white" for="checkVacaciones">Vacaciones no disfrutadas</label>
                </div>
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="checkNavidad" checked>
                    <label class="form-check-label text-white" for="checkNavidad">Salario de Navidad (Proporcional)</label>
                </div>

                <div class="alert alert-info py-2" style="font-size: 0.75rem;">
                    <i class="bi bi-info-circle me-1"></i> Basado en el Art. 76, 80 y 219 del Código de Trabajo RD.
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4 overflow-hidden">
            <div class="card-header bg-dark-2 text-white d-flex justify-content-between align-items-center">
                <span>Resumen de Prestaciones</span>
                <span id="seniorityLabel" class="badge bg-primary">0 años, 0 meses, 0 días</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark mb-0">
                        <thead>
                            <tr style="background: rgba(255,255,255,0.02);">
                                <th class="ps-4">Concepto</th>
                                <th>Criterio Aplicado</th>
                                <th>Días / Prop</th>
                                <th class="text-end pe-4">Subtotal (RD$)</th>
                            </tr>
                        </thead>
                        <tbody id="benefitsTableBody">
                            {{-- JS render --}}
                        </tbody>
                        <tfoot>
                            <tr class="bg-primary bg-opacity-10 border-top border-primary">
                                <td colspan="3" class="ps-4 fw-bold text-white py-3">TOTAL ESTIMADO</td>
                                <td class="text-end pe-4 fw-bold text-primary py-3" id="totalValue" style="font-size: 1.2rem;">RD$ 0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card bg-dark-2 h-100">
                    <div class="card-body">
                        <h6 class="small text-uppercase fw-bold mb-3 text-white">Datos de Conversión</h6>
                        <div class="d-flex justify-content-between mb-2 text-white">
                            <span>Valor Día (Prestaciones):</span>
                            <span class="fw-bold" id="dailyValueText">RD$ 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-0 text-white">
                            <span>Promedio Mensual (23.83):</span>
                            <span class="small">Estándar RD</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <button onclick="window.print()" class="btn btn-outline-custom w-100 h-100 py-3">
                    <i class="bi bi-printer me-2"></i> Imprimir Calculo
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const $employeeSelect = $('#employeeSelect');
    const $exitDate = $('#exitDate');
    const $salaryInput = $('#salaryInput');
    const $hireDateDisplay = $('#hireDateDisplay');
    const $benefitsTableBody = $('#benefitsTableBody');
    const $totalValue = $('#totalValue');
    const $dailyValueText = $('#dailyValueText');
    const $seniorityLabel = $('#seniorityLabel');

    const toggles = ['#checkPreaviso', '#checkCesantia', '#checkVacaciones', '#checkNavidad'];

    function calculate() {
        const selected = $employeeSelect.find(':selected');
        const hireDateStr = selected.data('hire');
        const salary = parseFloat($salaryInput.val()) || 0;
        const exitDateStr = $exitDate.val();

        if (!hireDateStr || !exitDateStr || salary <= 0) {
            $benefitsTableBody.empty().append('<tr><td colspan="4" class="text-center py-4 text-muted">Complete los datos para ver el cálculo</td></tr>');
            $totalValue.text('RD$ 0.00');
            $dailyValueText.text('RD$ 0.00');
            return;
        }

        const start = new Date(hireDateStr);
        const end = new Date(exitDateStr);
        
        // Calculate Seniority
        let years = end.getFullYear() - start.getFullYear();
        let months = end.getMonth() - start.getMonth();
        let days = end.getDate() - start.getDate();

        if (days < 0) {
            months -= 1;
            days += new Date(end.getFullYear(), end.getMonth(), 0).getDate();
        }
        if (months < 0) {
            years -= 1;
            months += 12;
        }

        const totalMonths = (years * 12) + months;
        $seniorityLabel.text(`${years} años, ${months} meses, ${days} días`);

        const dailyRate = salary / 23.83;
        $dailyValueText.text('RD$ ' + dailyRate.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

        let total = 0;
        let html = '';

        // --- PREAVISO ---
        if ($('#checkPreaviso').is(':checked')) {
            let preavisoDays = 0;
            let criteria = '';
            if (totalMonths >= 3 && totalMonths < 6) {
                preavisoDays = 7;
                criteria = '3-6 meses';
            } else if (totalMonths >= 6 && totalMonths < 12) {
                preavisoDays = 14;
                criteria = '6-12 meses';
            } else if (totalMonths >= 12) {
                preavisoDays = 28;
                criteria = '+1 año';
            }
            
            const subtotal = preavisoDays * dailyRate;
            if (preavisoDays > 0) {
                total += subtotal;
                html += `<tr><td class="ps-4">Preaviso</td><td>${criteria}</td><td>${preavisoDays} días</td><td class="text-end pe-4">RD$ ${subtotal.toLocaleString('en-US', {minimumFractionDigits:2})}</td></tr>`;
            }
        }

        // --- CESANTIA ---
        if ($('#checkCesantia').is(':checked')) {
            let cesantiaDays = 0;
            let criteriaParts = [];
            
            // Base years
            if (years > 0) {
                const daysPerYear = years >= 5 ? 23 : 21;
                cesantiaDays += years * daysPerYear;
                criteriaParts.push(`${years} año(s) @ ${daysPerYear}d`);
            }
            
            // Fractional months (remaining in the current year)
            // Scale: 3-6m = 6d, 6-12m = 13d
            if (months >= 3 && months < 6) {
                cesantiaDays += 6;
                criteriaParts.push('Fracc. 3-6m (+6d)');
            } else if (months >= 6) {
                cesantiaDays += 13;
                criteriaParts.push('Fracc. 6-12m (+13d)');
            } else if (years == 0 && totalMonths >= 3 && totalMonths < 6) {
                cesantiaDays = 6; criteriaParts = ['3-6 meses'];
            } else if (years == 0 && totalMonths >= 6 && totalMonths < 12) {
                cesantiaDays = 13; criteriaParts = ['6-12 meses'];
            }

            const subtotal = cesantiaDays * dailyRate;
            if (cesantiaDays > 0) {
                total += subtotal;
                html += `<tr><td class="ps-4">Cesantía</td><td class="small">${criteriaParts.join('<br>')}</td><td>${cesantiaDays} días</td><td class="text-end pe-4">RD$ ${subtotal.toLocaleString('en-US', {minimumFractionDigits:2})}</td></tr>`;
            }
        }

        // --- VACACIONES ---
        if ($('#checkVacaciones').is(':checked')) {
            let vacDays = 0;
            let criteria = '';
            
            // Años completos
            if (years >= 1) {
                const baseDays = (years >= 5) ? 18 : 14;
                vacDays += baseDays;
                criteria = `${baseDays}d (años) `;
            }
            
            // Fracción del último año (proporción legal RD)
            let propDays = 0;
            if (months >= 5 && months < 6) propDays = 6;
            else if (months >= 6 && months < 7) propDays = 7;
            else if (months >= 7 && months < 8) propDays = 8;
            else if (months >= 8 && months < 9) propDays = 9;
            else if (months >= 9 && months < 10) propDays = 10;
            else if (months >= 10 && months < 11) propDays = 11;
            else if (months >= 11) propDays = 12;
            
            if (propDays > 0) {
                vacDays += propDays;
                criteria += `+ ${propDays}d (fracción)`;
            } else if (years == 0) {
                criteria = 'Menos de 5 meses';
            }

            const subtotal = vacDays * dailyRate;
            if (vacDays > 0) {
                total += subtotal;
                html += `<tr><td class="ps-4">Vacaciones No Disfrutadas</td><td>${criteria}</td><td>${vacDays} días</td><td class="text-end pe-4">RD$ ${subtotal.toLocaleString('en-US', {minimumFractionDigits:2})}</td></tr>`;
            }
        }

        // --- NAVIDAD ---
        if ($('#checkNavidad').is(':checked')) {
            // Calculation: Sum of salaries in year / 12
            // We approximate using (salario * months worked THIS calendar year / 12)
            const exitYear = end.getFullYear();
            const jan1 = new Date(exitYear, 0, 1);
            let navStart = start > jan1 ? start : jan1; // Work began in this year or before
            
            let navMonths = (end.getMonth() - navStart.getMonth()) + (end.getDate() / 30);
            if (navMonths < 0) navMonths = 0;
            if (navMonths > 12) navMonths = 12;

            const navSubtotal = (salary * navMonths) / 12;
            if (navSubtotal > 0) {
                total += navSubtotal;
                html += `<tr><td class="ps-4 text-warning">Salario de Navidad</td><td>Proporcional (${navMonths.toFixed(2)} meses)</td><td>—</td><td class="text-end pe-4 text-warning">RD$ ${navSubtotal.toLocaleString('en-US', {minimumFractionDigits:2})}</td></tr>`;
            }
        }

        if (html === '') html = '<tr><td colspan="4" class="text-center py-4 text-muted">Ningún concepto activo o aplicable para este tiempo.</td></tr>';
        
        $benefitsTableBody.html(html);
        $totalValue.text('RD$ ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    $employeeSelect.on('change', function() {
        const hire = $(this).find(':selected').data('hire');
        const salary = $(this).find(':selected').data('salary');
        $hireDateDisplay.val(hire ? hire : '');
        $salaryInput.val(salary ? salary : '');
        calculate();
    });

    $exitDate.on('change', calculate);
    $salaryInput.on('input', calculate);
    toggles.forEach(id => $(id).on('change', calculate));
});
</script>
@endpush
