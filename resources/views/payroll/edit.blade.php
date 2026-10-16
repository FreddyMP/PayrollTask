@extends('layouts.app')
@section('title', 'Editar Nómina')
@section('page-title', 'Editar Nómina')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
         <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-pencil me-2"></i>Editar Nómina</div>
            <div class="card-body">
                <form method="POST" action="{{ route('payroll.update', $payroll) }}">
                    @csrf @method('PATCH')
                    {{-- Empleado + Período + Fecha --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label">Empleado</label>
                            <select class="form-select" name="employee_id" id="employeeSelect" required>
                                <option value="">Seleccionar...</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" 
                                        data-salary="{{ $emp->salary }}" 
                                        data-ars-extra="{{ $emp->total_ars_extra }}"
                                        {{ $payroll->employee_id == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->user->name }} — {{ $emp->department }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Período</label>
                            <select class="form-select" name="period" id="periodInput" required>
                                @foreach($periods as $period)
                                <option value="{{ $period['value'] }}" {{ $period['value'] == $payroll->period ? 'selected' : '' }}>
                                    {{ $period['label'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha de Pago</label>
                            <input type="date" class="form-control" name="payment_date" value="{{ $payroll->payment_date ? $payroll->payment_date->format('Y-m-d') : date('Y-m-d') }}">
                        </div>
                    </div>

                    {{-- Salario --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Salario Bruto</label>
                            <input type="number" step="0.01" class="form-control" name="gross_salary" id="grossSalary" required value="{{ $payroll->gross_salary }}">
                        </div>
                    </div>

                    {{-- Incentivos y Descuentos Dinámicos --}}
                    <div class="row g-3 mb-4">
                        {{-- Incentivos --}}
                        <div class="col-md-6">
                            <div class="card shadow-sm border-success" style="border-left: 4px solid #198754;">
                                <div class="card-body">
                                    <h6 class="card-title text-success mb-3"><i class="bi bi-arrow-up-circle me-1"></i> Incentivos / Extras</h6>
                                    <div id="incentivesContainer">
                                        @if(is_array($payroll->incentives_details))
                                            @foreach($payroll->incentives_details as $idx => $inc)
                                            <div class="row g-2 mb-2 align-items-center incentive-row" @if(!empty($inc['is_locked']) || ($inc['description'] ?? '') === 'Vacaciones') style="background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.3); border-radius: 8px; padding: 6px;" @endif>
                                                @if(!empty($inc['is_locked']) || ($inc['description'] ?? '') === 'Vacaciones')
                                                    <div class="col-sm-5">
                                                        <input type="text" class="form-control form-control-sm text-white" name="incentives[{{ $idx }}][description]" value="{{ $inc['description'] ?? 'Vacaciones' }}" readonly style="background: #1e293b; font-weight: 600;">
                                                        <small class="text-info d-block" style="font-size: 0.72rem;"><i class="bi bi-umbrella me-1"></i>Vacaciones (Concepto de Ley)</small>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="number" step="0.01" class="form-control form-control-sm text-success fw-bold incentive-amount" name="incentives[{{ $idx }}][amount]" value="{{ $inc['amount'] ?? 0 }}" readonly style="background: #1e293b;">
                                                    </div>
                                                    <div class="col-sm-3 text-center">
                                                        <div class="form-check form-check-inline m-0">
                                                            <input class="form-check-input incentive-taxable" type="checkbox" name="incentives[{{ $idx }}][is_taxable]" value="1" id="incTax_{{ $idx }}" checked onclick="return false;">
                                                            <label class="form-check-label text-warning" for="incTax_{{ $idx }}"><small><i class="bi bi-shield-lock me-1"></i>Gravable</small></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-1 text-end">
                                                        <span class="badge bg-secondary-subtle text-white-50" title="Concepto inmodificable"><i class="bi bi-lock-fill"></i></span>
                                                    </div>
                                                @else
                                                    <div class="col-sm-5">
                                                        <input type="text" class="form-control form-control-sm" name="incentives[{{ $idx }}][description]" placeholder="Descripción" value="{{ $inc['description'] ?? '' }}" required>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <input type="number" step="0.01" class="form-control form-control-sm incentive-amount" name="incentives[{{ $idx }}][amount]" placeholder="Monto" value="{{ $inc['amount'] ?? 0 }}" required>
                                                    </div>
                                                    <div class="col-sm-3 text-center">
                                                        <div class="form-check form-check-inline m-0">
                                                            <input class="form-check-input incentive-taxable" type="checkbox" name="incentives[{{ $idx }}][is_taxable]" value="1" id="incTax_{{ $idx }}" {{ !empty($inc['is_taxable']) ? 'checked' : '' }}>
                                                            <label class="form-check-label text-white" for="incTax_{{ $idx }}"><small>Gravable</small></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-1 text-end">
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="$(this).closest('.row').remove(); calculateTaxes();"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                @endif
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="addIncentiveRow()">+ Agregar Incentivo</button>
                                    <div class="mt-3 text-white fw-bold">Total Incentivos: RD$ <span id="totalIncentivesDisplay">0.00</span></div>
                                    <input type="hidden" name="extras" id="extras" value="{{ $payroll->extras }}">
                                </div>
                            </div>
                        </div>

                        {{-- Descuentos --}}
                        <div class="col-md-6">
                            <div class="card shadow-sm border-danger" style="border-left: 4px solid #dc3545;">
                                <div class="card-body">
                                    <h6 class="card-title text-danger mb-3"><i class="bi bi-arrow-down-circle me-1"></i> Otros Descuentos</h6>
                                    <div id="discountsContainer">
                                        @if(is_array($payroll->discounts_details))
                                            @foreach($payroll->discounts_details as $idx => $disc)
                                            <div class="row g-2 mb-2 align-items-center discount-row">
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control form-control-sm" name="discounts[{{ $idx }}][description]" placeholder="Descripción" value="{{ $disc['description'] ?? '' }}" required>
                                                </div>
                                                <div class="col-sm-3">
                                                    <input type="number" step="0.01" class="form-control form-control-sm discount-amount" name="discounts[{{ $idx }}][amount]" placeholder="Monto" value="{{ $disc['amount'] ?? 0 }}" required>
                                                </div>
                                                <div class="col-sm-3 text-center">
                                                    <div class="form-check form-check-inline m-0">
                                                        <input class="form-check-input discount-taxable" type="checkbox" name="discounts[{{ $idx }}][affects_taxes]" value="1" id="discTax_{{ $idx }}" {{ !empty($disc['affects_taxes']) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="discTax_{{ $idx }}"><small>Afecta ISR</small></label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-1 text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="$(this).closest('.row').remove(); calculateTaxes();"><i class="bi bi-trash"></i></button>
                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="addDiscountRow()">+ Agregar Descuento</button>
                                    <div class="mt-3 text-white fw-bold">Total Descuentos: RD$ <span id="totalDiscountsDisplay">0.00</span></div>
                                    <input type="hidden" name="descuentos" id="descuentos" value="{{ $payroll->descuentos }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Horas Extra Aprobadas (bloque informativo) --}}
                    <div class="rounded-3 mb-4 p-3" id="overtimeBlock" style="background: rgba(251,146,60,0.07); border: 1px solid rgba(251,146,60,0.2);">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-clock-fill" style="color: #fb923c;"></i>
                            <span class="fw-semibold" style="color: #fb923c; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.04em;">
                                Horas Extra Aprobadas — Período
                            </span>
                            <span id="overtimePeriodLabel" class="text-white" style="font-size: 0.78rem;"></span>
                        </div>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">
                                    Horas Extra Aprobadas
                                    <i class="bi bi-info-circle ms-1" title="Suma de horas extra aprobadas para este empleado en el período seleccionado" data-bs-toggle="tooltip"></i>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="overtimeHoursDisplay" value="—" readonly
                                           style="background: rgba(251,146,60,0.08); border-color: rgba(251,146,60,0.3); color: #fb923c; font-weight: 600; cursor: default;">
                                    <span class="input-group-text" style="background: rgba(251,146,60,0.12); border-color: rgba(251,146,60,0.3); color: #fb923c;">h</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">
                                    Monto Horas Extra
                                    <i class="bi bi-info-circle ms-1" data-bs-toggle="tooltip"
                                       title="Calculado según Art. 203 CT-RD: Salario hora × 1.35 × horas (días laborables)"></i>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: rgba(251,146,60,0.12); border-color: rgba(251,146,60,0.3); color: #fb923c;">RD$</span>
                                    <input type="text" class="form-control" id="overtimePayDisplay" value="—" readonly
                                           style="background: rgba(251,146,60,0.08); border-color: rgba(251,146,60,0.3); color: #fb923c; font-weight: 600; cursor: default;">
                                </div>
                                {{-- Campo oculto que se envía al servidor --}}
                                <input type="hidden" name="overtime_pay" id="overtimePayHidden" value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="height: 1.2rem;"></label>
                                <div class="p-2 rounded-2 text-center" style="background: rgba(251,146,60,0.1); border: 1px solid rgba(251,146,60,0.2); font-size: 0.75rem; color: #94a3b8; line-height: 1.5;">
                                    <i class="bi bi-book me-1 text-warning"></i>
                                    <strong style="color: #fb923c;">Art. 203/204 CT-RD</strong><br>
                                    Diurna: x1.35 | Nocturna: x2.00<br>
                                    Feriados/Descanso: x2.00
                                </div>
                            </div>
                        </div>
                        <div id="overtimeDetails" class="mt-3 pt-3 border-top border-warning-20 d-flex flex-wrap gap-2" style="display:none !important;">
                            <div class="px-2 py-1 rounded-2" style="background:rgba(255,255,255,0.05); border:1px solid rgba(251,146,60,0.1); font-size:0.75rem;">
                                <span class="text-white">Diurnas:</span> <span id="detailDiurnas" class="fw-semibold text-white">0</span>h
                            </div>
                            <div class="px-2 py-1 rounded-2" style="background:rgba(255,255,255,0.05); border:1px solid rgba(251,146,60,0.1); font-size:0.75rem;">
                                <span class="text-white">Nocturnas:</span> <span id="detailNocturnas" class="fw-semibold text-white">0</span>h
                            </div>
                            <div class="px-2 py-1 rounded-2" style="background:rgba(251,146,60,0.1); border:1px solid rgba(251,146,60,0.2); font-size:0.75rem;">
                                <span class="text-white">Feriados/Descanso:</span> <span id="detailFeriados" class="fw-semibold text-white">0</span>h
                            </div>
                        </div>

                        <div id="overtimeLoadingMsg" class="text-white small mt-2" style="display:none;">
                            <span class="spinner-border spinner-border-sm me-1"></span> Consultando horas extra...
                        </div>
                        <div id="overtimeEmptyMsg" class=" text-white small mt-2" style="display:none;">
                            <i class="bi bi-info-circle me-1 text-white"></i> No hay horas extra aprobadas para este empleado en el período seleccionado.
                        </div>
                    </div>

                    {{-- Descuentos de ley --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">ARS (3.04%)</label>
                            <input type="number" step="0.01" class="form-control bg-light text-secondary" name="ars" id="ars" readonly value="{{ $payroll->ars }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">AFP (2.87%)</label>
                            <input type="number" step="0.01" class="form-control bg-light text-secondary" name="afp" id="afp" readonly value="{{ $payroll->afp }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ISR</label>
                            <input type="number" step="0.01" class="form-control bg-light text-secondary" name="isr" id="isr" readonly value="{{ $payroll->isr }}">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> Actualizar</button>
                        <a href="{{ route('payroll.index') }}" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Inicializar tooltips Bootstrap
    $('[data-bs-toggle="tooltip"]').each(function() {
        new bootstrap.Tooltip(this);
    });

    // Bandera de modo quincenal (pasada desde PHP)
    const isBiweekly = {{ $isBiweekly ? 'true' : 'false' }};
    // Multiplicador: 24 para quincenal, 12 para mensual
    const multiplier = isBiweekly ? 24 : 12;

    // ── Cálculo de impuestos ──────────────────────────────────────────
    function calculateTaxes() {
        let salary   = parseFloat($('#grossSalary').val()) || 0;
        let arsExtra = parseFloat($('#employeeSelect option:selected').data('ars-extra')) || 0;
        
        // Calculate Incentives
        let extrasAmount = 0;
        let taxableExtrasAmount = 0;
        $('.incentive-row').each(function() {
            let amount = parseFloat($(this).find('.incentive-amount').val()) || 0;
            let isTaxable = $(this).find('.incentive-taxable').is(':checked');
            extrasAmount += amount;
            if (isTaxable) {
                taxableExtrasAmount += amount;
            }
        });
        $('#extras').val(extrasAmount.toFixed(2));
        $('#totalIncentivesDisplay').text(extrasAmount.toLocaleString('es-DO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        // Calculate Discounts
        let discountsAmount = 0;
        let taxAffectingDiscountsAmount = 0;
        $('.discount-row').each(function() {
            let amount = parseFloat($(this).find('.discount-amount').val()) || 0;
            let affectsTaxes = $(this).find('.discount-taxable').is(':checked');
            discountsAmount += amount;
            if (affectsTaxes) {
                taxAffectingDiscountsAmount += amount;
            }
        });
        $('#descuentos').val(discountsAmount.toFixed(2));
        $('#totalDiscountsDisplay').text(discountsAmount.toLocaleString('es-DO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        let overtimeAmount = parseFloat($('#overtimePayHidden').val()) || 0;
        let totalTaxableExtras = taxableExtrasAmount + overtimeAmount;

        let arsValue = (salary * 0.0304) + arsExtra;
        $('#ars').val(arsValue.toFixed(2));

        let afpValue = salary * 0.0287;
        $('#afp').val(afpValue.toFixed(2));

        // Anualizar con el multiplicador correcto (12 mensual / 24 quincenal)
        let base_imponible = ((salary + totalTaxableExtras) * multiplier) - ((arsValue + afpValue + taxAffectingDiscountsAmount) * multiplier);
        if (base_imponible < 0) base_imponible = 0;
        let isrAnnual = 0;
        if (base_imponible <= 416220) {
            isrAnnual = 0;
        } else if (base_imponible < 624329) {
            isrAnnual = (base_imponible - 416220) * 0.15;
        } else if (base_imponible < 867123) {
            isrAnnual = (base_imponible - 624329) * 0.20 + 31216.35;
        } else {
            isrAnnual = (base_imponible - 867123) * 0.25 + (31216.35 + 48558.80);
        }

        $('#isr').val((isrAnnual / multiplier).toFixed(2));
    }

    // ── Consulta de horas extra aprobadas ────────────────────────────
    let overtimeXhr = null;

    function loadOvertimeData() {
        const employeeId = $('#employeeSelect').val();
        const period     = $('#periodInput').val().trim();

        // Aceptar formato Y-m (mensual) o Y-m-Q1/Q2 (quincenal)
        if (!employeeId || !/^(\d{4}-\d{2})(-Q[12])?$/.test(period)) {
            resetOvertimeDisplay();
            return;
        }

        // Actualizar label del período
        const basePeriod = period.replace(/-Q[12]$/, '');
        const [year, month] = basePeriod.split('-');
        const monthNames = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        const quincenaLabel = period.endsWith('-Q1') ? ' — 1ª Quincena' : period.endsWith('-Q2') ? ' — 2ª Quincena' : '';
        $('#overtimePeriodLabel').text(`${monthNames[parseInt(month) - 1]} ${year}${quincenaLabel}`);

        if (overtimeXhr) overtimeXhr.abort();

        $('#overtimeLoadingMsg').show();
        $('#overtimeEmptyMsg').hide();
        $('#overtimeHoursDisplay').val('—');
        $('#overtimePayDisplay').val('—');
        $('#overtimePayHidden').val('0');

        overtimeXhr = $.ajax({
            url: '{{ route("payroll.apiOvertime") }}',
            method: 'GET',
            data: { employee_id: employeeId, period: period },
            success: function(data) {
                $('#overtimeLoadingMsg').hide();

                if (data.overtime_hours > 0) {
                    $('#overtimeHoursDisplay').val(data.overtime_hours.toFixed(2));
                    $('#overtimePayDisplay').val(
                        parseFloat(data.overtime_pay).toLocaleString('es-DO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                    );
                    $('#overtimePayHidden').val(data.overtime_pay);
                    calculateTaxes();

                    if (data.details) {
                        $('#detailDiurnas').text(data.details.diurnas.toFixed(2));
                        $('#detailNocturnas').text(data.details.nocturnas.toFixed(2));
                        $('#detailFeriados').text(data.details.feriados_descanso.toFixed(2));
                        $('#overtimeDetails').attr('style', 'display: flex !important;');
                    }

                    $('#overtimeEmptyMsg').hide();
                } else {
                    $('#overtimeHoursDisplay').val('0.00');
                    $('#overtimePayDisplay').val('0.00');
                    $('#overtimePayHidden').val('0');
                    $('#overtimeEmptyMsg').show();
                    $('#overtimeDetails').attr('style', 'display: none !important;');
                }
            },
            error: function(xhr) {
                if (xhr.statusText !== 'abort') {
                    $('#overtimeLoadingMsg').hide();
                    resetOvertimeDisplay();
                }
            }
        });
    }

    function resetOvertimeDisplay() {
        $('#overtimeHoursDisplay').val('—');
        $('#overtimePayDisplay').val('—');
        $('#overtimePayHidden').val('0');
        $('#overtimePeriodLabel').text('');
        $('#overtimeEmptyMsg').hide();
        $('#overtimeLoadingMsg').hide();
        $('#overtimeDetails').attr('style', 'display: none !important;');
        calculateTaxes();
    }

    // ── Eventos ──────────────────────────────────────────────────────
    $('#employeeSelect').on('change', function() {
        var rawSalary = parseFloat($(this).find(':selected').data('salary')) || 0;
        // En modo quincenal el salario bruto es la mitad del salario mensual del empleado
        var salary = isBiweekly ? rawSalary / 2 : rawSalary;
        if (rawSalary > 0) {
            $('#grossSalary').val(salary.toFixed(2));
            calculateTaxes();
        }
        loadOvertimeData();
    });

    $(document).on('input', '#grossSalary, .incentive-amount, .discount-amount', function() {
        calculateTaxes();
    });
    
    $(document).on('change', '.incentive-taxable, .discount-taxable', function() {
        calculateTaxes();
    });

    // Funciones para agregar filas dinámicas
    window.addIncentiveRow = function() {
        let idx = new Date().getTime(); // Usar timestamp para evitar colisiones en edición
        let row = `
            <div class="row g-2 mb-2 align-items-center incentive-row">
                <div class="col-sm-5">
                    <input type="text" class="form-control form-control-sm" name="incentives[${idx}][description]" placeholder="Descripción" required>
                </div>
                <div class="col-sm-3">
                    <input type="number" step="0.01" class="form-control form-control-sm incentive-amount" name="incentives[${idx}][amount]" placeholder="Monto" required>
                </div>
                <div class="col-sm-3 text-center">
                    <div class="form-check form-check-inline m-0">
                        <input class="form-check-input incentive-taxable" type="checkbox" name="incentives[${idx}][is_taxable]" value="1" id="incTax_${idx}" checked>
                        <label class="form-check-label text-white" for="incTax_${idx}"><small>Gravable</small></label>
                    </div>
                </div>
                <div class="col-sm-1 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="$(this).closest('.row').remove(); calculateTaxes();"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        `;
        $('#incentivesContainer').append(row);
    };

    window.addDiscountRow = function() {
        let idx = new Date().getTime();
        let row = `
            <div class="row g-2 mb-2 align-items-center discount-row">
                <div class="col-sm-5">
                    <input type="text" class="form-control form-control-sm" name="discounts[${idx}][description]" placeholder="Descripción" required>
                </div>
                <div class="col-sm-3">
                    <input type="number" step="0.01" class="form-control form-control-sm discount-amount" name="discounts[${idx}][amount]" placeholder="Monto" required>
                </div>
                <div class="col-sm-3 text-center">
                    <div class="form-check form-check-inline m-0">
                        <input class="form-check-input discount-taxable" type="checkbox" name="discounts[${idx}][affects_taxes]" value="1" id="discTax_${idx}">
                        <label class="form-check-label text-white" for="discTax_${idx}"><small>Afecta ISR</small></label>
                    </div>
                </div>
                <div class="col-sm-1 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="$(this).closest('.row').remove(); calculateTaxes();"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        `;
        $('#discountsContainer').append(row);
    };

    $('#periodInput').on('change', function() {
        loadOvertimeData();
    });

    // Cargar datos iniciales
    loadOvertimeData();
});
</script>
@endpush
