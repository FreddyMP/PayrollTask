@extends('layouts.app')

@section('title', 'Gestión de Vacaciones')
@section('page-title', 'Gestión de Vacaciones')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="text-white mb-1">Control de Vacaciones {{ $year }}</h5>
                    <p class="small text-white mb-0">
                        Administra y monitorea las vacaciones de los empleados y sus pagos correspondientes
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @if(($company->vacation_payment_enabled ?? true) && ($company->vacation_payment_method ?? 'payroll') === 'separate')
                        <button type="button" class="btn btn-success d-flex align-items-center gap-2" data-bs-toggle="modal"
                            data-bs-target="#payApprovedModal">
                            <i class="bi bi-cash-coin fs-5"></i>
                            <span>Pagar vacaciones aprobadas</span>
                            @if(isset($eligibleVacations) && $eligibleVacations->count() > 0)
                                <span class="badge bg-white text-success fw-bold ms-1">{{ $eligibleVacations->count() }}</span>
                            @endif
                        </button>
                    @endif
                    <a href="{{ route('vacations.create') }}" class="btn btn-primary-custom">
                        <i class="bi bi-plus-circle me-2"></i>Registrar Vacaciones
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Banner informativo de modalidad de pago de vacaciones -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm"
                style="background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border-left: 4px solid {{ ($company->vacation_payment_method ?? 'payroll') === 'separate' ? '#ec4899' : '#6366f1' }} !important;">
                <div class="card-body py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 rounded-3"
                            style="background: {{ ($company->vacation_payment_method ?? 'payroll') === 'separate' ? 'rgba(236,72,153,0.15)' : 'rgba(99,102,241,0.15)' }}; color: {{ ($company->vacation_payment_method ?? 'payroll') === 'separate' ? '#ec4899' : '#818cf8' }};">
                            <i
                                class="bi {{ ($company->vacation_payment_method ?? 'payroll') === 'separate' ? 'bi-credit-card-2-front-fill' : 'bi-calendar2-week-fill' }} fs-4"></i>
                        </div>
                        <div>
                            <span class="text-white fw-bold d-block">
                                Modalidad de Pago de Vacaciones:
                                <span
                                    class="badge {{ ($company->vacation_payment_method ?? 'payroll') === 'separate' ? 'bg-pink-subtle text-pink' : 'bg-indigo-subtle text-indigo' }}"
                                    style="font-size: 0.82rem; background: {{ ($company->vacation_payment_method ?? 'payroll') === 'separate' ? 'rgba(236,72,153,0.2)' : 'rgba(99,102,241,0.2)' }}; color: {{ ($company->vacation_payment_method ?? 'payroll') === 'separate' ? '#f472b6' : '#a5b4fc' }};">
                                    {{ ($company->vacation_payment_method ?? 'payroll') === 'separate' ? 'Pago Independiente a la Nómina' : 'Pagar con la Nómina Regular' }}
                                </span>
                            </span>
                            <small class="text-white-50">
                                @if(($company->vacation_payment_method ?? 'payroll') === 'separate')
                                    Las vacaciones aprobadas con inicio hasta 5 días después de hoy se pagan de forma
                                    independiente generando su volante de pago y deducciones de ley.
                                @else
                                    Al registrar las vacaciones se añade como extra gravable inmodificable de "Vacaciones" en el
                                    período de nómina correspondiente sujeto a TSS e IR-3.
                                @endif
                            </small>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('company.edit') }}" class="btn btn-outline-custom btn-sm">
                            <i class="bi bi-gear me-1"></i>Configuración
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="GET" action="{{ route('vacations.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Año</label>
                            <select name="year" class="form-select" onchange="this.form.submit()">
                                @foreach($years as $y)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Buscar Empleado</label>
                            <input type="text" name="search" class="form-control" placeholder="Nombre o email..."
                                value="{{ $search ?? '' }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary-custom w-100">
                                <i class="bi bi-search me-2"></i>Buscar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary p-3 rounded-3 me-3">
                            <i class="bi bi-people-fill text-white fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-white ">Total Empleados</h6>
                            <h3 class="text-white mb-0">{{ $employees->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success p-3 rounded-3 me-3">
                            <i class="bi bi-calendar-check-fill text-white fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-white small mb-0">Días Tomados</h6>
                            <h3 class="text-white mb-0">{{ $employees->sum('days_taken') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning p-3 rounded-3 me-3">
                            <i class="bi bi-cash-stack text-white fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-white small mb-0">Total Vacaciones Anual</h6>
                            <h4 class="text-white mb-0">RD$ {{ number_format($employees->sum('amount_entitled'), 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info p-3 rounded-3 me-3">
                            <i class="bi bi-calendar2-week text-white fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-white small mb-0">Días Restantes</h6>
                            <h3 class="text-white mb-0">{{ $employees->sum('days_remaining') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div
                    class="card-header bg-transparent border-bottom border-secondary d-flex justify-content-between align-items-center">
                    <h6 class="text-white mb-0">
                        <i class="bi bi-list-ul me-2"></i>Lista de Empleados y Compensación de Vacaciones
                    </h6>
                    <small class="text-white-50">
                        Cálculo Art. 177 CT: 14 días (< 5 años) o 18 días (≥ 5 años) de salario ordinario </small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>Departamento</th>
                                    <th>Cargo</th>
                                    <th class="text-center">Antigüedad</th>
                                    <th class="text-center">Días Corresp.</th>
                                    <th class="text-center">Días Tomados</th>
                                    <th class="text-center">Días Restantes</th>
                                    <th class="text-end pe-3">Monto por Vacaciones</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong class="text-dark">{{ $employee['name'] }}</strong>
                                                <br>
                                                <small class="text-dark">{{ $employee['email'] }}</small>
                                            </div>
                                        </td>
                                        <td><span class="text-dark">{{ $employee['department'] }}</span></td>
                                        <td><span class="text-dark">{{ $employee['position'] }}</span></td>
                                        <td class="text-center">
                                            <span class="badge badge-status badge-supervisor">
                                                {{ number_format($employee['years_of_service'], 1) }} años
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <strong class="text-dark">{{ $employee['days_entitled'] }}</strong>
                                        </td>
                                        <td class="text-center text-dark">
                                            @if($employee['days_taken'] > 0)
                                                <span class="badge badge-status badge-completed">
                                                    {{ $employee['days_taken'] }} días
                                                </span>
                                            @else
                                                <span class="text-dark">0 días</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-dark">
                                            @php
                                                $remaining = $employee['days_remaining'];
                                                $percentage = $employee['days_entitled'] > 0
                                                    ? ($remaining / $employee['days_entitled']) * 100
                                                    : 0;
                                            @endphp

                                            @if($percentage > 60)
                                                <span class="badge badge-status badge-active">
                                                    {{ $remaining }} días
                                                </span>
                                            @elseif($percentage > 30)
                                                <span class="badge badge-status badge-warning">
                                                    {{ $remaining }} días
                                                </span>
                                            @else
                                                <span class="badge badge-status badge-danger text-dark">
                                                    {{ $remaining }} días
                                                </span>
                                            @endif
                                        </td>
                                        {{-- NUEVA COLUMNA: Cuánto le tocaría al empleado al tomar las vacaciones --}}
                                        <td class="text-end pe-3">
                                            @if($employee['can_take_vacation'])
                                                <div>
                                                    <strong class="text-success" style="font-size: 0.95rem;">
                                                        RD$ {{ number_format($employee['amount_entitled'], 2) }}
                                                    </strong>
                                                    <br>
                                                    <small class="text-dark" style="font-size: 0.75rem;">
                                                        RD$ {{ number_format($employee['daily_salary'], 2) }}/día
                                                    </small>
                                                    @if($employee['days_taken'] > 0)
                                                        <br>
                                                        <span class="badge bg-secondary-subtle text-dark" style="font-size: 0.7rem;">
                                                            Tomado: RD$ {{ number_format($employee['amount_taken'], 2) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted" style="font-size: 0.8rem;">
                                                    <i class="bi bi-lock me-1"></i>RD$ 0.00 (< 1 año) </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('vacations.show', $employee['id']) }}"
                                                    class="btn btn-outline-custom btn-sm" title="Ver historial">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if($employee['can_take_vacation'])
                                                    <a href="{{ route('vacations.create', ['employee_id' => $employee['id']]) }}"
                                                        class="btn btn-primary-custom btn-sm" title="Registrar vacaciones">
                                                        <i class="bi bi-plus"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-outline-custom btn-sm" disabled
                                                        title="Requiere 1 año de antigüedad">
                                                        <i class="bi bi-lock"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="bi bi-inbox display-4 text-white-50 d-block mb-3"></i>
                                            <p class="text-white-50">No se encontraron empleados</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-white mb-3">
                        <i class="bi bi-info-circle me-2"></i>Normativa y Cálculo de Pago de Vacaciones
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled small text-white mb-0">
                                <li class="mb-2 text-white">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Empleados con <strong>más de 12 meses y menos de 5 años</strong>: <strong
                                        class="text-white">14 días</strong> de salario ordinario al año.
                                </li>
                                <li class="mb-2 text-white">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Empleados con <strong>5 años o más</strong>: <strong class="text-white">18
                                        días</strong> de salario ordinario al año.
                                </li>
                                <li class="text-white">
                                    <i class="bi bi-pie-chart-fill text-info me-2"></i>
                                    <strong>Ingreso divisible:</strong> Si el empleado toma 5 días, se le pagan esos 5 días
                                    de salario ordinario diario (Salario / 23.83).
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled small text-white mb-0">
                                <li class="mb-2 text-white">
                                    <i class="bi bi-shield-check text-warning me-2"></i>
                                    <strong>Impuestos y TSS:</strong> El pago de vacaciones está sujeto a aportes a la TSS
                                    (SFS 3.04%, AFP 2.87%) y retención del IR-3 según la escala impositiva mensual.
                                </li>
                                <li class="mb-2 text-white">
                                    <i class="bi bi-envelope-paper-fill text-primary me-2"></i>
                                    <strong>Pago Independiente:</strong> Al pagar con el botón "Pagar vacaciones aprobadas",
                                    se genera un recibo/volante de pago automático y se notifica al empleado por correo
                                    electrónico.
                                </li>
                                <li class="text-white">
                                    <i class="bi bi-calendar2-range text-white-50 me-2"></i>
                                    Se excluyen días no laborables y festivos configurados por la empresa.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PARA PAGAR VACACIONES APROBADAS (PAGO INDEPENDIENTE) --}}
    @if(($company->vacation_payment_enabled ?? true) && ($company->vacation_payment_method ?? 'payroll') === 'separate')
        <div class="modal fade" id="payApprovedModal" tabindex="-1" aria-labelledby="payApprovedModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content"
                    style="background: #1e293b; color: white; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title d-flex align-items-center gap-2" id="payApprovedModalLabel">
                            <i class="bi bi-cash-coin text-success fs-4"></i>
                            <span>Pagar Vacaciones Aprobadas</span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if(isset($eligibleVacations) && $eligibleVacations->isNotEmpty())
                            <p class="text-white-50 mb-3">
                                Se procesará el pago de las vacaciones aprobadas cuyo disfrute inicia en un máximo de <strong>5 días
                                    posteriores</strong> a hoy (o ya iniciadas). Se generarán los registros de nómina con sus
                                descuentos de ley (TSS e ISR) y se enviará un volante digital por correo a cada empleado.
                            </p>

                            <div class="table-responsive mb-3" style="max-height: 320px; overflow-y: auto;">
                                <table class="table table-dark table-sm table-hover align-middle mb-0">
                                    <thead>
                                        <tr class="text-secondary" style="font-size: 0.8rem; text-transform: uppercase;">
                                            <th>Empleado</th>
                                            <th>Período</th>
                                            <th class="text-center">Días</th>
                                            <th class="text-end">Salario Diario</th>
                                            <th class="text-end">Monto Bruto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($eligibleVacations as $vac)
                                            <tr>
                                                <td>
                                                    <strong>{{ $vac->employee->user->name ?? 'N/A' }}</strong>
                                                    <br>
                                                    <small
                                                        class="text-white-50">{{ $vac->employee->department_rel->name ?? ($vac->employee->department ?? '') }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary">
                                                        {{ $vac->start_date ? $vac->start_date->format('d/m/Y') : '' }}
                                                        -
                                                        {{ $vac->end_date ? $vac->end_date->format('d/m/Y') : '' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-success">{{ $vac->days_taken }} días</span>
                                                </td>
                                                <td class="text-end">
                                                    RD$ {{ number_format($vac->employee->daily_salary, 2) }}
                                                </td>
                                                <td class="text-end fw-bold text-success">
                                                    RD$ {{ number_format($vac->calculated_amount, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="border-top border-secondary">
                                            <td colspan="4" class="fw-bold text-end">Total Bruto a Pagar:</td>
                                            <td class="fw-bold text-end text-success fs-6">
                                                RD$ {{ number_format($eligibleVacations->sum('calculated_amount'), 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="alert alert-info d-flex align-items-center gap-2 mb-0"
                                style="background: rgba(6,182,212,0.15); border: 1px solid rgba(6,182,212,0.3); color: #67e8f9; font-size: 0.82rem;">
                                <i class="bi bi-info-circle-fill fs-5 flex-shrink-0"></i>
                                <span>Al confirmar, el sistema aplicará automáticamente las deducciones de TSS (SFS + AFP) y la
                                    retención proporcional de ISR (IR-3), emitiendo los volantes individuales de inmediato.</span>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-calendar-check display-4 text-white-50 d-block mb-3"></i>
                                <h6 class="text-white">No hay vacaciones pendientes de pago para procesar</h6>
                                <p class="text-white-50 small mb-0">
                                    Todas las vacaciones aprobadas que inician en los próximos 5 días ya han sido pagadas o no hay
                                    solicitudes en ese rango de fechas.
                                </p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cerrar</button>
                        @if(isset($eligibleVacations) && $eligibleVacations->isNotEmpty())
                            <form method="POST" action="{{ route('vacations.pay-approved') }}">
                                @csrf
                                <button type="submit" class="btn btn-success d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Confirmar y Pagar ({{ $eligibleVacations->count() }})</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .stat-icon {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }

        .bg-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
        }

        .bg-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        }

        .bg-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        }

        .bg-info {
            background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%) !important;
        }

        .table tbody tr:hover {
            background: rgba(99, 102, 241, 0.05);
        }
    </style>
@endsection