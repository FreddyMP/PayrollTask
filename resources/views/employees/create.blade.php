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
                    
                    <ul class="nav nav-tabs mb-4" id="employeeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                <i class="bi bi-person me-1"></i> Información General
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ars-extras-tab" data-bs-toggle="tab" data-bs-target="#ars-extras" type="button" role="tab">
                                <i class="bi bi-plus-circle me-1"></i> ARS Extras
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="employeeTabsContent">
                        <!-- Pestaña Información General -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
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
                        </div>

                        <!-- Pestaña ARS Extras -->
                        <div class="tab-pane fade" id="ars-extras" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 text-secondary">Dependientes Adicionales (ARS Extra)</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddExtra">
                                    <i class="bi bi-plus-lg me-1"></i> Agregar Dependiente
                                </button>
                            </div>
                            
                            <div id="extrasContainer">
                                {{-- Las filas se agregarán dinámicamente aquí --}}
                            </div>

                            <div id="noExtrasMsg" class="text-center py-4 text-muted border rounded-3 bg-light bg-opacity-10 mb-4">
                                <i class="bi bi-info-circle me-1"></i> No se han agregado dependientes extras.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> Registrar Empleado</button>
                        <a href="{{ route('employees.index') }}" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>

                {{-- Template para filas de extras --}}
                <template id="extraRowTemplate">
                    <div class="extra-row card mb-3 border-0 shadow-sm" style="background: rgba(255,255,255,0.03);">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label small">Nombres y Apellidos</label>
                                    <input type="text" class="form-control form-control-sm" name="ars_extras[INDEX][name]" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Cédula</label>
                                    <input type="text" class="form-control form-control-sm" name="ars_extras[INDEX][id_number]">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Parentesco</label>
                                    <select class="form-select form-select-sm" name="ars_extras[INDEX][relationship]">
                                        <option value="Padre / Madre">Padre / Madre</option>
                                        <option value="Suegro / Suegra">Suegro / Suegra</option>
                                        <option value="Hijo / Hijastro mayor de edad">Hijo / Hijastro mayor de edad</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Fecha Nacimiento</label>
                                    <input type="date" class="form-control form-control-sm" name="ars_extras[INDEX][birth_date]">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Sexo</label>
                                    <select class="form-select form-select-sm" name="ars_extras[INDEX][sex]">
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Teléfono</label>
                                    <input type="text" class="form-control form-control-sm" name="ars_extras[INDEX][phone]">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Dirección</label>
                                    <input type="text" class="form-control form-control-sm" name="ars_extras[INDEX][address]">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small text-primary">Monto ARS</label>
                                    <input type="number" step="0.01" class="form-control form-control-sm border-primary" name="ars_extras[INDEX][ars_amount]" value="0.00" required>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger w-100 btnRemoveExtra">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let extraIndex = 0;
    const container = $('#extrasContainer');
    const template = $('#extraRowTemplate').html();
    const noExtrasMsg = $('#noExtrasMsg');

    function updateNoExtrasMsg() {
        if (container.children().length > 0) {
            noExtrasMsg.hide();
        } else {
            noExtrasMsg.show();
        }
    }

    $('#btnAddExtra').on('click', function() {
        const newRow = template.replace(/INDEX/g, extraIndex);
        container.append(newRow);
        extraIndex++;
        updateNoExtrasMsg();
    });

    $(document).on('click', '.btnRemoveExtra', function() {
        $(this).closest('.extra-row').remove();
        updateNoExtrasMsg();
    });
});
</script>
@endpush
