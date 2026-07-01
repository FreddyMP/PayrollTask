@extends('layouts.app')

@section('title', 'Gestión de Vacaciones')
@section('page-title', 'Gestión de Vacaciones')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="text-white mb-1">Control de Vacaciones {{ $year }}</h5>
                    <p class="small text-white mb-0">Administra y monitorea las vacaciones de los empleados</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('vacations.create') }}" class="btn btn-primary-custom">
                        <i class="bi bi-plus-circle me-2"></i>Registrar Vacaciones
                    </a>
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
                            <i class="bi bi-hourglass-split text-white fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-white small mb-0">Días Disponibles</h6>
                            <h3 class="text-white mb-0">{{ $employees->sum('days_entitled') }}</h3>
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
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <h6 class="text-white mb-0">
                        <i class="bi bi-list-ul me-2"></i>Lista de Empleados
                    </h6>
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
                                    <th class="text-center">Días Correspondientes</th>
                                    <th class="text-center">Días Tomados</th>
                                    <th class="text-center">Días Restantes</th>
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
                                        <td>{{ $employee['department'] }}</td>
                                        <td>{{ $employee['position'] }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-status badge-supervisor">
                                                {{ number_format($employee['years_of_service'], 1) }} años
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <strong class="text-dark">{{ $employee['days_entitled'] }}</strong>
                                        </td>
                                        <td class="text-center">
                                            @if($employee['days_taken'] > 0)
                                                <span class="badge badge-status badge-completed">
                                                    {{ $employee['days_taken'] }} días
                                                </span>
                                            @else
                                                <span class="text-dark">0 días</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $remaining = $employee['days_remaining'];
                                                $percentage = $employee['days_entitled'] > 0
                                                    ? ($remaining / $employee['days_entitled']) * 100
                                                    : 0;
                                            @endphp

                                            @if($percentage > 60)
                                                <span class="badge badge-status badge-active text-dark">
                                                    {{ $remaining }} días
                                                </span>
                                            @elseif($percentage > 30)
                                                <span class="badge badge-status badge-warning text-dark">
                                                    {{ $remaining }} días
                                                </span>
                                            @else
                                                <span class="badge badge-status badge-danger text-dark">
                                                    {{ $remaining }} días
                                                </span>
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
                                        <td colspan="8" class="text-center py-5">
                                            <i class="bi bi-inbox display-4 text-dark d-block mb-3"></i>
                                            <p class="text-dark">No se encontraron empleados</p>
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
    <div class="row mt-3">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-white mb-3">
                        <i class="bi bi-info-circle me-2"></i>Información
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled small text-white mb-0">
                                <li class="mb-2 text-white">
                                    <i class="bi bi-check-circle-fill text-white me-2"></i>
                                    Empleados con <strong>menos de 5 años</strong> de antigüedad: <strong
                                        class="text-white">14 días</strong> de vacaciones anuales
                                </li>
                                <li class="text-white">
                                    <i class="bi bi-check-circle-fill text-white me-2"></i>
                                    Empleados con <strong>5 años o más</strong> de antigüedad: <strong class="text-white">18
                                        días</strong> de vacaciones anuales
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled small text-white mb-0">
                                <li class="mb-2 text-white">
                                    <i class="bi bi-calendar-x me-2"></i>
                                    Los días se calculan excluyendo <strong>días festivos</strong> y <strong>días de
                                        descanso</strong> configurados en el calendario
                                </li>
                                <li class="mb-2 text-white">
                                    <i class="bi bi-clock-history me-2"></i>
                                    La antigüedad se calcula desde la <strong>fecha de contratación</strong> del empleado
                                </li>
                                <li>
                                    <i class="bi bi-shield-check text-white me-2"></i>
                                    Solo empleados con <strong>1 año o más</strong> de antigüedad pueden tomar vacaciones
                                </li>
                            </ul>
                        </div>
                    </div>
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

        .bg-info {
            background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%) !important;
        }

        .table tbody tr:hover {
            background: rgba(99, 102, 241, 0.05);
        }
    </style>
@endsection