<?php $__env->startSection('title', 'Nómina'); ?>
<?php $__env->startSection('page-title', 'Nómina'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $company = auth()->user()->company;
        $isBiweekly = $company->payroll_frequency === 'biweekly';
        $currentPeriod = $isBiweekly
            ? date('Y-m') . (date('j') <= 15 ? '-Q1' : '-Q2')
            : date('Y-m');

        $totalEmployees = \App\Models\Employee::where('company_id', auth()->user()->company_id)->count();
        $periodPayrolls = \App\Models\Payroll::where('company_id', auth()->user()->company_id)
            ->where('period', $currentPeriod)->get();
        $generatedCount = $periodPayrolls->count();
        $pendingCount = $periodPayrolls->where('status', 'pending')->count();

        // El botón solo se muestra cuando:
        // 1) Todos los empleados tienen nómina generada para el período actual
        // 2) Al menos una nómina está pendiente de pago
        $showMarkAllBtn = $totalEmployees > 0 && $generatedCount >= $totalEmployees && $pendingCount > 0;
    ?>
    <ul class="nav nav-tabs mb-4 px-3" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
        <li class="nav-item">
            <a class="nav-link active" href="<?php echo e(route('payroll.index')); ?>"
                style="color: white; border-bottom: 2px solid var(--primary); background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
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
            <a class="nav-link" href="<?php echo e(route('payroll.tss')); ?>"
                style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
                Exportar TSS
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?php echo e(route('payroll.ir17')); ?>"
                style="color: #94a3b8; background: transparent; border: 0; padding: 0.75rem 1.25rem;">
                Generar IR-3
            </a>
        </li>
    </ul>

    <div class="d-flex justify-content-end align-items-center mb-3">
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-custom" data-bs-toggle="collapse" data-bs-target="#filterCard"
                aria-expanded="false" aria-controls="filterCard">
                <i class="bi bi-funnel me-1"></i> Filtros
            </button>
            <?php if(!$showMarkAllBtn): ?>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#autoGenerateModal">
                    <i class="bi bi-magic me-1"></i> Generar Automáticamente
                </button>
            <?php endif; ?>
            <?php if($showMarkAllBtn): ?>
                <button type="button" class="btn btn-mark-all" data-bs-toggle="modal" data-bs-target="#markAllPaidModal"
                    title="Marcar toda la nómina del período <?php echo e($currentPeriod); ?> como pagada">
                    <i class="bi bi-check2-all me-1"></i> Marcar Todos como Pagados
                    <span class="badge-pending-count"><?php echo e($pendingCount); ?></span>
                </button>
            <?php endif; ?>
            <a href="<?php echo e(route('payroll.create')); ?>" class="btn btn-primary-custom">
                <i class="bi bi-plus-lg me-1"></i> Nueva Nómina
            </a>
        </div>
    </div>

    <!-- Card de Filtros Colapsable -->
    <div class="collapse mb-4" id="filterCard">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('payroll.index')); ?>" id="filterForm">
                    <div class="row g-3">
                        <!-- Búsqueda por empleado -->
                        <div class="col-md-3">
                            <label for="employee_name" class="form-label">Empleado</label>
                            <input type="text" class="form-control" id="employee_name" name="employee_name"
                                placeholder="Buscar por nombre" value="<?php echo e(request('employee_name')); ?>">
                        </div>

                        <!-- Filtro por período -->
                        <div class="col-md-3">
                            <label for="period" class="form-label">Período</label>
                            <select class="form-select" id="period" name="period">
                                <option value="">Todos los períodos</option>
                                <?php
                                    $periods = \App\Models\Payroll::where('company_id', auth()->user()->company_id)
                                        ->distinct()
                                        ->orderBy('period', 'desc')
                                        ->pluck('period');
                                ?>
                                <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($p); ?>" <?php echo e(request('period') == $p ? 'selected' : ''); ?>><?php echo e($p); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Filtro por estado -->
                        <div class="col-md-2">
                            <label for="status" class="form-label">Estado</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Todos</option>
                                <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pendiente
                                </option>
                                <option value="paid" <?php echo e(request('status') == 'paid' ? 'selected' : ''); ?>>Pagado</option>
                            </select>
                        </div>

                        <!-- Filtro por rango de salario (mínimo) -->
                        <div class="col-md-2">
                            <label for="salary_min" class="form-label">Salario Mínimo</label>
                            <input type="number" class="form-control" id="salary_min" name="salary_min" placeholder="Min"
                                step="0.01" value="<?php echo e(request('salary_min')); ?>">
                        </div>

                        <!-- Filtro por rango de salario (máximo) -->
                        <div class="col-md-2">
                            <label for="salary_max" class="form-label">Salario Máximo</label>
                            <input type="number" class="form-control" id="salary_max" name="salary_max" placeholder="Max"
                                step="0.01" value="<?php echo e(request('salary_max')); ?>">
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="<?php echo e(route('payroll.index')); ?>" class="btn btn-outline-custom">
                            <i class="bi bi-x-circle me-1"></i> Limpiar Filtros
                        </a>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-search me-1"></i> Aplicar Filtros
                        </button>
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
                            <th>Período</th>
                            <th>Salario Bruto</th>
                            <th>Deducciones</th>
                            <th>Salario Neto</th>
                            <th>Fecha Pago</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $payrolls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payroll): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="fw-semibold"><?php echo e($payroll->employee->user->name ?? '—'); ?></td>
                                <td><?php echo e($payroll->period); ?></td>
                                <td>RD$ <?php echo e(number_format($payroll->gross_salary, 2)); ?></td>
                                <td style="color: #f87171;">-RD$ <?php echo e(number_format($payroll->deductions, 2)); ?></td>
                                <td class="fw-semibold" style="color: var(--success);">RD$
                                    <?php echo e(number_format($payroll->net_salary, 2)); ?>

                                </td>
                                <td><?php echo e($payroll->payment_date?->format('d/m/Y') ?? '—'); ?></td>
                                <td><span
                                        class="badge-status badge-<?php echo e($payroll->status); ?>"><?php echo e(ucfirst($payroll->status)); ?></span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <?php if($payroll->status === 'pending'): ?>
                                            <a href="<?php echo e(route('payroll.edit', $payroll)); ?>" class="btn btn-outline-custom btn-sm"
                                                style="color: #60a5fa;" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="<?php echo e(route('payroll.markPaid', $payroll)); ?>" class="d-inline"
                                                onsubmit="return submitMarkPaid(this)">
                                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="btn btn-outline-custom btn-sm" style="color: #34d399;"
                                                    title="Marcar como pagado">
                                                    <i class="bi bi-check-circle btn-icon"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" action="<?php echo e(route('payroll.destroy', $payroll)); ?>" class="d-inline"
                                            onsubmit="return confirm('¿Eliminar este registro?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-outline-custom btn-sm" style="color: #f87171;"
                                                title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="text-center text-dark py-4">No hay registros de nómina</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3"><?php echo e($payrolls->links()); ?></div>
<?php $__env->stopSection(); ?>

<!-- Modal para Generar Nómina Automáticamente -->
<div class="modal fade" id="autoGenerateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generar Nómina Automáticamente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?php echo e(route('payroll.autoGenerate')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Período</label>
                        <select class="form-select" name="period" required>
                            <?php
                                $company = auth()->user()->company;
                                $isBiweekly = $company->payroll_frequency === 'biweekly';
                                $currentPeriod = $isBiweekly
                                    ? date('Y-m') . (date('j') <= 15 ? '-Q1' : '-Q2')
                                    : date('Y-m');
                                $periods = [];
                                for ($i = -3; $i <= 1; $i++) {
                                    $date = \Carbon\Carbon::now()->addMonths($i);
                                    if ($isBiweekly) {
                                        $periods[] = [
                                            'value' => $date->format('Y-m') . '-Q1',
                                            'label' => ucfirst($date->translatedFormat('F Y')) . ' — 1ª Quincena (1-15)',
                                        ];
                                        $periods[] = [
                                            'value' => $date->format('Y-m') . '-Q2',
                                            'label' => ucfirst($date->translatedFormat('F Y')) . ' — 2ª Quincena (16-fin)',
                                        ];
                                    } else {
                                        $periods[] = [
                                            'value' => $date->format('Y-m'),
                                            'label' => ucfirst($date->translatedFormat('F Y')),
                                        ];
                                    }
                                }
                                $periods = array_reverse($periods);
                            ?>
                            <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($period['value']); ?>" <?php echo e($period['value'] == $currentPeriod ? 'selected' : ''); ?>>
                                    <?php echo e($period['label']); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Esto generará automáticamente la nómina para todos los empleados activos que no tengan nómina
                        registrada para este período. Se incluirán las horas extra aprobadas automáticamente.
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-magic me-1"></i> Generar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Marcar Todos como Pagados -->
<div class="modal fade" id="markAllPaidModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content mark-all-modal">
            <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <span class="mark-all-icon"><i class="bi bi-check2-all"></i></span>
                    Confirmar Pago Masivo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mark-all-info-box">
                    <i class="bi bi-calendar2-check me-2"></i>
                    <strong>Período actual:</strong>&nbsp;<span class="period-badge"><?php echo e($currentPeriod); ?></span>
                </div>
                <p class="mt-3 mb-1" style="color: #cbd5e1;">
                    Esta acción marcará como <strong style="color:#34d399;">pagadas</strong>
                    las <strong><?php echo e($pendingCount); ?></strong> nómina(s) pendientes del período actual
                    y enviará automáticamente el <strong>volante de pago</strong> por correo a cada empleado.
                </p>
                <div class="alert-warning-custom mt-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Esta operación no se puede deshacer.
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.1);">
                <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?php echo e(route('payroll.markAllPaid')); ?>" id="markAllPaidForm"
                    onsubmit="return submitMarkAllPaid(this)">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-mark-all-confirm" id="markAllPaidBtn">
                        <i class="bi bi-check2-all me-1"></i> Confirmar y Pagar Todos
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Mostrar el card de filtros si hay filtros activos
        document.addEventListener('DOMContentLoaded', function () {
            const hasFilters = <?php echo e((request()->has('employee_name') || request()->has('period') || request()->has('status') || request()->has('salary_min') || request()->has('salary_max')) ? 'true' : 'false'); ?>;
            if (hasFilters) {
                const filterCard = document.getElementById('filterCard');
                if (filterCard) {
                    filterCard.classList.add('show');
                }
            }
        });

        function submitMarkPaid(form) {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            }
            return true;
        }

        function submitMarkAllPaid(form) {
            const btn = document.getElementById('markAllPaidBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Procesando...';
            }
            return true;
        }
    </script>

    <style>
        .btn-mark-all {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
            transition: all 0.2s ease;
            animation: pulse-green 2.5s infinite;
        }

        .btn-mark-all:hover {
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
            transform: translateY(-1px);
            color: #fff;
        }

        @keyframes pulse-green {

            0%,
            100% {
                box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
            }

            50% {
                box-shadow: 0 4px 22px rgba(16, 185, 129, 0.6);
            }
        }

        .badge-pending-count {
            background: rgba(255, 255, 255, 0.25);
            border-radius: 999px;
            padding: 0.1rem 0.55rem;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .mark-all-modal {
            background: #1e293b;
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
        }

        .mark-all-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #059669, #10b981);
            color: #fff;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            font-size: 1rem;
        }

        .mark-all-info-box {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: #a7f3d0;
            font-size: 0.9rem;
        }

        .period-badge {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.4);
            border-radius: 6px;
            padding: 0.15rem 0.5rem;
            font-family: monospace;
            font-size: 0.85rem;
            color: #34d399;
        }

        .alert-warning-custom {
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.35);
            border-radius: 10px;
            padding: 0.65rem 1rem;
            color: #fcd34d;
            font-size: 0.85rem;
        }

        .btn-mark-all-confirm {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-mark-all-confirm:hover {
            background: linear-gradient(135deg, #047857 0%, #059669 100%);
            color: #fff;
        }

        .btn-mark-all-confirm:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/payroll/index.blade.php ENDPATH**/ ?>