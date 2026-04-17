@extends('layouts.app')
@section('title', 'Nueva Nómina')
@section('page-title', 'Registrar Nómina')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-cash-stack me-2"></i>Registrar Nómina</div>
            <div class="card-body">
                <form method="POST" action="{{ route('payroll.store') }}">
                    @csrf
                    {{-- Empleado + Período + Fecha --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label class="form-label">Empleado</label>
                            <select class="form-select" name="employee_id" id="employeeSelect" required>
                                <option value="">Seleccionar...</option>
                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" data-salary="{{ $emp->salary }}">{{ $emp->user->name }} — {{ $emp->department }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Período</label>
                            <input type="text" class="form-control" name="period" id="periodInput" placeholder="2024-03" required value="{{ $currentPeriod }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha de Pago</label>
                            <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    {{-- Salario + Extras + Descuentos --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Salario Bruto</label>
                            <input type="number" step="0.01" class="form-control" name="gross_salary" id="grossSalary" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Extras / Incentivos</label>
                            <input type="number" step="0.01" class="form-control" name="extras" id="extras" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Otros Descuentos</label>
                            <input type="number" step="0.01" class="form-control" name="descuentos" id="descuentos" value="0">
                        </div>
                    </div>

                    {{-- Horas Extra Aprobadas (bloque informativo) --}}
                    <div class="rounded-3 mb-4 p-3" id="overtimeBlock" style="background: rgba(251,146,60,0.07); border: 1px solid rgba(251,146,60,0.2);">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-clock-fill" style="color: #fb923c;"></i>
                            <span class="fw-semibold" style="color: #fb923c; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.04em;">
                                Horas Extra Aprobadas — Período
                            </span>
                            <span id="overtimePeriodLabel" class="text-muted" style="font-size: 0.78rem;"></span>
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
                                    <strong style="color: #fb923c;">Art. 203 CT-RD</strong><br>
                                    Hora extra = hora ordinaria × 1.35<br>
                                    <span style="font-size:0.68rem;">(Salario mensual ÷ 173.33 h)</span>
                                </div>
                            </div>
                        </div>
                        <div id="overtimeLoadingMsg" class="text-muted small mt-2" style="display:none;">
                            <span class="spinner-border spinner-border-sm me-1"></span> Consultando horas extra...
                        </div>
                        <div id="overtimeEmptyMsg" class="text-muted small mt-2" style="display:none;">
                            <i class="bi bi-info-circle me-1"></i> No hay horas extra aprobadas para este empleado en el período seleccionado.
                        </div>
                    </div>

                    {{-- Descuentos de ley --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">ARS (3.04%)</label>
                            <input type="number" step="0.01" class="form-control bg-light text-secondary" name="ars" id="ars" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">AFP (2.87%)</label>
                            <input type="number" step="0.01" class="form-control bg-light text-secondary" name="afp" id="afp" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ISR</label>
                            <input type="number" step="0.01" class="form-control bg-light text-secondary" name="isr" id="isr" readonly>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> Registrar</button>
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

    // ── Cálculo de impuestos ──────────────────────────────────────────
    function calculateTaxes() {
        let salary = parseFloat($('#grossSalary').val()) || 0;

        let arsValue = salary * 0.0304;
        $('#ars').val(arsValue.toFixed(2));

        let afpValue = salary * 0.0287;
        $('#afp').val(afpValue.toFixed(2));

        let base_imponible = (salary * 12) - ((arsValue * 12) + (afpValue * 12));
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

        $('#isr').val((isrAnnual / 12).toFixed(2));
    }

    // ── Consulta de horas extra aprobadas ────────────────────────────
    let overtimeXhr = null;

    function loadOvertimeData() {
        const employeeId = $('#employeeSelect').val();
        const period     = $('#periodInput').val().trim();

        // Validar formato Y-m
        if (!employeeId || !/^\d{4}-\d{2}$/.test(period)) {
            resetOvertimeDisplay();
            return;
        }

        // Actualizar label del período
        const [year, month] = period.split('-');
        const monthNames = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        $('#overtimePeriodLabel').text(`${monthNames[parseInt(month) - 1]} ${year}`);

        // Cancelar petición anterior si existe
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
                    $('#overtimeEmptyMsg').hide();
                } else {
                    $('#overtimeHoursDisplay').val('0.00');
                    $('#overtimePayDisplay').val('0.00');
                    $('#overtimePayHidden').val('0');
                    $('#overtimeEmptyMsg').show();
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
    }

    // ── Eventos ──────────────────────────────────────────────────────
    $('#employeeSelect').on('change', function() {
        var salary = $(this).find(':selected').data('salary');
        if (salary !== undefined) {
            $('#grossSalary').val(salary);
            calculateTaxes();
        }
        loadOvertimeData();
    });

    $('#grossSalary').on('input', function() {
        calculateTaxes();
    });

    // Usar debounce en el período para no disparar petición en cada tecla
    let periodTimer;
    $('#periodInput').on('input', function() {
        clearTimeout(periodTimer);
        periodTimer = setTimeout(loadOvertimeData, 600);
    });
});
</script>
@endpush
