<?php $__currentLoopData = $nodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
    $children = $allDepartments->where('parent_department_id', $department->id)->sortBy('name')->values();
?>
<li>
    <div class="org-node <?php echo e($children->isNotEmpty() ? 'org-node--has-children' : ''); ?>">
        <div class="org-node-header">
            <div class="org-node-icon">
                <i class="bi bi-building"></i>
            </div>
            <div class="org-node-info">
                <h6 class="org-node-title"><?php echo e($department->name); ?></h6>
                <?php if($department->description): ?>
                    <p class="org-node-desc"><?php echo e(Str::limit($department->description, 60)); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="org-node-stats">
            <span style="cursor: pointer; text-decoration: underline;" data-bs-toggle="collapse" data-bs-target="#employees-<?php echo e($department->id); ?>" aria-expanded="false" aria-controls="employees-<?php echo e($department->id); ?>">
                <i class="bi bi-people me-1"></i><?php echo e($department->employees_count); ?> empleado(s)
            </span>
            <span><i class="bi bi-briefcase me-1"></i><?php echo e($department->positions_count); ?> posición(es)</span>
        </div>
        <?php if($department->employees->isNotEmpty()): ?>
            <div class="collapse" id="employees-<?php echo e($department->id); ?>">
                <div class="org-node-employees mt-2">
                    <?php $__currentLoopData = $department->employees->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="org-employee">
                            <span class="org-employee-avatar"><?php echo e(strtoupper(substr($employee->user->name ?? '?', 0, 2))); ?></span>
                            <div>
                                <span class="org-employee-name"><?php echo e($employee->user->name ?? '—'); ?></span>
                                <small class="org-employee-role"><?php echo e($employee->position?->title ?? $employee->user->position ?? 'Sin cargo'); ?></small>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($department->employees_count > 5): ?>
                        <small class="org-employee-more">+<?php echo e($department->employees_count - 5); ?> más</small>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="org-node-actions">
            <a href="<?php echo e(route('departments.edit', $department)); ?>" class="btn btn-sm btn-outline-custom">
                <i class="bi bi-pencil"></i> Editar
            </a>
        </div>
    </div>
    <?php if($children->isNotEmpty()): ?>
        <ul>
            <?php echo $__env->make('org-chart._node', ['nodes' => $children, 'allDepartments' => $allDepartments], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </ul>
    <?php endif; ?>
</li>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/org-chart/_node.blade.php ENDPATH**/ ?>