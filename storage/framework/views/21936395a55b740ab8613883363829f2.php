<?php $__env->startSection('title', 'Evaluaciones de Personal'); ?>
<?php $__env->startSection('page-title', 'Evaluaciones de Personal'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="text-white mb-1">Gestión de Evaluaciones</h5>
            <p class="small ">Crea formularios de evaluación y asígnalos a los empleados.</p>
        </div>
        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle me-2"></i>Nueva Evaluación
        </button>
    </div>
</div>

<!-- Filters Card -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header" style="background: rgba(255, 255, 255, 0.05); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
        <button class="btn btn-link text-white text-decoration-none w-100 text-start d-flex justify-content-between align-items-center p-0" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="false" aria-controls="filtersCollapse">
            <span><i class="bi bi-funnel me-2"></i>Filtros</span>
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse" id="filtersCollapse">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('evaluations.index')); ?>" id="filterForm">
                <div class="row g-3">
                    <!-- Search by title -->
                    <div class="col-md-4">
                        <label class="form-label small text-white">Buscar por título</label>
                        <input type="text" name="search" class="form-control" placeholder="Título de evaluación" value="<?php echo e(request('search')); ?>">
                    </div>

                    <!-- Filter by status -->
                    <div class="col-md-4">
                        <label class="form-label small text-white">Estado</label>
                        <select name="status" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Activa</option>
                            <option value="draft" <?php echo e(request('status') === 'draft' ? 'selected' : ''); ?>>Borrador</option>
                            <option value="closed" <?php echo e(request('status') === 'closed' ? 'selected' : ''); ?>>Cerrada</option>
                        </select>
                    </div>

                    <!-- Date range filter -->
                    <div class="col-md-4">
                        <label class="form-label small text-white">Fecha de creación</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="date" name="date_from" class="form-control" placeholder="Desde" value="<?php echo e(request('date_from')); ?>">
                            </div>
                            <div class="col-6">
                                <input type="date" name="date_to" class="form-control" placeholder="Hasta" value="<?php echo e(request('date_to')); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter buttons -->
                <div class="row mt-3">
                    <div class="col-12 d-flex gap-2 justify-content-end">
                        <a href="<?php echo e(route('evaluations.index')); ?>" class="btn btn-outline-custom btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Limpiar
                        </a>
                        <button type="submit" class="btn btn-primary-custom btn-sm">
                            <i class="bi bi-search me-1"></i>Aplicar Filtros
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <?php $__empty_1 = true; $__currentLoopData = $evaluations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $evaluation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 class="text-white fw-bold mb-0"><?php echo e($evaluation->title); ?></h6>
                    <span class="badge <?php echo e($evaluation->status === 'active' ? 'bg-success' : ($evaluation->status === 'draft' ? 'bg-secondary' : 'bg-danger')); ?>">
                        <?php echo e(ucfirst($evaluation->status)); ?>

                    </span>
                </div>
                <p class="small text-white mb-3"><?php echo e(Str::limit($evaluation->description, 100)); ?></p>
                <div class="d-flex gap-3 mb-4">
                    <div class="text-center">
                        <span class="d-block h5 text-white mb-0"><?php echo e($evaluation->questions_count); ?></span>
                        <span class="small text-white" style="font-size:0.75rem;">Preguntas</span>
                    </div>
                    <div class="text-center">
                        <span class="d-block h5 text-white mb-0"><?php echo e($evaluation->assignments_count); ?></span>
                        <span class="small text-white" style="font-size:0.75rem;">Asignados</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('evaluations.show', $evaluation)); ?>" class="btn btn-primary-custom btn-sm flex-grow-1">
                        <i class="bi bi-gear-fill me-1"></i> Gestionar
                    </a>
                    <a href="<?php echo e(route('evaluations.results', $evaluation)); ?>" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-bar-chart-fill"></i>
                    </a>
                    <form action="<?php echo e(route('evaluations.destroy', $evaluation)); ?>" method="POST">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Eliminar esta evaluación?')">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-12 text-center py-5">
        <i class="bi bi-clipboard2-check display-1 opacity-25"></i>
        <p class="mt-3 text-white">No hay evaluaciones creadas.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo e(route('evaluations.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title text-white">Nueva Evaluación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="allow_multiple_responses" class="form-check-input" id="multipleResponses">
                        <label class="form-check-label text-white small" for="multipleResponses">
                            Permitir responder múltiples veces
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Crear Evaluación</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/evaluations/index.blade.php ENDPATH**/ ?>