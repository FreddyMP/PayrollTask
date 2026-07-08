<?php $__env->startSection('title', 'Gestionar Plantilla'); ?>
<?php $__env->startSection('page-title', 'Plantilla: ' . $template->title); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-8">
            <ul class="nav nav-tabs border-0 mb-3" id="templateTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active text-white fw-bold border-0 bg-transparent" id="vars-tab"
                        data-bs-toggle="tab" data-bs-target="#vars" type="button" role="tab">
                        <i class="bi bi-tags-fill me-2"></i>Variables de Plantilla
                    </button>
                </li>
                <!--
                    <li class="nav-item">
                        <button class="nav-link text-white fw-bold border-0 bg-transparent opacity-50" id="content-tab" data-bs-toggle="tab" data-bs-target="#contentTemplate" type="button" role="tab">
                            <i class="bi bi-file-earmark-text me-2"></i>Contenido de Plantilla
                        </button>
                    </li>
                    -->
            </ul>

            <div class="tab-content" id="templateTabsContent">
                <!-- Variables Tab -->
                <div class="tab-pane fade show active" id="vars" role="tabpanel">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-dark-2 py-3 d-flex justify-content-between align-items-center">
                            <span class="text-white fw-bold">Gestión de Variables de Plantilla</span>
                            <!--
                                <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addVariableModal">
                                    <i class="bi bi-plus-circle me-1"></i>Nueva
                                </button>
                                -->
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Valor</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $configuredFieldNames = $fields->pluck('name')->map(fn($n) => strtolower($n))->toArray();
                                            $systemVars = ['empresa_nombre', 'empresa_rnc', 'empresa_direccion', 'nombre_empleado', 'email_empleado'];

                                            $unconfiguredVariables = collect($extractedVariables ?? [])->filter(function ($var) use ($configuredFieldNames, $systemVars) {
                                                return !in_array(strtolower($var), $configuredFieldNames) && !in_array(strtolower($var), $systemVars);
                                            })->unique();
                                        ?>

                                        <?php $__currentLoopData = $unconfiguredVariables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $unconfiguredVar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="table-warning opacity-75">
                                                <td><code class="text-primary-light">&lt;# <?php echo e($unconfiguredVar); ?> #&gt;</code>
                                                    <span class="badge bg-warning text-dark ms-2 extra-small">No
                                                        configurada</span>
                                                </td>
                                                <td class="text-white fst-italic">Sin valor</td>
                                                <td class="text-end">
                                                    <button class="btn btn-primary-custom btn-sm border-0"
                                                        data-bs-toggle="modal" data-bs-target="#addVariableModal"
                                                        onclick="document.getElementById('newVarName').value='<?php echo e($unconfiguredVar); ?>'">
                                                        <i class="bi bi-plus-circle"></i> Configurar
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        <?php $__empty_1 = true; $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><code class="text-primary-light">&lt;# <?php echo e($field->name); ?> #&gt;</code></td>
                                                <td><?php echo e(Str::limit($field->value, 40)); ?></td>
                                                <td class="text-end">
                                                    <button class="btn btn-outline-custom btn-sm border-0"
                                                        data-bs-toggle="modal" data-bs-target="#editModal<?php echo e($field->id); ?>">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <?php if($unconfiguredVariables->isEmpty()): ?>
                                                <tr>
                                                    <td colspan="3" class="text-center py-4 text-white">No hay variables
                                                        configuradas ni detectadas en la plantilla.</td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Tab -->
                <div class="tab-pane fade" id="contentTemplate" role="tabpanel">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-dark-2 py-3 d-flex justify-content-between align-items-center">
                            <span class="text-white fw-bold">Vista Previa del Contenido</span>
                            <span class="badge bg-primary"><?php echo e(ucfirst($template->category)); ?></span>
                        </div>
                        <div class="card-body">
                            <div class="bg-dark-3 p-4 rounded-3 text-white border border-secondary"
                                style="min-height: 400px; white-space: pre-wrap;"><?php echo e($template->content); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark-2 py-3">
                    <span class="text-white fw-bold">Generar Documento</span>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('documents.generate', $template)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <!--
                            <div class="mb-4">

                                <label class="form-label">Contexto (Opcional)</label>
                                <select name="employee_id" class="form-select">
                                    <option value="">Ninguno (Solo variables globales)</option>
                                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($employee->id); ?>"><?php echo e($employee->user->name); ?> (<?php echo e($employee->id_number); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <small class=" text-white d-block mt-2">
                                    Si seleccionas un empleado, podrás usar tags como <code>&lt;# salary #&gt;</code>, <code>&lt;# contract_type #&gt;</code>, etc.
                                </small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Formato de Salida</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="format" id="formatPreview" value="preview" checked>
                                        <label class="form-check-label text-white" for="formatPreview">Vista Previa</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="format" id="formatPdf" value="pdf">
                                        <label class="form-check-label text-white" for="formatPdf">Descargar PDF</label>
                                    </div>
                                </div>
                            </div>
        -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary-custom py-2">
                                <i class="bi bi-play-fill me-2"></i>Generar Documento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!--
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark-2 py-3">
                        <span class="text-white fw-bold">Ayuda de Variables</span>
                    </div>
                    <div class="card-body">
                        <h6 class="text-white small fw-bold mb-2">Variables de Plantilla</h6>
                        <p class="text-white  extra-small mb-3">Configuradas en la pestaña de variables.</p>

                        <h6 class="text-white  small fw-bold mb-2">Variables de Sistema</h6>
                        <ul class="list-unstyled extra-small mb-0">
                            <li><code>&lt;# empresa_nombre #&gt;</code></li>
                            <li><code>&lt;# empresa_rnc #&gt;</code></li>
                            <li><code>&lt;# empresa_direccion #&gt;</code></li>
                        </ul>
                    </div>
                </div>
                -->
        </div>
    </div>

    <style>
        .extra-small {
            font-size: 0.75rem;
        }
    </style>

    <!-- Modals for Variables -->
    <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="editModal<?php echo e($field->id); ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="<?php echo e(route('company-fields.update', $field)); ?>" method="POST">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <div class="modal-header">
                            <h5 class="modal-title text-white">Editar Variable</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="name" class="form-control" value="<?php echo e($field->name); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Valor</label>
                                <textarea name="value" class="form-control" rows="3"><?php echo e($field->value); ?></textarea>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="is_bold" class="form-check-input" id="isBoldCheck<?php echo e($field->id); ?>"
                                    <?php echo e($field->is_bold ? 'checked' : ''); ?>>
                                <label class="form-check-label text-white small" for="isBoldCheck<?php echo e($field->id); ?>">Mostrar en
                                    Negrita</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary-custom">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="modal fade" id="addVariableModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="<?php echo e(route('company-fields.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title text-white">Nueva Variable Global</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="document_template_id" value="<?php echo e($template->id); ?>">
                        <div class="mb-3">
                            <label class="form-label">Nombre de la Variable</label>
                            <input type="text" name="name" id="newVarName" class="form-control"
                                placeholder="Ej: Representante Legal" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Valor</label>
                            <textarea name="value" class="form-control" rows="3" placeholder="Ej: Juan Pérez"></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_bold" class="form-check-input" id="isBoldCheckNew">
                            <label class="form-check-label text-white small" for="isBoldCheckNew">Mostrar en Negrita</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">Guardar Variable</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/documents/show.blade.php ENDPATH**/ ?>