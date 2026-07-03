<?php $__env->startSection('title', 'Nuevo Departamento'); ?>
<?php $__env->startSection('page-title', 'Nuevo Departamento'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('departments.partials.tabs', ['activeTab' => 'departments'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark-2 py-3 border-0">
                <i class="bi bi-diagram-3 me-2"></i>
                <span class="text-white fw-bold">Crear Departamento</span>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('departments.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Nombre del Departamento</label>
                        <input type="text" class="form-control" name="name" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Departamento Padre (Opcional)</label>
                        <select class="form-select" name="parent_department_id">
                            <option value="">Sin departamento padre (Departamento principal)</option>
                            <?php $__currentLoopData = $parentDepartments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($parent->id); ?>"><?php echo e($parent->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="text-muted">Si selecciona un departamento padre, este será un subdepartamento del mismo.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" checked>
                            <label class="form-check-label text-white" for="isActive">
                                Departamento Activo
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-check-lg me-1"></i> Guardar
                        </button>
                        <a href="<?php echo e(route('departments.index')); ?>" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/departments/create.blade.php ENDPATH**/ ?>