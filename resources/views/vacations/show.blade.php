@extends('layouts.app')

@section('title', 'Historial de Vacaciones')
@section('page-title', 'Historial de Vacaciones')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('vacations.index') }}" class="btn btn-outline-custom btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Volver a Vacaciones
            </a>
        </div>
    </div>

    <!-- Employee Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-primary p-3 rounded-3 me-3">
                                    <i class="bi bi-person-circle text-white fs-3"></i>
                                </div>
                                <div>
                                    <h4 class="text-white mb-1">{{ $employee->user->name ?? 'N/A' }}</h4>
                                    <p class="text-white mb-2">{{ $employee->user->email ?? 'N/A' }}</p>
                                    <div class="d-flex gap-3 flex-wrap">
                                        <span class="badge badge-status badge-supervisor">
                                            <i class="bi bi-briefcase me-1"></i>
                                            {{ $employee->position->name ?? 'N/A' }}
                                        </span>
                                        <span class="badge badge-status badge-info">
                                            <i class="bi bi-building me-1"></i>
                                            {{ $employee->department_rel->name ?? $employee->department ?? 'N/A' }}
                                        </span>
                                        <span class="badge badge-status badge-active">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            {{ $employee->years_of_service }} años de antigüedad
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('vacations.create', ['employee_id' => $employee->id]) }}"
                                class="btn btn-primary-custom">
                                <i class="bi bi-plus-circle me-2"></i>Registrar Vacaciones
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Year Summary -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary p-3 rounded-3 me-3">
                            <i class="bi bi-calendar2-check text-white fs-5"></i>
                        </div>
                        <div>
                            <h6 class="text-white small mb-0">Días Correspondientes</h6>
                            <h3 class="text-white mb-0">{{ $employee->vacation_days_entitled }}</h3>
                            <small class="text-white">
                                {{ $employee->years_of_service >= 5 ? '5+ años' : '< 5 años' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success p-3 rounded-3 me-3">
                            <i class="bi bi-check-circle text-white fs-5"></i>
                        </div>
                        <div>
                            <h6 class="text-white small mb-0">Días Tomados ({{ now()->year }})</h6>
                            <h3 class="text-white mb-0">{{ $employee->getVacationDaysTaken() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning p-3 rounded-3 me-3">
                            <i class="bi bi-hourglass-split text-white fs-5"></i>
                        </div>
                        <div>
                            <h6 class="text-white small mb-0">Días Restantes</h6>
                            <h3 class="text-white mb-0">{{ $employee->getVacationDaysRemaining() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vacation History -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <h6 class="text-white mb-0">
                        <i class="bi bi-clock-history me-2"></i>Historial de Vacaciones
                    </h6>
                </div>
                <div class="card-body p-0">
                    @forelse($vacations as $year => $yearVacations)
                        <div class="p-4 border-bottom border-secondary">
                            <h6 class="text-white mb-3">
                                <i class="bi bi-calendar3 me-2"></i>Año {{ $year }}
                                <span class="badge badge-status badge-info ms-2">
                                    {{ collect($yearVacations)->sum('days_taken') }} días tomados
                                </span>
                            </h6>

                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Período</th>
                                            <th>Días</th>
                                            <th>Monto Bruto</th>
                                            <th>Estado Pago</th>
                                            <th>Origen</th>
                                            <th>Notas</th>
                                            <th>Registrado por</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($yearVacations as $vacation)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-calendar-range text-primary me-2"></i>
                                                        <div>
                                                            <strong class="text-dark">
                                                                {{ \Carbon\Carbon::parse($vacation['start_date'])->format('d/m/Y') }}
                                                                -
                                                                {{ \Carbon\Carbon::parse($vacation['end_date'])->format('d/m/Y') }}
                                                            </strong>
                                                            <br>
                                                            <small class="text-dark">
                                                                {{ \Carbon\Carbon::parse($vacation['start_date'])->diffInDays(\Carbon\Carbon::parse($vacation['end_date'])) + 1 }}
                                                                días naturales
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-status badge-completed">
                                                        {{ $vacation['days_taken'] }} días hábiles
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong class="text-success">
                                                        RD$ {{ number_format($vacation['amount'], 2) }}
                                                    </strong>
                                                </td>
                                                <td>
                                                    @if($vacation['is_paid'])
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle"
                                                            title="Pagado el {{ $vacation['paid_at'] ? $vacation['paid_at']->format('d/m/Y') : '' }}">
                                                            <i class="bi bi-check-circle-fill me-1"></i>Pagado
                                                        </span>
                                                    @elseif(($company->vacation_payment_method ?? 'payroll') === 'payroll')
                                                        <span class="badge bg-indigo-subtle text-primary border border-primary-subtle"
                                                            style="font-size: 0.75rem;">
                                                            <i class="bi bi-calendar-event me-1"></i>En Nómina
                                                        </span>
                                                    @elseif($vacation['is_eligible'])
                                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle"
                                                            style="font-size: 0.75rem;">
                                                            <i class="bi bi-hourglass-top me-1"></i>Listo para pago
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-white-50"
                                                            style="font-size: 0.75rem;">
                                                            <i class="bi bi-clock me-1"></i>Pendiente
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($vacation['source'] === 'request_module')
                                                        <span class="badge source-badge source-request"
                                                            title="Solicitado desde el módulo de Permisos">
                                                            <i class="bi bi-file-earmark-check me-1"></i>Permisos
                                                        </span>
                                                    @else
                                                        <span class="badge source-badge source-vacation"
                                                            title="Registrado directamente en Gestión de Vacaciones">
                                                            <i class="bi bi-calendar2-check me-1"></i>Vacaciones
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($vacation['notes'])
                                                        <small class="text-dark">{{ Str::limit($vacation['notes'], 40) }}</small>
                                                    @else
                                                        <small class="text-dark">-</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-dark">
                                                        {{ $vacation['created_by'] }}
                                                        <br>
                                                        {{ \Carbon\Carbon::parse($vacation['created_at'])->format('d/m/Y') }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    @if($vacation['source'] === 'vacation_module')
                                                        <div class="btn-group btn-group-sm">
                                                            @if(!$vacation['is_paid'] && ($company->vacation_payment_method ?? 'payroll') === 'separate' && $vacation['is_eligible'])
                                                                <form action="{{ route('vacations.pay-single', $vacation['vacation_id']) }}"
                                                                    method="POST" class="d-inline"
                                                                    onsubmit="return confirm('¿Procesar pago de vacaciones de {{ $vacation['days_taken'] }} días (RD$ {{ number_format($vacation['amount'], 2) }}) y enviar volante al empleado?')">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-outline-success btn-sm"
                                                                        title="Pagar vacaciones y enviar volante">
                                                                        <i class="bi bi-cash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            <a href="{{ route('vacations.edit', $vacation['vacation_id']) }}"
                                                                class="btn btn-outline-custom btn-sm" title="Editar">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <form action="{{ route('vacations.destroy', $vacation['vacation_id']) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('¿Eliminar este registro de vacaciones?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                    title="Eliminar">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @else
                                                        <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary btn-sm"
                                                            title="Ver en módulo de Permisos">
                                                            <i class="bi bi-box-arrow-up-right"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x display-4 text-white d-block mb-3"></i>
                            <h6 class="text-white mb-2">No hay registros de vacaciones</h6>
                            <p class="text-white mb-4">Este empleado aún no ha tomado vacaciones</p>
                            <a href="{{ route('vacations.create', ['employee_id' => $employee->id]) }}"
                                class="btn btn-primary-custom">
                                <i class="bi bi-plus-circle me-2"></i>Registrar Primera Vacación
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

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

        .table tbody tr:hover {
            background: rgba(99, 102, 241, 0.05);
        }

        .source-badge {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.3em 0.65em;
            border-radius: 6px;
        }

        .source-vacation {
            background: rgba(99, 102, 241, 0.18);
            color: #a5b4fc;
            border: 1px solid rgba(99, 102, 241, 0.35);
        }

        .source-request {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
    </style>
@endsection