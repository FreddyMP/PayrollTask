@extends('layouts.app')
@section('title', 'Control de Fichaje')
@section('page-title', 'Control de Fichaje')

@section('content')
    <!-- Filtros -->
    <div class="card mb-4" style="background-color: #1e293b; border: 1px solid #334155;">
        <div class="card-body">
            <form method="GET" action="{{ route('fichajes.index') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size: 0.875rem; color: #94a3b8;">
                            <i class="bi bi-calendar me-1"></i>Fecha
                        </label>
                        <input type="date" class="form-control" name="date" 
                            value="{{ request('date', now()->toDateString()) }}"
                            style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-search me-1"></i>Buscar
                        </button>
                        <a href="{{ route('fichajes.index') }}" class="btn btn-outline-custom">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Hoy
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resultados -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Horas de Descanso</th>
                            <th>Total Horas Trab.</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fichajes as $fichaje)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--gradient-2); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: white; font-weight: 700;">
                                            {{ strtoupper(substr($fichaje->employee->user->name ?? '', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $fichaje->employee->user->name ?? '—' }}</div>
                                            <div style="font-size: 0.75rem; color: #64748b;">
                                                ID: {{ $fichaje->employee->id_number ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center text-success">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        {{ \Carbon\Carbon::parse($fichaje->clock_in)->format('h:i A') }}
                                    </div>
                                </td>
                                <td>
                                    @if($fichaje->clock_out)
                                        <div class="d-flex align-items-center text-danger">
                                            <i class="bi bi-box-arrow-left me-2"></i>
                                            {{ \Carbon\Carbon::parse($fichaje->clock_out)->format('h:i A') }}
                                        </div>
                                    @else
                                        <span class="badge-status badge-pending">Sin salida</span>
                                    @endif
                                </td>
                                <td>
                                    @if($fichaje->break_start && $fichaje->break_end)
                                        <div class="text-info" style="font-size: 0.85rem;">
                                            <i class="bi bi-cup-hot me-1"></i>
                                            {{ \Carbon\Carbon::parse($fichaje->break_start)->format('h:i A') }} - 
                                            {{ \Carbon\Carbon::parse($fichaje->break_end)->format('h:i A') }}
                                        </div>
                                    @else
                                        <span style="color: #64748b; font-size: 0.85rem;">Sin descanso</span>
                                    @endif
                                </td>
                                <td style="font-weight: 600;">
                                    @if($fichaje->clock_out)
                                        {{ $fichaje->total_hours }} hrs
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($fichaje->clock_out)
                                        <span class="badge-status badge-completed">Completado</span>
                                    @else
                                        <span class="badge-status badge-in-progress">En Turno</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">No hay registros de fichaje para esta fecha</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
