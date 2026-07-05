<?php $__env->startSection('title', 'Detalle Empleado'); ?>
<?php $__env->startSection('page-title', 'Detalle del Empleado'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div style="width: 80px; height: 80px; border-radius: 20px; background: linear-gradient(135deg, #6366f1, #a855f7); display: inline-flex; align-items: center; justify-content: center; font-size: 1.8rem; color: white; font-weight: 700; margin-bottom: 1rem;">
                    <?php echo e(strtoupper(substr($employee->user->name ?? '', 0, 2))); ?>

                </div>
                <h5 class="mb-1" style="color: white;"><?php echo e($employee->user->name); ?></h5>
                <p style="font-size: 0.85rem; color: #64748b;"><?php echo e($employee->position?->title ?? $employee->user->position ?? '—'); ?></p>
                <span class="badge-status badge-<?php echo e($employee->user->role); ?>"><?php echo e(ucfirst($employee->user->role)); ?></span>
                <span class="badge-status badge-<?php echo e($employee->user->status); ?>"><?php echo e(ucfirst($employee->user->status)); ?></span>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header text-secondary">Información de Contacto</div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-white">Email</small>
                    <div style="color: var(--success);" ><?php echo e($employee->user->email); ?></div>
                </div>
                <div class="mb-2">
                    <small class="text-white">Teléfono</small>
                    <div style="color: var(--success);"><?php echo e($employee->user->phone ?? '—'); ?></div>
                </div>
                <div>
                    <small class="text-white">Cédula</small>
                    <div style="color: var(--success);"><?php echo e($employee->id_number ?? '—'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header text-secondary">Información Laboral</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <small class="text-white">Salario</small>
                        <div class="fw-semibold" style="color: var(--success);">RD$ <?php echo e(number_format($employee->salary, 2)); ?></div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-white">Fecha de Ingreso</small>
                        <div class="fw-semibold" style="color: var(--success);"><?php echo e($employee->hire_date?->format('d/m/Y') ?? '—'); ?></div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-white">Tipo de Contrato</small>
                        <div class="fw-semibold" style="color: var(--success);">
                            <?php $contracts = ['full_time' => 'Tiempo Completo', 'part_time' => 'Medio Tiempo', 'contractor' => 'Contratista']; ?>
                            <?php echo e($contracts[$employee->contract_type] ?? $employee->contract_type); ?>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-white">Cuenta Bancaria</small>
                        <div class="fw-semibold" style="color: var(--success);"><?php echo e($employee->bank_account ?? '—'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header text-secondary">Documentos del Empleado</div>
            <div class="card-body p-0">
                <?php if($employee->documents->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Nombre del Documento</th>
                                <th>Fecha</th>
                                <th class="text-end">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $employee->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($doc->name); ?></td>
                                <td><?php echo e($doc->created_at->format('d/m/Y')); ?></td>
                                <td class="text-end">
                                    <a href="<?php echo e(Storage::url($doc->file_path)); ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="p-3 text-center text-white">No hay documentos registrados para este empleado.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center text-secondary">
                <span>Historial de Nómina</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Período</th>
                                <th>Bruto</th>
                                <th>Deducciones</th>
                                <th>Neto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $employee->payrolls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payroll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($payroll->period); ?></td>
                                <td>RD$ <?php echo e(number_format($payroll->gross_salary, 2)); ?></td>
                                <td style="color: #f87171;">-RD$ <?php echo e(number_format($payroll->deductions, 2)); ?></td>
                                <td class="fw-semibold" style="color: var(--success);">RD$ <?php echo e(number_format($payroll->net_salary, 2)); ?></td>
                                <td><span class="badge-status badge-<?php echo e($payroll->status); ?>"><?php echo e(ucfirst($payroll->status)); ?></span></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="5" class="text-center text-white py-3">Sin registros de nómina</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="<?php echo e(route('employees.index')); ?>" class="btn btn-outline-custom"><i class="bi bi-arrow-left me-1"></i> Volver</a>
    <a href="<?php echo e(route('employees.edit', $employee)); ?>" class="btn btn-primary-custom"><i class="bi bi-pencil me-1"></i> Editar</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/employees/show.blade.php ENDPATH**/ ?>