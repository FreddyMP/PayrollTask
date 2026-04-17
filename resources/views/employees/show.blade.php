@extends('layouts.app')
@section('title', 'Detalle Empleado')
@section('page-title', 'Detalle del Empleado')

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div style="width: 80px; height: 80px; border-radius: 20px; background: linear-gradient(135deg, #6366f1, #a855f7); display: inline-flex; align-items: center; justify-content: center; font-size: 1.8rem; color: white; font-weight: 700; margin-bottom: 1rem;">
                    {{ strtoupper(substr($employee->user->name ?? '', 0, 2)) }}
                </div>
                <h5 class="mb-1" style="color: white;">{{ $employee->user->name }}</h5>
                <p style="font-size: 0.85rem; color: #64748b;">{{ $employee->user->position ?? '—' }}</p>
                <span class="badge-status badge-{{ $employee->user->role }}">{{ ucfirst($employee->user->role) }}</span>
                <span class="badge-status badge-{{ $employee->user->status }}">{{ ucfirst($employee->user->status) }}</span>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header text-secondary">Información de Contacto</div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-white">Email</small>
                    <div style="color: var(--success);" >{{ $employee->user->email }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-white">Teléfono</small>
                    <div style="color: var(--success);">{{ $employee->user->phone ?? '—' }}</div>
                </div>
                <div>
                    <small class="text-white">Cédula</small>
                    <div style="color: var(--success);">{{ $employee->id_number ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header text-secondary">Información Laboral</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <small class="text-white">Departamento</small>
                        <div class="fw-semibold" style="color: var(--success);">{{ $employee->department ?? '—' }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-white">Salario</small>
                        <div class="fw-semibold" style="color: var(--success);">RD$ {{ number_format($employee->salary, 2) }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-white">Fecha de Ingreso</small>
                        <div class="fw-semibold" style="color: var(--success);">{{ $employee->hire_date?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-white">Tipo de Contrato</small>
                        <div class="fw-semibold" style="color: var(--success);">
                            @php $contracts = ['full_time' => 'Tiempo Completo', 'part_time' => 'Medio Tiempo', 'contractor' => 'Contratista']; @endphp
                            {{ $contracts[$employee->contract_type] ?? $employee->contract_type }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-white">Cuenta Bancaria</small>
                        <div class="fw-semibold" style="color: var(--success);">{{ $employee->bank_account ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center text-secondary">
                <span>Historial de Nómina</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Período</th>
                                <th>Bruto</th>
                                <th>Deducciones</th>
                                <th>Neto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employee->payrolls as $payroll)
                            <tr>
                                <td>{{ $payroll->period }}</td>
                                <td>RD$ {{ number_format($payroll->gross_salary, 2) }}</td>
                                <td style="color: #f87171;">-RD$ {{ number_format($payroll->deductions, 2) }}</td>
                                <td class="fw-semibold" style="color: var(--success);">RD$ {{ number_format($payroll->net_salary, 2) }}</td>
                                <td><span class="badge-status badge-{{ $payroll->status }}">{{ ucfirst($payroll->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Sin registros de nómina</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('employees.index') }}" class="btn btn-outline-custom"><i class="bi bi-arrow-left me-1"></i> Volver</a>
    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary-custom"><i class="bi bi-pencil me-1"></i> Editar</a>
</div>
@endsection
