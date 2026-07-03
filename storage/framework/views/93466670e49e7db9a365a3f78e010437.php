<?php
    $activeTab = $activeTab ?? 'departments';
?>
<ul class="nav nav-tabs mb-4 px-3" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
    <li class="nav-item">
        <a class="nav-link <?php echo e($activeTab === 'departments' ? 'active' : ''); ?>"
           href="<?php echo e(route('departments.index')); ?>"
           style="<?php echo e($activeTab === 'departments' ? 'color: white; border-bottom: 2px solid var(--primary);' : 'color: #94a3b8;'); ?> background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
            Departamentos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo e($activeTab === 'positions' ? 'active' : ''); ?>"
           href="<?php echo e(route('positions.index')); ?>"
           style="<?php echo e($activeTab === 'positions' ? 'color: white; border-bottom: 2px solid var(--primary);' : 'color: #94a3b8;'); ?> background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
            Posiciones
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo e($activeTab === 'org-chart' ? 'active' : ''); ?>"
           href="<?php echo e(route('org-chart.index')); ?>"
           style="<?php echo e($activeTab === 'org-chart' ? 'color: white; border-bottom: 2px solid var(--primary);' : 'color: #94a3b8;'); ?> background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
            Organigrama
        </a>
    </li>
</ul>
<?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/departments/partials/tabs.blade.php ENDPATH**/ ?>