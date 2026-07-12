<?php $__env->startSection('title', 'Gestionar Evaluación'); ?>
<?php $__env->startSection('page-title', 'Evaluación: ' . $evaluation->title); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark-2 py-3 d-flex justify-content-between align-items-center">
                <span class="text-white fw-bold">Preguntas</span>
                <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                    <i class="bi bi-plus-circle me-1"></i>Añadir Pregunta
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pregunta</th>
                                <th>Tipo</th>
                                <th>Requerida</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $evaluation->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($question->order); ?></td>
                                <td><?php echo e($question->question_text); ?></td>
                                <td>
                                    <?php if($question->type === 'scale'): ?> Escala 1-10
                                    <?php elseif($question->type === 'boolean'): ?> Sí o No
                                    <?php elseif($question->type === 'text'): ?> Texto Corto
                                    <?php else: ?> Texto Largo <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($question->is_required): ?> <span class="badge bg-danger">Sí</span>
                                    <?php else: ?> <span class="badge bg-secondary">No</span> <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <form action="<?php echo e(route('evaluations.questions.destroy', [$evaluation, $question])); ?>" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-outline-danger btn-sm border-0" onclick="return confirm('¿Eliminar pregunta?')">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-white">No hay preguntas configuradas.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark-2 py-3 d-flex justify-content-between align-items-center">
                <span class="text-white fw-bold">Asignar a Empleados</span>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('evaluations.assignments.store', $evaluation)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Seleccionar Empleados</label>
                        <select name="employee_ids[]" class="form-select" multiple size="6" required>
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!$evaluation->assignments->contains('employee_id', $employee->id)): ?>
                                    <option value="<?php echo e($employee->id); ?>"><?php echo e($employee->user->name); ?> (<?php echo e($employee->department); ?>)</option>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="text-white d-block mt-2">Mantén presionado Ctrl/Cmd para seleccionar múltiples.</small>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100">
                        <i class="bi bi-send-fill me-2"></i>Asignar y Notificar
                    </button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark-2 py-3">
                <span class="text-white fw-bold">Empleados Asignados</span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush rounded-bottom">
                    <?php $__empty_1 = true; $__currentLoopData = $evaluation->assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center border-secondary">
                        <div>
                            <span class="text-white"><?php echo e($assignment->employee->user->name); ?></span>
                            <br>
                            <?php if($assignment->is_completed): ?>
                                <span class="badge bg-success" style="font-size:0.65rem">Completado</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark" style="font-size:0.65rem">Pendiente</span>
                            <?php endif; ?>
                        </div>
                        <form action="<?php echo e(route('evaluations.assignments.destroy', [$evaluation, $assignment])); ?>" method="POST">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm border-0 py-0" onclick="return confirm('¿Quitar asignación?')">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </form>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li class="list-group-item bg-transparent text-center text-white py-3">Ningún empleado asignado.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo e(route('evaluations.questions.store', $evaluation)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title text-white">Añadir Pregunta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pregunta</label>
                        <textarea name="question_text" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Respuesta</label>
                        <select name="type" class="form-select" required>
                            <option value="scale">Escala del 1 al 10</option>
                            <option value="boolean">Sí o No</option>
                            <option value="text">Texto Corto (Una línea)</option>
                            <option value="textarea">Texto Largo (Párrafo)</option>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_required" class="form-check-input" id="isRequiredCheck" checked>
                        <label class="form-check-label text-white small" for="isRequiredCheck">
                            Obligatoria
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Añadir Pregunta</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/evaluations/show.blade.php ENDPATH**/ ?>