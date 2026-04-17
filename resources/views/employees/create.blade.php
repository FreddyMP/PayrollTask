@extends('layouts.app')
@section('title', 'Nuevo Empleado')
@section('page-title', 'Nuevo Empleado')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-person-plus me-2"></i>Registrar Empleado</div>
            <div class="card-body">
                <form method="POST" action="{{ route('employees.store') }}">
                    @csrf
                    <h6 class="mb-3" style="color: var(--primary-light);">Información de Cuenta</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" required minlength="8">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Rol</label>
                            <select class="form-select" name="role">
                                <option value="usuario">Usuario</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cargo</label>
                            <input type="text" class="form-control" name="position" value="{{ old('position') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cédula</label>
                            <input type="text" class="form-control" name="id_number" value="{{ old('id_number') }}" placeholder="001-0000000-0">
                        </div>
                    </div>

                    <h6 class="mb-3 text-secondary">Información Laboral</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Departamento</label>
                            <input type="text" class="form-control" name="department" value="{{ old('department') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Salario</label>
                            <input type="number" step="0.01" class="form-control" name="salary" value="{{ old('salary') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha de Ingreso</label>
                            <input type="date" class="form-control" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipo de Contrato</label>
                            <select class="form-select" name="contract_type">
                                <option value="full_time">Tiempo Completo</option>
                                <option value="part_time">Medio Tiempo</option>
                                <option value="contractor">Contratista</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Cuenta Bancaria</label>
                            <input type="text" class="form-control" name="bank_account" value="{{ old('bank_account') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-warning"><i class="bi bi-clock me-1"></i>Hora de Entrada</label>
                            <input type="time" class="form-control" name="work_start" value="{{ old('work_start', '08:00') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-warning"><i class="bi bi-clock-fill me-1"></i>Hora de Salida</label>
                            <input type="time" class="form-control" name="work_end" value="{{ old('work_end', '17:00') }}" required>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> Registrar Empleado</button>
                        <a href="{{ route('employees.index') }}" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
