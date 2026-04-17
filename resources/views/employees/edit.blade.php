@extends('layouts.app')
@section('title', 'Editar Empleado')
@section('page-title', 'Editar Empleado')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-pencil me-2"></i>Editar Empleado: {{ $employee->user->name }}</div>
            <div class="card-body">
                <form method="POST" action="{{ route('employees.update', $employee) }}">
                    @csrf @method('PUT')
                    <h6 class="mb-3" style="color: var(--primary-light);">Información de Cuenta</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $employee->user->name) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email', $employee->user->email) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="status">
                                <option value="active" {{ $employee->user->status == 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ $employee->user->status == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Rol</label>
                            <select class="form-select" name="role">
                                <option value="usuario" {{ $employee->user->role == 'usuario' ? 'selected' : '' }}>Usuario</option>
                                <option value="supervisor" {{ $employee->user->role == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                <option value="admin" {{ $employee->user->role == 'admin' ? 'selected' : '' }}>Administrador</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="phone" value="{{ old('phone', $employee->user->phone) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cargo</label>
                            <input type="text" class="form-control" name="position" value="{{ old('position', $employee->user->position) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cédula</label>
                            <input type="text" class="form-control" name="id_number" value="{{ old('id_number', $employee->id_number) }}">
                        </div>
                    </div>

                    <h6 class="mb-3" style="color: var(--primary-light);">Información Laboral</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Departamento</label>
                            <input type="text" class="form-control" name="department" value="{{ old('department', $employee->department) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Salario</label>
                            <input type="number" step="0.01" class="form-control" name="salary" value="{{ old('salary', $employee->salary) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha de Ingreso</label>
                            <input type="date" class="form-control" name="hire_date" value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipo de Contrato</label>
                            <select class="form-select" name="contract_type">
                                <option value="full_time" {{ $employee->contract_type == 'full_time' ? 'selected' : '' }}>Tiempo Completo</option>
                                <option value="part_time" {{ $employee->contract_type == 'part_time' ? 'selected' : '' }}>Medio Tiempo</option>
                                <option value="contractor" {{ $employee->contract_type == 'contractor' ? 'selected' : '' }}>Contratista</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Cuenta Bancaria</label>
                            <input type="text" class="form-control" name="bank_account" value="{{ old('bank_account', $employee->bank_account) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-warning"><i class="bi bi-clock me-1"></i>Hora de Entrada</label>
                            <input type="time" class="form-control" name="work_start" value="{{ old('work_start', \Carbon\Carbon::parse($employee->work_start)->format('H:i')) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-warning"><i class="bi bi-clock-fill me-1"></i>Hora de Salida</label>
                            <input type="time" class="form-control" name="work_end" value="{{ old('work_end', \Carbon\Carbon::parse($employee->work_end)->format('H:i')) }}" required>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> Guardar Cambios</button>
                        <a href="{{ route('employees.index') }}" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
