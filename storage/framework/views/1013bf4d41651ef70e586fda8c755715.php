<?php $__env->startSection('title', 'Nuevo Proyecto'); ?>
<?php $__env->startSection('page-title', 'Crear Nuevo Proyecto'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10">
        <div class="card bg-dark-2 border-0 shadow-lg">
            <div class="card-header border-bottom border-light bg-transparent p-4">
                <h5 class="mb-0 text-white">Información del Proyecto</h5>
                <p class="text-white small mb-0">Complete los detalles para iniciar un nuevo proyecto.</p>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo e(route('projects.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Nombre del Proyecto</label>
                            <input type="text" name="name" class="form-control" placeholder="Ej: Rediseño de Sitio Web" required value="<?php echo e(old('name')); ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Descripción</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Breve descripción del objetivo y alcance..."><?php echo e(old('description')); ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Fecha de Inicio</label>
                            <input type="date" name="start_date" class="form-control" value="<?php echo e(old('start_date')); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Fecha de Fin (Estimada)</label>
                            <input type="date" name="end_date" class="form-control" value="<?php echo e(old('end_date')); ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Estado Inicial</label>
                            <select name="status" class="form-select">
                                <option value="active" <?php echo e(old('status') == 'active' ? 'selected' : ''); ?>>Activo</option>
                                <option value="on_hold" <?php echo e(old('status') == 'on_hold' ? 'selected' : ''); ?>>En Pausa</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <hr class="my-4 border-light opacity-10">
                            <h6 class="text-white mb-3">Equipo del Proyecto</h6>
                            <p class="text-white small mb-3">Seleccione los supervisores y usuarios que podrán ver y trabajar en este proyecto.</p>
                            
                            <div class="row g-2" style="max-height: 250px; overflow-y: auto;">
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <div class="form-check p-2 rounded-3" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="team[]" value="<?php echo e($user->id); ?>" id="user-<?php echo e($user->id); ?>">
                                        <label class="form-check-label d-flex align-items-center" for="user-<?php echo e($user->id); ?>">
                                            <div class="avatar-group me-2">
                                                <div class="avatar-xs" style="width: 24px; height: 24px; border-radius: 6px; background: var(--gradient-2); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.6rem;">
                                                    <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-white small fw-bold"><?php echo e($user->name); ?></div>
                                                <div class="text-white" style="font-size: 0.65rem;"><?php echo e(ucfirst($user->role)); ?></div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <div class="col-12 mt-5">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary-custom px-4">Crear Proyecto</button>
                                <a href="<?php echo e(route('projects.index')); ?>" class="btn btn-outline-custom">Cancelar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/projects/create.blade.php ENDPATH**/ ?>