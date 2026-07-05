@extends('layouts.app')
@section('title', 'Salario Navidad')
@section('page-title', 'Salario de Navidad')

@section('content')
    <ul class="nav nav-tabs mb-4 px-3" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('payroll.index') }}"
                style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
                Registros de Nómina
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('payroll.bonuses') }}"
                style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
                Bonificaciones de Ley
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('payroll.benefits') }}"
                style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
                Prestaciones Laborales
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('payroll.christmas') }}"
                style="color: white; border-bottom: 2px solid var(--primary); background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
                Salario Navidad
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('payroll.tss') }}"
                style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
                TSS
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('payroll.ir17') }}"
                style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
                IR-3
            </a>
        </li>
    </ul>

    <div class="card">
        <div class="card-header text-secondary d-flex justify-content-between align-items-center">
            <span>Cálculo de Salario de Navidad</span>
            <button onclick="window.print()" class="btn btn-sm btn-outline-custom">
                <i class="bi bi-printer me-1"></i> Imprimir Reporte
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Fecha de Ingreso</th>
                            <th>Meses Trabajados</th>
                            <th>Salario Mensual</th>
                            <th class="text-end">Salario Navidad</th>
                            <th class="text-center">Estado / Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalChristmas = 0; @endphp
                        @forelse($employees as $employee)
                            @if($employee->hire_date)
                                @php $totalChristmas += $employee->christmas_salary; @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $employee->user->name }}</td>
                                    <td>{{ $employee->hire_date->format('d/m/Y') }}</td>
                                    <td>{{ $employee->months_worked }}</td>
                                    <td>RD$ {{ number_format($employee->salary, 2) }}</td>
                                    <td class="text-end fw-bold" style="color: var(--success);">RD$
                                        {{ number_format($employee->christmas_salary, 2) }}</td>
                                    <td class="text-center">
                                        @if(in_array($employee->id, $paidEmployeeIds ?? []))
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                                <i class="bi bi-check-circle me-1"></i> Pagado
                                            </span>
                                        @else
                                            <form action="{{ route('payroll.christmas.pay', $employee) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerHTML = '<span class=\'spinner-border spinner-border-sm\' role=\'status\' aria-hidden=\'true\'></span>';">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-custom"
                                                    title="Marcar como pagado y enviar correo">
                                                    <i class="bi bi-envelope-check me-1"></i> Pagar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-dark">No hay empleados registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-primary bg-opacity-10 border-top border-primary">
                            <td colspan="4" class="fw-bold text-white text-end py-3">TOTAL SALARIO NAVIDAD:</td>
                            <td class="text-end fw-bold text-primary py-3" style="font-size: 1.1rem;">RD$
                                {{ number_format($totalChristmas, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection