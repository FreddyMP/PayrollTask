<?php $__env->startSection('title', 'Salario Navidad'); ?>
<?php $__env->startSection('page-title', 'Salario de Navidad'); ?>

<?php $__env->startSection('content'); ?>
    <ul class="nav nav-tabs mb-4 px-3" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
        <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('payroll.index')); ?>"
                style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
                Registros de Nómina
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('payroll.bonuses')); ?>"
                style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
                Bonificaciones de Ley
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('payroll.benefits')); ?>"
                style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
                Prestaciones Laborales
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="<?php echo e(route('payroll.christmas')); ?>"
                style="color: white; border-bottom: 2px solid var(--primary); background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
                Salario Navidad
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('payroll.tss')); ?>"
                style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
                TSS
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('payroll.ir17')); ?>"
                style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
                IR-3
            </a>
        </li>
    </ul>

    <div class="card">
        <div class="card-header text-secondary d-flex justify-content-between align-items-center">
            <span>Cálculo de Salario de Navidad</span>
            <button onclick="window.print()" class="btn btn-sm btn-outline-custom">
                <i class="bi bi-printer me-1"></i> Imprimir Reporte
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Fecha de Ingreso</th>
                            <th>Meses Trabajados</th>
                            <th>Salario Mensual</th>
                            <th class="text-end">Salario Navidad</th>
                            <th class="text-center">Estado / Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $totalChristmas = 0; ?>
                        <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php if($employee->hire_date): ?>
                                <?php $totalChristmas += $employee->christmas_salary; ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo e($employee->user->name); ?></td>
                                    <td><?php echo e($employee->hire_date->format('d/m/Y')); ?></td>
                                    <td><?php echo e($employee->months_worked); ?></td>
                                    <td>RD$ <?php echo e(number_format($employee->salary, 2)); ?></td>
                                    <td class="text-end fw-bold" style="color: var(--success);">RD$
                                        <?php echo e(number_format($employee->christmas_salary, 2)); ?></td>
                                    <td class="text-center">
                                        <?php if(in_array($employee->id, $paidEmployeeIds ?? [])): ?>
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                                <i class="bi bi-check-circle me-1"></i> Pagado
                                            </span>
                                        <?php else: ?>
                                            <form action="<?php echo e(route('payroll.christmas.pay', $employee)); ?>" method="POST"
                                                class="d-inline"
                                                onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerHTML = '<span class=\'spinner-border spinner-border-sm\' role=\'status\' aria-hidden=\'true\'></span>';">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-custom"
                                                    title="Marcar como pagado y enviar correo">
                                                    <i class="bi bi-envelope-check me-1"></i> Pagar
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-dark">No hay empleados registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-primary bg-opacity-10 border-top border-primary">
                            <td colspan="4" class="fw-bold text-white text-end py-3">TOTAL SALARIO NAVIDAD:</td>
                            <td class="text-end fw-bold text-primary py-3" style="font-size: 1.1rem;">RD$
                                <?php echo e(number_format($totalChristmas, 2)); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/payroll/christmas.blade.php ENDPATH**/ ?>