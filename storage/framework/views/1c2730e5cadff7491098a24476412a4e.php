<?php $__env->startSection('title', 'Proyectos'); ?>
<?php $__env->startSection('page-title', 'Gestión de Proyectos'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 text-white">Proyectos</h4>
    <?php if(auth()->user()->isSupervisor() || auth()->user()->isAdmin() || auth()->user()->isSuper()): ?>
    <a href="<?php echo e(route('projects.create')); ?>" class="btn btn-primary-custom">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Proyecto
    </a>
    <?php endif; ?>
</div>


<div class="card mb-4" style="border: 1px solid rgba(255,255,255,0.06); background: var(--dark-2);">
    <div class="card-header" style="background: transparent; border-bottom: 1px solid rgba(255,255,255,0.06);">
        <button class="btn btn-link text-white text-decoration-none w-100 text-start d-flex justify-content-between align-items-center p-0" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="<?php echo e(request()->hasAny(['search', 'status', 'start_date', 'end_date', 'team_member']) ? 'true' : 'false'); ?>" aria-controls="filtersCollapse">
            <span><i class="bi bi-funnel me-2"></i>Filtros <?php if(request()->hasAny(['search', 'status', 'start_date', 'end_date', 'team_member'])): ?><span class="badge bg-primary ms-2">Activos</span><?php endif; ?></span>
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse <?php echo e(request()->hasAny(['search', 'status', 'start_date', 'end_date', 'team_member']) ? 'show' : ''); ?>" id="filtersCollapse">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('projects.index')); ?>">
                <div class="row g-3">
                    
                    <div class="col-md-3">
                        <label for="search" class="form-label text-white small">Buscar por nombre</label>
                        <input type="text" class="form-control" id="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Nombre del proyecto...">
                    </div>

                    
                    <div class="col-md-3">
                        <label for="status" class="form-label text-white small">Estado</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Todos los estados</option>
                            <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Activo</option>
                            <option value="on_hold" <?php echo e(request('status') == 'on_hold' ? 'selected' : ''); ?>>En espera</option>
                            <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>Completado</option>
                            <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>Cancelado</option>
                        </select>
                    </div>

                    
                    <div class="col-md-3">
                        <label for="start_date" class="form-label text-white small">Fecha de inicio desde</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo e(request('start_date')); ?>">
                    </div>

                    
                    <div class="col-md-3">
                        <label for="end_date" class="form-label text-white small">Fecha de fin hasta</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo e(request('end_date')); ?>">
                    </div>

                    
                    <div class="col-md-6">
                        <label for="team_member" class="form-label text-white small">Miembro del equipo</label>
                        <select class="form-select" id="team_member" name="team_member">
                            <option value="">Todos los miembros</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>" <?php echo e(request('team_member') == $user->id ? 'selected' : ''); ?>>
                                    <?php echo e($user->name); ?> (<?php echo e(ucfirst($user->role)); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-6 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-search me-1"></i> Aplicar Filtros
                        </button>
                        <a href="<?php echo e(route('projects.index')); ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row g-4">
    <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="col-xl-4 col-md-6">
        <div class="card h-100 stat-card" style="border: 1px solid rgba(255,255,255,0.06); background: var(--dark-2); transition: all 0.3s ease;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge-status badge-<?php echo e($project->status); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $project->status))); ?></span>
                    <div class="d-flex align-items-center gap-2">
                        <?php if(auth()->user()->isSupervisor() || auth()->user()->isAdmin() || auth()->user()->isSuper()): ?>
                        <a href="<?php echo e(route('projects.edit', $project)); ?>" class="btn btn text-secondary p-0" title="Editar">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <?php endif; ?>
                        <div class="dropdown">
                            <button class="btn btn-link text-white p-0" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="<?php echo e(route('projects.show', $project)); ?>"><i class="bi bi-eye me-2"></i>Ver Detalles</a></li>
                            <?php if(auth()->user()->isSupervisor() || auth()->user()->isAdmin() || auth()->user()->isSuper()): ?>
                            <li><a class="dropdown-item" href="<?php echo e(route('projects.edit', $project)); ?>"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="<?php echo e(route('projects.destroy', $project)); ?>" method="POST" onsubmit="return confirm('¿Eliminar proyecto y sus tareas relacionadas?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Eliminar</button>
                                </form>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

                <h5 class="text-white mb-2"><?php echo e($project->name); ?></h5>
                <p class="text-secondary small mb-4" style="height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                    <?php echo e($project->description ?? 'Sin descripción'); ?>

                </p>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1 small">
                        <span class="text-white">Progreso</span>
                        <span class="text-white fw-bold"><?php echo e($project->progress); ?>%</span>
                    </div>
                    <div class="progress" style="height: 6px; background: rgba(255,255,255,0.05);">
                        <div class="progress-bar" style="width: <?php echo e($project->progress); ?>%; background: var(--gradient-1);"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="avatar-group d-flex">
                        <?php $__currentLoopData = $project->team->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="avatar-sm" title="<?php echo e($member->name); ?>" style="width: 28px; height: 28px; border-radius: 50%; background: var(--gradient-2); border: 2px solid var(--dark-2); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; margin-right: -10px;">
                            <?php echo e(strtoupper(substr($member->name, 0, 1))); ?>

                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($project->team->count() > 4): ?>
                        <div class="avatar-sm" style="width: 28px; height: 28px; border-radius: 50%; background: var(--dark-3); border: 2px solid var(--dark-2); color: #94a3b8; display: flex; align-items: center; justify-content: center; font-size: 0.65rem; margin-right: -10px;">
                            +<?php echo e($project->team->count() - 4); ?>

                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="small text-secondary">
                        <i class="bi bi-calendar-event me-1"></i> <?php echo e($project->end_date ? $project->end_date->format('d/m/Y') : 'Sin fecha'); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-12 text-center py-5">
        <div class="mb-3"><i class="bi bi-folder-x display-4 text-white"></i></div>
        <h5 class="text-white">No hay proyectos registrados</h5>
        <?php if(auth()->user()->isSupervisor()|| auth()->user()->isAdmin() || auth()->user()->isSuper()): ?>
        <a href="<?php echo e(route('projects.create')); ?>" class="btn btn-primary-custom mt-3">Crear primer proyecto</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<div class="mt-4">
    <?php echo e($projects->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/projects/index.blade.php ENDPATH**/ ?>