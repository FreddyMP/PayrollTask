<?php $__env->startSection('title', 'Organigrama'); ?>
<?php $__env->startSection('page-title', 'Organigrama'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .org-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .org-stat-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 1rem 1.25rem;
        }

        .org-stat-card small {
            color: #94a3b8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .org-stat-card .value {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin-top: 0.25rem;
        }

        .org-chart-wrapper {
            overflow: auto;
            padding: 2rem 1rem;
            background: rgba(0, 0, 0, 0.15);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .org-tree,
        .org-tree ul {
            padding-top: 20px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            list-style: none;
            margin: 0 auto;
            padding-left: 0;
            min-width: max-content;
            min-height: max-content;
        }

        .org-tree ul {
            padding-top: 40px;
        }

        .org-tree li {
            position: relative;
            padding: 20px 12px 0;
            text-align: center;
            list-style: none;
        }

        .org-tree li::before,
        .org-tree li::after {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            width: 50%;
            height: 20px;
            border-top: 2px solid rgba(99, 102, 241, 0.35);
        }

        .org-tree li::after {
            right: auto;
            left: 50%;
            border-left: 2px solid rgba(99, 102, 241, 0.35);
        }

        .org-tree li:only-child::before,
        .org-tree li:only-child::after {
            display: none;
        }

        .org-tree li:first-child::before,
        .org-tree li:last-child::after {
            border: 0 none;
        }

        .org-tree li:last-child::before {
            border-right: 2px solid rgba(99, 102, 241, 0.35);
            border-radius: 0 5px 0 0;
        }

        .org-tree li:first-child::after {
            border-radius: 5px 0 0 0;
        }

        .org-tree ul::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            width: 0;
            height: 20px;
            border-left: 2px solid rgba(99, 102, 241, 0.35);
        }

        .org-tree>li {
            padding-top: 0;
        }

        .org-tree>li::before,
        .org-tree>li::after {
            display: none;
        }

        .org-node {
            display: inline-block;
            min-width: 240px;
            max-width: 280px;
            background: var(--dark-2);
            border: 1px solid rgba(99, 102, 241, 0.25);
            border-radius: 16px;
            padding: 1rem;
            text-align: left;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        }

        .org-node:hover {
            transform: translateY(-2px);
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 12px 32px rgba(99, 102, 241, 0.15);
        }

        .org-node--has-children {
            border-color: rgba(99, 102, 241, 0.45);
        }

        .org-node-header {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            margin-bottom: 0.75rem;
        }

        .org-node-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--gradient-1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }

        .org-node-title {
            color: white;
            font-weight: 700;
            font-size: 0.95rem;
            margin: 0;
        }

        .org-node-desc {
            color: #94a3b8;
            font-size: 0.75rem;
            margin: 0.25rem 0 0;
            line-height: 1.4;
        }

        .org-node-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem 1rem;
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .org-node-employees {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .org-employee {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .org-employee-avatar {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(99, 102, 241, 0.2);
            color: var(--primary-light);
            font-size: 0.65rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .org-employee-name {
            display: block;
            font-size: 0.8rem;
            color: #e2e8f0;
            line-height: 1.2;
        }

        .org-employee-role {
            display: block;
            color: #64748b;
            font-size: 0.7rem;
        }

        .org-employee-more {
            color: #64748b;
            font-size: 0.75rem;
            padding-left: 2.25rem;
        }

        .org-node-actions {
            text-align: center;
        }

        .org-empty {
            text-align: center;
            padding: 4rem 2rem;
            color: #94a3b8;
        }

        .org-empty i {
            font-size: 3rem;
            opacity: 0.4;
            margin-bottom: 1rem;
            display: block;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <!--<?php echo $__env->make('departments.partials.tabs', ['activeTab' => 'org-chart'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>-->

    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="text-white mb-1">Organigrama Organizacional</h5>
                <p class="small mb-0 text-secondary">Jerarquía basada en los departamentos y sus relaciones padre-hijo</p>
            </div>
        </div>
    </div>

    <div class="org-stats">
        <div class="org-stat-card">
            <small>Departamentos</small>
            <div class="value"><?php echo e($stats['departments']); ?></div>
        </div>
        <div class="org-stat-card">
            <small>Empleados</small>
            <div class="value"><?php echo e($stats['employees']); ?></div>
        </div>
        <div class="org-stat-card">
            <small>Posiciones</small>
            <div class="value"><?php echo e($stats['positions']); ?></div>
        </div>
        <div class="org-stat-card">
            <small>Niveles</small>
            <div class="value"><?php echo e($stats['levels']); ?></div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark-2 py-3 border-0 d-flex justify-content-between align-items-center">
            <span class="text-white fw-bold"><i class="bi bi-bezier2 me-2"></i>Estructura Jerárquica</span>
            <small class="text-secondary">Solo departamentos activos</small>
        </div>
        <div class="card-body">
            <?php if($rootDepartments->isNotEmpty()): ?>
                <div class="org-chart-wrapper">
                    <ul class="org-tree">
                        <?php echo $__env->make('org-chart._node', ['nodes' => $rootDepartments, 'allDepartments' => $departments], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </ul>
                </div>
            <?php else: ?>
                <div class="org-empty">
                    <i class="bi bi-diagram-3"></i>
                    <h6 class="text-white mb-2">No hay departamentos para mostrar</h6>
                    <p class="mb-3">Crea departamentos y define su jerarquía asignando un departamento padre.</p>
                    <a href="<?php echo e(route('departments.create')); ?>" class="btn btn-primary-custom">
                        <i class="bi bi-plus-lg me-1"></i> Crear primer departamento
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/org-chart/index.blade.php ENDPATH**/ ?>