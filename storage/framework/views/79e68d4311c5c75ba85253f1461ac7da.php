<?php $__env->startSection('title', 'Registro de Accesos'); ?>
<?php $__env->startSection('page-title', 'Registro de Accesos'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between text-white align-items-center">
            <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Filtros de Búsqueda</h6>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
                data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('access-logs.index')); ?>" id="filterForm">
                    <div class="row g-3">
                        <!-- Búsqueda por Usuario -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-person me-1"></i>Buscar Usuario</label>
                            <input type="text" class="form-control form-control-sm" name="search_user"
                                value="<?php echo e(request('search_user')); ?>" placeholder="Nombre o email del usuario...">
                        </div>

                        <!-- Filtro por Acción/Evento -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-activity me-1"></i>Acción/Evento</label>
                            <select class="form-select form-select-sm" name="action">
                                <option value="">Todas las acciones</option>
                                <option value="login" <?php echo e(request('action') == 'login' ? 'selected' : ''); ?>>Login</option>
                                <option value="logout" <?php echo e(request('action') == 'logout' ? 'selected' : ''); ?>>Logout</option>
                                <option value="create" <?php echo e(request('action') == 'create' ? 'selected' : ''); ?>>Crear</option>
                                <option value="update" <?php echo e(request('action') == 'update' ? 'selected' : ''); ?>>Actualizar
                                </option>
                                <option value="delete" <?php echo e(request('action') == 'delete' ? 'selected' : ''); ?>>Eliminar</option>
                            </select>
                        </div>

                        <!-- Filtro por Usuario (select) -->
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-people me-1"></i>Usuario</label>
                            <select class="form-select form-select-sm" name="user_id">
                                <option value="">Todos</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>>
                                        <?php echo e($user->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Rango de Fechas -->
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-calendar-range me-1"></i>Desde</label>
                            <input type="date" class="form-control form-control-sm" name="date_from"
                                value="<?php echo e(request('date_from')); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="bi bi-calendar-check me-1"></i>Hasta</label>
                            <input type="date" class="form-control form-control-sm" name="date_to"
                                value="<?php echo e(request('date_to')); ?>">
                        </div>

                        <!-- Filtro por IP -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-hdd-network me-1"></i>Dirección IP</label>
                            <input type="text" class="form-control form-control-sm" name="ip_address"
                                value="<?php echo e(request('ip_address')); ?>" placeholder="Ej: 192.168.1.1">
                        </div>

                        <!-- Botones -->
                        <div class="col-md-6 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary-custom btn-sm flex-grow-1">
                                <i class="bi bi-search me-1"></i> Filtrar
                            </button>
                            <a href="<?php echo e(route('access-logs.index')); ?>" class="btn btn-outline-secondary btn-sm flex-grow-1">
                                <i class="bi bi-x-circle me-1"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Inicio de Sesión</th>
                            <th>Estado</th>
                            <th>Cierre de Sesión</th>
                            <th>Duración</th>
                            <th>Dispositivo / IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="fw-semibold"><?php echo e($log->user->name ?? '—'); ?></td>
                                <td><span
                                        class="badge-status badge-<?php echo e($log->user->role ?? ''); ?>"><?php echo e(ucfirst($log->user->role ?? '')); ?></span>
                                </td>
                                <td><?php echo e($log->login_at->format('d/m/Y H:i:s')); ?></td>
                                <td>
                                    <?php
                                        $status = $log->attendance_status;
                                        $badgeClass = $status === 'Puntual' ? 'success' : ($status === 'Tarde' ? 'warning' : 'danger');
                                    ?>
                                    <span
                                        class="badge bg-<?php echo e($badgeClass); ?> bg-opacity-10 text-<?php echo e($badgeClass); ?> border border-<?php echo e($badgeClass); ?> border-opacity-25"
                                        style="font-size: 0.7rem;">
                                        <?php echo e($status); ?>

                                    </span>
                                </td>
                                <td><?php echo $log->logout_at ? $log->logout_at->format('d/m/Y H:i:s') : '<span class="badge-status badge-active">Activo</span>'; ?>

                                </td>
                                <td>
                                    <?php if($log->logout_at): ?>
                                        <?php echo e($log->login_at->diff($log->logout_at)->format('%Hh %Im')); ?>

                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 0.85rem;">
                                    <?php if($log->device_name !== $log->ip_address): ?>
                                        <div class="fw-semibold text-secondary"><i
                                                class="bi bi-laptop me-1 small "></i><?php echo e($log->device_name); ?></div>
                                        <div class="text-white small" style="font-family: monospace; font-size: 0.7rem;">
                                            <?php echo e($log->ip_address); ?></div>
                                    <?php else: ?>
                                        <span style="font-family: monospace; color: #94a3b8;"><?php echo e($log->ip_address ?? '—'); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center text-white py-4">No hay registros</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3"><?php echo e($logs->links()); ?></div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/access-logs/index.blade.php ENDPATH**/ ?>