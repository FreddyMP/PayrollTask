<?php $__env->startSection('title', 'Nueva Tarea'); ?>
<?php $__env->startSection('page-title', 'Nueva Tarea'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-plus-circle me-2"></i>Crear Tarea</div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('tasks.store')); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" name="title" value="<?php echo e(old('title')); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adjuntar Imágenes o Videos</label>
                        <input type="file" class="form-control" name="attachments[]" accept="image/*,video/*" multiple>
                        <div class="form-text text-white small">Puedes seleccionar varios archivos a la vez (Máx 30MB cada uno).</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="description" rows="4"><?php echo e(old('description')); ?></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Proyecto <span class="text-danger">*</span></label>
                            <select class="form-select" name="project_id" required>
                                <option value="">Seleccione un proyecto</option>
                                <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($project->id); ?>" <?php echo e(old('project_id') == $project->id ? 'selected' : ''); ?>><?php echo e($project->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Prioridad</label>
                            <select class="form-select" name="priority">
                                <option value="low">Baja</option>
                                <option value="medium" selected>Media</option>
                                <option value="high">Alta</option>
                                <option value="urgent">Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Asignar a</label>
                            <select class="form-select" name="assigned_to">
                                <option value="">Sin asignar</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha límite</label>
                            <input type="date" class="form-control" name="due_date" value="<?php echo e(old('due_date')); ?>">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> Crear Tarea</button>
                        <a href="<?php echo e(route('tasks.index')); ?>" class="btn btn-outline-custom">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/tasks/create.blade.php ENDPATH**/ ?>