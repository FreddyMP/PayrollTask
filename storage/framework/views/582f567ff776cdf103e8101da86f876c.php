<?php $__env->startSection('title', 'Posiciones'); ?>
<?php $__env->startSection('page-title', 'Posiciones'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('departments.partials.tabs', ['activeTab' => 'positions'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="text-white mb-1">Posiciones</h5>
            <p class="small mb-0">Gestión de posiciones y puestos de trabajo</p>
        </div>
        <a href="<?php echo e(route('positions.create')); ?>" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-2"></i>Nueva Posición
        </a>
    </div>
</div>

<!-- Card de Filtros Colapsable -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark-2 py-2 border-0">
                <button class="btn btn-link text-white text-decoration-none w-100 text-start d-flex justify-content-between align-items-center p-0"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#filtersCard"
                        aria-expanded="false"
                        aria-controls="filtersCard">
                    <span>
                        <i class="bi bi-funnel me-2"></i>
                        <span class="fw-bold">Filtros</span>
                    </span>
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
            <div class="collapse" id="filtersCard">
                <div class="card-body">
                    <form action="<?php echo e(route('positions.index')); ?>" method="GET" id="filterForm">
                        <div class="row g-3">
                            <!-- Búsqueda por título -->
                            <div class="col-md-4">
                                <label for="search" class="form-label">Búsqueda por Título</label>
                                <input type="text"
                                       class="form-control"
                                       id="search"
                                       name="search"
                                       placeholder="Buscar por título..."
                                       value="<?php echo e(request('search')); ?>">
                            </div>

                            <!-- Filtro por Departamento -->
                            <div class="col-md-4">
                                <label for="department_id" class="form-label">Departamento</label>
                                <select class="form-select" id="department_id" name="department_id">
                                    <option value="">Todos los departamentos</option>
                                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($department->id); ?>" <?php echo e(request('department_id') == $department->id ? 'selected' : ''); ?>>
                                            <?php echo e($department->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <!-- Filtro por Estado -->
                            <div class="col-md-4">
                                <label for="status" class="form-label">Estado</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Todos los estados</option>
                                    <option value="1" <?php echo e(request('status') === '1' ? 'selected' : ''); ?>>Activo</option>
                                    <option value="0" <?php echo e(request('status') === '0' ? 'selected' : ''); ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="row mt-3">
                            <div class="col-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="bi bi-search me-2"></i>Buscar
                                </button>
                                <a href="<?php echo e(route('positions.index')); ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark-2 py-3 border-0">
                <i class="bi bi-person-badge me-2"></i>
                <span class="text-white fw-bold">Listado de Posiciones</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-dark-2">
                            <tr>
                                <th>Título</th>
                                <th>Departamento</th>
                                <th>Código</th>
                                <th>Salario Base</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <span class="d-block fw-semibold"><?php echo e($position->title); ?></span>
                                    <?php if($position->description): ?>
                                    <small class="text-muted"><?php echo e(Str::limit($position->description, 50)); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($position->department ? $position->department->name : 'N/A'); ?></td>
                                <td><?php echo e($position->code ?? 'N/A'); ?></td>
                                <td><?php echo e($position->base_salary ? '$' . number_format($position->base_salary, 2) : 'N/A'); ?></td>
                                <td>
                                    <span class="badge <?php echo e($position->is_active ? 'bg-success' : 'bg-danger'); ?>">
                                        <?php echo e($position->is_active ? 'Activo' : 'Inactivo'); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?php echo e(route('positions.edit', $position)); ?>" class="btn btn-sm btn-outline-custom">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?php echo e(route('positions.destroy', $position)); ?>" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta posición?');">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    No hay posiciones registradas.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/positions/index.blade.php ENDPATH**/ ?>