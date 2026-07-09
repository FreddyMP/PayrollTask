<?php $__env->startSection('title', 'TSS - Tesorería de la Seguridad Social'); ?>
<?php $__env->startSection('page-title', 'Reporte TSS'); ?>

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
            <a class="nav-link" href="<?php echo e(route('payroll.christmas')); ?>"
                style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
                Salario Navidad
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="<?php echo e(route('payroll.tss')); ?>"
                style="color: white; border-bottom: 2px solid var(--primary); background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
                TSS
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('payroll.ir17')); ?>"
                style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
                IR-3s
            </a>
        </li>
    </ul>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <form action="<?php echo e(route('payroll.tss')); ?>" method="GET" class="d-flex gap-2 align-items-center">
            <label class="text-secondary small me-2">Período:</label>
            <select name="period" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <?php $__currentLoopData = $availablePeriods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $periodLabel = \Carbon\Carbon::parse($p)->translatedFormat('F Y');
                    ?>
                    <option value="<?php echo e($p); ?>" <?php echo e($period == $p ? 'selected' : ''); ?>>
                        <?php echo e(ucfirst($periodLabel)); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </form>
        <div>
            <button onclick="window.print()" class="btn btn-outline-custom btn-sm">
                <i class="bi bi-printer me-1"></i> Imprimir Reporte
            </button>
        </div>
    </div>

    <div class="alert alert-info border-0 shadow-sm mb-4" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
        <div class="d-flex">
            <i class="bi bi-info-circle-fill me-3 fs-4"></i>
            <div>
                <h6 class="alert-heading fw-bold mb-1">Información de Tasas y Topes (Ley 2026)</h6>
                <p class="mb-0 small">
                    <strong>SFS:</strong> 3.04%/7.09% (Tope RD$ 232,230) |
                    <strong>AFP:</strong> 2.87%/7.10% (Tope RD$ 464,460) |
                    <strong>SRL:</strong> <?php echo e($company->srl_rate ?? '1.10'); ?>% (Tope RD$ 92,892) |
                    <strong>INFOTEP:</strong> 1%
                </p>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="color: #e2e8f0;">
                    <thead style="background: rgba(255,255,255,0.02); border-bottom: 2px solid rgba(255,255,255,0.05);">
                        <tr>
                            <th class="ps-4">Empleado</th>
                            <th>Salario Bruto</th>
                            <th class="text-center" style="background: rgba(52, 211, 153, 0.05);">SFS (Emp/Pat)</th>
                            <th class="text-center" style="background: rgba(96, 165, 250, 0.05);">AFP (Emp/Pat)</th>
                            <th class="text-center">SRL</th>
                            <th class="text-center">INFOTEP</th>
                            <th class="pe-4 text-end">Total TSS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td class="ps-4">
                                    <div class="fw-semibold"><?php echo e($row['employee']); ?></div>
                                </td>
                                <td>RD$ <?php echo e(number_format($row['salary'], 2)); ?></td>
                                <td class="text-center" style="background: rgba(52, 211, 153, 0.02);">
                                    <div class="small">E: <?php echo e(number_format($row['sfs_emp'], 2)); ?></div>
                                    <div class="fw-bold">P: <?php echo e(number_format($row['sfs_pat'], 2)); ?></div>
                                </td>
                                <td class="text-center" style="background: rgba(96, 165, 250, 0.02);">
                                    <div class="small">E: <?php echo e(number_format($row['afp_emp'], 2)); ?></div>
                                    <div class="fw-bold">P: <?php echo e(number_format($row['afp_pat'], 2)); ?></div>
                                </td>
                                <td class="text-center">
                                    <?php echo e(number_format($row['srl_pat'], 2)); ?>

                                </td>
                                <td class="text-center">
                                    <?php echo e(number_format($row['infotep_pat'], 2)); ?>

                                </td>
                                <td class="pe-4 text-end">
                                    <?php 
                                        $total = $row['sfs_emp'] + $row['sfs_pat'] + $row['afp_emp'] + $row['afp_pat'] + $row['srl_pat'] + $row['infotep_pat'];
                                    ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold" style="font-size: 0.9rem;">
                                        RD$ <?php echo e(number_format($total, 2)); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-dark">
                                    <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                                    No hay registros de nómina para el período <?php echo e($period); ?>.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if($report->count() > 0): ?>
                        <tfoot style="background: rgba(255,255,255,0.03); border-top: 2px solid rgba(255,255,255,0.1);">
                            <tr class="fw-bold border-0">
                                <td class="ps-4">TOTALES</td>
                                <td>RD$ <?php echo e(number_format($report->sum('salary'), 2)); ?></td>
                                <td class="text-center">
                                    RD$ <?php echo e(number_format($report->sum('sfs_emp') + $report->sum('sfs_pat'), 2)); ?>

                                </td>
                                <td class="text-center">
                                    RD$ <?php echo e(number_format($report->sum('afp_emp') + $report->sum('afp_pat'), 2)); ?>

                                </td>
                                <td class="text-center">RD$ <?php echo e(number_format($report->sum('srl_pat'), 2)); ?></td>
                                <td class="text-center">RD$ <?php echo e(number_format($report->sum('infotep_pat'), 2)); ?></td>
                                <td class="pe-4 text-end text-primary" style="font-size: 1.1rem;">
                                    <?php 
                                                                $grandTotal = $report->sum('sfs_emp') + $report->sum('sfs_pat') +
                                        $report->sum('afp_emp') + $report->sum('afp_pat') +
                                        $report->sum('srl_pat') + $report->sum('infotep_pat');
                                    ?>
                                    RD$ <?php echo e(number_format($grandTotal, 2)); ?>

                                </td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>

               </div>
        </div>
    </div>

    <div class="mt-4 text-secondary small">
        <i class="bi bi-shield-check me-1"></i> Reporte generado según parámetros de la Tesorería de la Seguridad Social (TSS) de República Dominicana.
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/payroll/tss.blade.php ENDPATH**/ ?>