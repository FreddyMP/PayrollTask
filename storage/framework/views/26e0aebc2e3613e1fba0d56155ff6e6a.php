<?php $__env->startSection('title', 'Empleados'); ?>
<?php $__env->startSection('page-title', 'Empleados'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Botón de Filtros y Nuevo Empleado -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-outline-custom" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse"
            aria-expanded="false" aria-controls="filterCollapse">
            <i class="bi bi-funnel me-2"></i>Filtros
            <?php if(request()->hasAny(['search', 'department', 'role', 'contract_type', 'status'])): ?>
                <span
                    class="badge bg-primary ms-1"><?php echo e(collect(request()->only(['search', 'department', 'role', 'contract_type', 'status']))->filter()->count()); ?></span>
            <?php endif; ?>
        </button>
        <a href="<?php echo e(route('employees.create')); ?>" class="btn btn-primary-custom">
            <i class="bi bi-person-plus me-1"></i> Nuevo Empleado
        </a>
    </div>

    <!-- Área de Filtros Colapsable -->
    <div class="collapse <?php echo e(request()->hasAny(['search', 'department', 'role', 'contract_type', 'status']) ? 'show' : ''); ?>"
        id="filterCollapse">
        <div class="card mb-4" style="background-color: #1e293b; border: 1px solid #334155;">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('employees.index')); ?>" id="filterForm">
                    <div class="row g-3">
                        <!-- Búsqueda por nombre/email -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label" style="font-size: 0.875rem; color: #94a3b8;">
                                <i class="bi bi-search me-1"></i>Buscar
                            </label>
                            <input type="text" class="form-control" name="search" placeholder="Nombre o email..."
                                value="<?php echo e(request('search')); ?>"
                                style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                        </div>

                        <!-- Filtro por Departamento -->
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label" style="font-size: 0.875rem; color: #94a3b8;">
                                <i class="bi bi-building me-1"></i>Departamento
                            </label>
                            <select class="form-select" name="department"
                                style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                                <option value="">Todos los departamentos</option>
                                <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($dept->id); ?>" <?php echo e(request('department') == $dept->id ? 'selected' : ''); ?>>
                                        <?php echo e($dept->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Filtro por Rol -->
                        <div class="col-md-6 col-lg-2">
                            <label class="form-label" style="font-size: 0.875rem; color: #94a3b8;">
                                <i class="bi bi-person-badge me-1"></i>Rol
                            </label>
                            <select class="form-select" name="role"
                                style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                                <option value="">Todos los roles</option>
                                <option value="usuario" <?php echo e(request('role') == 'usuario' ? 'selected' : ''); ?>>Usuario</option>
                                <option value="supervisor" <?php echo e(request('role') == 'supervisor' ? 'selected' : ''); ?>>Supervisor
                                </option>
                                <option value="admin" <?php echo e(request('role') == 'admin' ? 'selected' : ''); ?>>Admin</option>
                                <option value="super" <?php echo e(request('role') == 'super' ? 'selected' : ''); ?>>Super</option>
                            </select>
                        </div>

                        <!-- Filtro por Tipo de Contrato -->
                        <div class="col-md-6 col-lg-2">
                            <label class="form-label" style="font-size: 0.875rem; color: #94a3b8;">
                                <i class="bi bi-file-text me-1"></i>Contrato
                            </label>
                            <select class="form-select" name="contract_type"
                                style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                                <option value="">Todos los tipos</option>
                                <option value="full_time" <?php echo e(request('contract_type') == 'full_time' ? 'selected' : ''); ?>>
                                    Tiempo Completo</option>
                                <option value="part_time" <?php echo e(request('contract_type') == 'part_time' ? 'selected' : ''); ?>>
                                    Medio Tiempo</option>
                                <option value="contractor" <?php echo e(request('contract_type') == 'contractor' ? 'selected' : ''); ?>>
                                    Contratista</option>
                            </select>
                        </div>

                        <!-- Filtro por Estado -->
                        <div class="col-md-6 col-lg-2">
                            <label class="form-label" style="font-size: 0.875rem; color: #94a3b8;">
                                <i class="bi bi-toggle-on me-1"></i>Estado
                            </label>
                            <select class="form-select" name="status"
                                style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                                <option value="">Todos los estados</option>
                                <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Activo</option>
                                <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Inactivo
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-search me-1"></i>Aplicar Filtros
                        </button>
                        <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-outline-custom">
                            <i class="bi bi-x-circle me-1"></i>Limpiar
                        </a>
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
                            <th>Empleado</th>
                            <th>Rol</th>
                            <th>Cargo</th>
                            <th>Salario</th>
                            <th>Contrato</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div
                                            style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #6366f1, #a855f7); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: white; font-weight: 700;">
                                            <?php echo e(strtoupper(substr($emp->user->name ?? '', 0, 2))); ?>

                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?php echo e($emp->user->name ?? '—'); ?></div>
                                            <div style="font-size: 0.75rem; color: #64748b;"><?php echo e($emp->user->email ?? ''); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span
                                        class="badge-status badge-<?php echo e($emp->user->role ?? ''); ?>"><?php echo e(ucfirst($emp->user->role ?? '')); ?></span>
                                </td>
                                <td><?php echo e($emp->position?->title ?? $emp->user->position ?? '—'); ?></td>
                                <td style="font-weight: 600;">RD$ <?php echo e(number_format($emp->salary, 2)); ?></td>
                                <td>
                                    <?php
                                        $contractLabels = ['full_time' => 'Tiempo Completo', 'part_time' => 'Medio Tiempo', 'contractor' => 'Contratista'];
                                    ?>
                                    <?php echo e($contractLabels[$emp->contract_type] ?? $emp->contract_type); ?>

                                </td>
                                <td><span
                                        class="badge-status badge-<?php echo e($emp->user->status ?? ''); ?>"><?php echo e(ucfirst($emp->user->status ?? '')); ?></span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo e(route('employees.show', $emp)); ?>" class="btn btn-outline-custom btn-sm"
                                            title="Ver"><i class="bi bi-eye"></i></a>
                                        <a href="<?php echo e(route('employees.edit', $emp)); ?>" class="btn btn-outline-custom btn-sm"
                                            title="Editar"><i class="bi bi-pencil"></i></a>
                                        <form method="POST" action="<?php echo e(route('employees.destroy', $emp)); ?>" class="d-inline"
                                            onsubmit="return confirm('¿Eliminar este empleado?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-outline-custom btn-sm" style="color: #f87171;"
                                                title="Eliminar"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center text-dark py-4">No hay empleados registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <?php echo e($employees->links()); ?>

    </div>

    <style>
        .form-select option {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        .collapse.show {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/employees/index.blade.php ENDPATH**/ ?>