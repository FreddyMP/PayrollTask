@extends('layouts.app')
@section('title', 'Empleados')
@section('page-title', 'Empleados')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <form method="GET" action="{{ route('employees.index') }}" class="d-flex gap-2">
        <input type="text" class="form-control form-control-sm" name="search" placeholder="Buscar empleado..." value="{{ request('search') }}" style="width: 250px;">
        <button type="submit" class="btn btn-outline-custom btn-sm"><i class="bi bi-search"></i></button>
    </form>
    <a href="{{ route('employees.create') }}" class="btn btn-primary-custom">
        <i class="bi bi-person-plus me-1"></i> Nuevo Empleado
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Empleado</th>
                        <th>Rol</th>
                        <th>Cargo</th>
                        <th>Departamento</th>
                        <th>Salario</th>
                        <th>Contrato</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #6366f1, #a855f7); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: white; font-weight: 700;">
                                    {{ strtoupper(substr($emp->user->name ?? '', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $emp->user->name ?? '—' }}</div>
                                    <div style="font-size: 0.75rem; color: #64748b;">{{ $emp->user->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge-status badge-{{ $emp->user->role ?? '' }}">{{ ucfirst($emp->user->role ?? '') }}</span></td>
                        <td>{{ $emp->user->position ?? '—' }}</td>
                        <td>{{ $emp->department ?? '—' }}</td>
                        <td style="font-weight: 600;">RD$ {{ number_format($emp->salary, 2) }}</td>
                        <td>
                            @php
                                $contractLabels = ['full_time' => 'Tiempo Completo', 'part_time' => 'Medio Tiempo', 'contractor' => 'Contratista'];
                            @endphp
                            {{ $contractLabels[$emp->contract_type] ?? $emp->contract_type }}
                        </td>
                        <td><span class="badge-status badge-{{ $emp->user->status ?? '' }}">{{ ucfirst($emp->user->status ?? '') }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('employees.show', $emp) }}" class="btn btn-outline-custom btn-sm" title="Ver"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('employees.edit', $emp) }}" class="btn btn-outline-custom btn-sm" title="Editar"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('employees.destroy', $emp) }}" class="d-inline" onsubmit="return confirm('¿Eliminar este empleado?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-custom btn-sm" style="color: #f87171;" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No hay empleados registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $employees->links() }}</div>
@endsection
