<?php $__env->startSection('title', 'Editar Posición'); ?>
<?php $__env->startSection('page-title', 'Editar Posición'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('departments.partials.tabs', ['activeTab' => 'positions'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark-2 py-3 border-0">
                <i class="bi bi-pencil me-2"></i>
                <span class="text-white fw-bold">Editar Posición</span>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('positions.update', $position)); ?>">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <div class="mb-3">
                        <label class="form-label">Título del Puesto</label>
                        <input type="text" class="form-control" name="title" value="<?php echo e(old('title', $position->title)); ?>" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Departamento</label>
                        <select class="form-select" name="department_id" required>
                            <option value="">Seleccionar...</option>
                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($department->id); ?>" <?php echo e($position->department_id == $department->id ? 'selected' : ''); ?>><?php echo e($department->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Código (Opcional)</label>
                        <input type="text" class="form-control" name="code" value="<?php echo e(old('code', $position->code)); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Salario Base (Opcional)</label>
                        <input type="number" step="0.01" class="form-control" name="base_salary" value="<?php echo e(old('base_salary', $position->base_salary)); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="description" rows="3"><?php echo e(old('description', $position->description)); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" <?php echo e($position->is_active ? 'checked' : ''); ?>>
                            <label class="form-check-label text-white" for="isActive">
                                Posición Activa
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-check-lg me-1"></i> Actualizar
                        </button>
                        <a href="<?php echo e(route('positions.index')); ?>" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/positions/edit.blade.php ENDPATH**/ ?>