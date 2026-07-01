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
                                    {{ $yearVacations->sum('days_taken') }} días tomados
                                </span>
                            </h6>

                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th>Período</th>
                                            <th>Días</th>
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
                                                            <strong class="text-white">
                                                                {{ $vacation->start_date->format('d/m/Y') }}
                                                                -
                                                                {{ $vacation->end_date->format('d/m/Y') }}
                                                            </strong>
                                                            <br>
                                                            <small class="text- white">
                                                                {{ $vacation->start_date->diffInDays($vacation->end_date) + 1 }}
                                                                días naturales
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-status badge-completed">
                                                        {{ $vacation->days_taken }} días hábiles
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($vacation->notes)
                                                        <small class="text- white">{{ Str::limit($vacation->notes, 50) }}</small>
                                                    @else
                                                        <small class="text- white">-</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text- white">
                                                        {{ $vacation->creator->name ?? 'N/A' }}
                                                        <br>
                                                        {{ $vacation->created_at->format('d/m/Y') }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('vacations.edit', $vacation) }}"
                                                            class="btn btn-outline-custom btn-sm" title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('vacations.destroy', $vacation) }}" method="POST"
                                                            class="d-inline"
                                                            onsubmit="return confirm('¿Eliminar este registro de vacaciones?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                                title="Eliminar">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
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
    </style>
@endsection