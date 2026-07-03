<?php $__env->startSection('title', 'Editar Empleado'); ?>
<?php $__env->startSection('page-title', 'Editar Empleado'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-pencil me-2"></i>Editar Empleado: <?php echo e($employee->user->name); ?></div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('employees.update', $employee)); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

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
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                                <i class="bi bi-file-earmark-text me-1"></i> Documentos
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
                                    <input type="text" class="form-control" name="name" value="<?php echo e(old('name', $employee->user->name)); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo e(old('email', $employee->user->email)); ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" name="status">
                                        <option value="active" <?php echo e($employee->user->status == 'active' ? 'selected' : ''); ?>>Activo</option>
                                        <option value="inactive" <?php echo e($employee->user->status == 'inactive' ? 'selected' : ''); ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label">Rol</label>
                                    <select class="form-select" name="role">
                                        <option value="usuario" <?php echo e($employee->user->role == 'usuario' ? 'selected' : ''); ?>>Usuario</option>
                                        <option value="supervisor" <?php echo e($employee->user->role == 'supervisor' ? 'selected' : ''); ?>>Supervisor</option>
                                        <option value="admin" <?php echo e($employee->user->role == 'admin' ? 'selected' : ''); ?>>Administrador</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" name="phone" value="<?php echo e(old('phone', $employee->user->phone)); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cargo</label>
                                    <?php
                                        $selectedPositionId = old('position_id', $employee->position_id);
                                        if (!$selectedPositionId && $employee->user->position) {
                                            $matchedPosition = $positions->firstWhere('title', $employee->user->position);
                                            $selectedPositionId = $matchedPosition?->id;
                                        }
                                    ?>
                                    <select class="form-select" name="position_id">
                                        <option value="">Seleccionar...</option>
                                        <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($position->id); ?>" <?php echo e($selectedPositionId == $position->id ? 'selected' : ''); ?>>
                                                <?php echo e($position->title); ?><?php if($position->department): ?> (<?php echo e($position->department->name); ?>)<?php endif; ?>
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Cédula</label>
                                    <input type="text" class="form-control" name="id_number" value="<?php echo e(old('id_number', $employee->id_number)); ?>">
                                </div>
                            </div>

                            <h6 class="mb-3" style="color: var(--primary-light);">Información Laboral</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Salario</label>
                                    <input type="number" step="0.01" class="form-control" name="salary" value="<?php echo e(old('salary', $employee->salary)); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha de Ingreso</label>
                                    <input type="date" class="form-control" name="hire_date" value="<?php echo e(old('hire_date', $employee->hire_date?->format('Y-m-d'))); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tipo de Contrato</label>
                                    <select class="form-select" name="contract_type">
                                        <option value="full_time" <?php echo e($employee->contract_type == 'full_time' ? 'selected' : ''); ?>>Tiempo Completo</option>
                                        <option value="part_time" <?php echo e($employee->contract_type == 'part_time' ? 'selected' : ''); ?>>Medio Tiempo</option>
                                        <option value="contractor" <?php echo e($employee->contract_type == 'contractor' ? 'selected' : ''); ?>>Contratista</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Cuenta Bancaria</label>
                                    <input type="text" class="form-control" name="bank_account" value="<?php echo e(old('bank_account', $employee->bank_account)); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-warning"><i class="bi bi-clock me-1"></i>Hora de Entrada</label>
                                    <input type="time" class="form-control" name="work_start" value="<?php echo e(old('work_start', \Carbon\Carbon::parse($employee->work_start)->format('H:i'))); ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label text-warning"><i class="bi bi-clock-fill me-1"></i>Hora de Salida</label>
                                    <input type="time" class="form-control" name="work_end" value="<?php echo e(old('work_end', \Carbon\Carbon::parse($employee->work_end)->format('H:i'))); ?>" required>
                                </div>
                            </div>

                            <h6 class="mb-3 text-secondary">Horas de Descanso en la Jornada</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label text-info"><i class="bi bi-cup-hot me-1"></i>Inicio de Descanso (Opcional)</label>
                                    <input type="time" class="form-control" name="break_start" value="<?php echo e(old('break_start', $employee->break_start ? \Carbon\Carbon::parse($employee->break_start)->format('H:i') : '')); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-info"><i class="bi bi-cup-hot-fill me-1"></i>Fin de Descanso (Opcional)</label>
                                    <input type="time" class="form-control" name="break_end" value="<?php echo e(old('break_end', $employee->break_end ? \Carbon\Carbon::parse($employee->break_end)->format('H:i') : '')); ?>">
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
                                <?php $__currentLoopData = $employee->arsExtras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $extra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="extra-row card mb-3 border-0 shadow-sm" style="background: rgba(255,255,255,0.03);">
                                    <div class="card-body p-3">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label small">Nombres y Apellidos</label>
                                                <input type="text" class="form-control form-control-sm" name="ars_extras[<?php echo e($index); ?>][name]" value="<?php echo e($extra->name); ?>" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small">Cédula</label>
                                                <input type="text" class="form-control form-control-sm" name="ars_extras[<?php echo e($index); ?>][id_number]" value="<?php echo e($extra->id_number); ?>">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small">Parentesco</label>
                                                <select class="form-select form-select-sm" name="ars_extras[<?php echo e($index); ?>][relationship]">
                                                    <option value="Padre / Madre" <?php echo e($extra->relationship == 'Padre / Madre' ? 'selected' : ''); ?>>Padre / Madre</option>
                                                    <option value="Suegro / Suegra" <?php echo e($extra->relationship == 'Suegro / Suegra' ? 'selected' : ''); ?>>Suegro / Suegra</option>
                                                    <option value="Hijo / Hijastro mayor de edad" <?php echo e($extra->relationship == 'Hijo / Hijastro mayor de edad' ? 'selected' : ''); ?>>Hijo / Hijastro mayor de edad</option>
                                                    <option value="Otro" <?php echo e($extra->relationship == 'Otro' ? 'selected' : ''); ?>>Otro</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small">Fecha Nacimiento</label>
                                                <input type="date" class="form-control form-control-sm" name="ars_extras[<?php echo e($index); ?>][birth_date]" value="<?php echo e($extra->birth_date?->format('Y-m-d')); ?>">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small">Sexo</label>
                                                <select class="form-select form-select-sm" name="ars_extras[<?php echo e($index); ?>][sex]">
                                                    <option value="M" <?php echo e($extra->sex == 'M' ? 'selected' : ''); ?>>Masculino</option>
                                                    <option value="F" <?php echo e($extra->sex == 'F' ? 'selected' : ''); ?>>Femenino</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small">Teléfono</label>
                                                <input type="text" class="form-control form-control-sm" name="ars_extras[<?php echo e($index); ?>][phone]" value="<?php echo e($extra->phone); ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small">Dirección</label>
                                                <input type="text" class="form-control form-control-sm" name="ars_extras[<?php echo e($index); ?>][address]" value="<?php echo e($extra->address); ?>">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small text-primary">Monto ARS</label>
                                                <input type="number" step="0.01" class="form-control form-control-sm border-primary" name="ars_extras[<?php echo e($index); ?>][ars_amount]" value="<?php echo e($extra->ars_amount); ?>" required>
                                            </div>
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger w-100 btnRemoveExtra">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <div id="noExtrasMsg" class="text-center py-4 text-white border rounded-3 bg-light bg-opacity-10 mb-4" style="<?php echo e($employee->arsExtras->count() > 0 ? 'display: none;' : ''); ?>">
                                <i class="bi bi-info-circle me-1"></i> No se han agregado dependientes extras.
                            </div>
                        </div>

                        <!-- Pestaña Documentos -->
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 text-secondary">Documentos del Empleado</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddDocument">
                                    <i class="bi bi-plus-lg me-1"></i> Agregar Otro Documento
                                </button>
                            </div>

                            <div id="existingDocuments" class="mb-4">
                                <?php if($employee->documents->count() > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Documento</th>
                                                    <th>Fecha</th>
                                                    <th class="text-end">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $employee->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($doc->name); ?></td>
                                                    <td><?php echo e($doc->created_at->format('d/m/Y')); ?></td>
                                                    <td class="text-end">
                                                        <a href="<?php echo e(Storage::url($doc->file_path)); ?>" target="_blank" class="btn btn-sm btn-outline-info me-1">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-danger btnDeleteExistingDoc" data-id="<?php echo e($doc->id); ?>">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-3 text-white border rounded-3 bg-light bg-opacity-10 mb-3">
                                        <i class="bi bi-info-circle me-1"></i> No hay documentos guardados.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div id="documentsContainer">
                                
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> Guardar Cambios</button>
                        <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>

                
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

                
                <template id="documentRowTemplate">
                    <div class="document-row card mb-3 border-0 shadow-sm" style="background: rgba(255,255,255,0.03);">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label small">Nombre del Documento</label>
                                    <input type="text" class="form-control form-control-sm" name="documents[INDEX][name]" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">Archivo</label>
                                    <input type="file" class="form-control form-control-sm" name="documents[INDEX][file]" required>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger w-100 btnRemoveDocument">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                
                <form id="deleteDocForm" method="POST" style="display: none;">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    // Calcular el siguiente índice basado en las filas existentes
    let extraIndex = <?php echo e($employee->arsExtras->count()); ?>;
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

    // Documentos
    let docIndex = 0;
    const docContainer = $('#documentsContainer');
    const docTemplate = $('#documentRowTemplate').html();

    $('#btnAddDocument').on('click', function() {
        const newRow = docTemplate.replace(/INDEX/g, docIndex);
        docContainer.append(newRow);
        docIndex++;
    });

    $(document).on('click', '.btnRemoveDocument', function() {
        $(this).closest('.document-row').remove();
    });

    $('.btnDeleteExistingDoc').on('click', function() {
        if (confirm('¿Estás seguro de eliminar este documento?')) {
            const id = $(this).data('id');
            const form = $('#deleteDocForm');
            form.attr('action', `/employees/documents/${id}`);
            form.submit();
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/employees/edit.blade.php ENDPATH**/ ?>