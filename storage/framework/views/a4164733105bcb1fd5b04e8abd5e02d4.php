<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card purple">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-value"><?php echo e($totalEmployees); ?></div>
                <div class="stat-label">Empleados Activos</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card green">
                <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-value">
                    <?php echo e($payrollSummaryLatest ? number_format($payrollSummaryLatest->total_net, 0, '.', ',') : '0'); ?></div>
                <div class="stat-label">Nómina Neta <?php echo e($latestPeriod ?? ''); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="bi bi-briefcase-fill"></i></div>
                <div class="stat-value"><?php echo e($openVacancies); ?></div>
                <div class="stat-label">Vacantes Abiertas</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card orange">
                <div class="stat-icon"><i class="bi bi-person-badge-fill"></i></div>
                <div class="stat-value"><?php echo e($activeCandidates); ?></div>
                <div class="stat-label">Candidatos Activos</div>
            </div>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="text-secondary"><i class="bi bi-receipt-cutoff me-2"></i>Historial de Nómina</span>
                    <a href="<?php echo e(route('payroll.index')); ?>" class="btn btn-outline-custom btn-sm">Ver nómina</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Período</th>
                                    <th>Empleados</th>
                                    <th>Bruto</th>
                                    <th>Deducciones</th>
                                    <th>Neto</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $payrollHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo e($ph->period); ?></td>
                                        <td><?php echo e($ph->employee_count); ?></td>
                                        <td>RD$ <?php echo e(number_format($ph->total_gross, 2)); ?></td>
                                        <td style="color:#f87171;">RD$ <?php echo e(number_format($ph->total_deductions, 2)); ?></td>
                                        <td style="color:#34d399;font-weight:600;">RD$ <?php echo e(number_format($ph->total_net, 2)); ?>

                                        </td>
                                        <td>
                                            <?php if($ph->pending_count > 0): ?>
                                                <span class="badge-status badge-pending"><?php echo e($ph->pending_count); ?> pend.</span>
                                            <?php endif; ?>
                                            <?php if($ph->paid_count > 0): ?>
                                                <span class="badge-status badge-paid"><?php echo e($ph->paid_count); ?> pag.</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-dark py-4">Sin registros de nómina</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-header text-secondary"><i class="bi bi-bar-chart-fill me-2"></i>Distribución Salarial &
                    Deducciones</div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-2" style="font-size:0.78rem;color:#94a3b8;">
                                <span>Mínimo</span><span>Promedio</span><span>Máximo</span>
                            </div>
                            <?php
                                $min = $salaryStats->min_salary ?? 0;
                                $max = $salaryStats->max_salary ?? 1;
                                $avg = $salaryStats->avg_salary ?? 0;
                                $pct = $max > $min ? (($avg - $min) / ($max - $min)) * 100 : 50;
                            ?>
                            <div class="dash-salary-bar">
                                <div class="dash-salary-avg" style="left:<?php echo e($pct); ?>%;" title="Promedio"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1" style="font-size:0.8rem;font-weight:600;">
                                <span style="color:#818cf8;">RD$ <?php echo e(number_format($min, 0)); ?></span>
                                <span style="color:#34d399;">RD$ <?php echo e(number_format($avg, 0)); ?></span>
                                <span style="color:#fbbf24;">RD$ <?php echo e(number_format($max, 0)); ?></span>
                            </div>
                        </div>
                        <div class="col-md-6 text-white">
                            <div style="font-size:0.75rem;color:#94a3b8;margin-bottom:0.5rem;font-weight:600;">DESGLOSE
                                PROMEDIO DEDUCCIONES</div>

                            <?php if($deductionBreakdown): ?>

                                <?php
                                    $dItems = [
                                        ['label' => 'ARS (SFS)', 'val' => $deductionBreakdown->avg_ars, 'color' => '#818cf8'],
                                        ['label' => 'AFP', 'val' => $deductionBreakdown->avg_afp, 'color' => '#06b6d4'],
                                        ['label' => 'ISR', 'val' => $deductionBreakdown->avg_isr, 'color' => '#f59e0b'],
                                        ['label' => 'Otros', 'val' => $deductionBreakdown->avg_otros, 'color' => '#ef4444'],
                                    ];
                                    $dTotal = array_sum(array_column($dItems, 'val')) ?: 1;
                                ?>
                                <?php $__currentLoopData = $dItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div
                                            style="width:10px;height:10px;border-radius:3px;background:<?php echo e($d['color']); ?>;flex-shrink:0;">
                                        </div>
                                        <span style="font-size:0.8rem;width:70px;"><?php echo e($d['label']); ?></span>
                                        <div
                                            style="flex:1;height:8px;background:rgba(255,255,255,0.06);border-radius:4px;overflow:hidden;">
                                            <div
                                                style="height:100%;width:<?php echo e(($d['val'] / $dTotal) * 100); ?>%;background:<?php echo e($d['color']); ?>;border-radius:4px;transition:width 0.6s ease;">
                                            </div>
                                        </div>
                                        <span
                                            style="font-size:0.78rem;font-weight:600;color:<?php echo e($d['color']); ?>;min-width:75px;text-align:right;">RD$
                                            <?php echo e(number_format($d['val'], 0)); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <p style="font-size:0.85rem;">Sin datos de deducciones</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="text-secondary"><i class="bi bi-funnel-fill me-2"></i>Pipeline de Reclutamiento</span>
                    <a href="<?php echo e(route('recruitment.index')); ?>" class="btn btn-outline-custom btn-sm">Ver vacantes</a>
                </div>
                <div class="card-body">
                    <?php $__empty_1 = true; $__currentLoopData = $vacancyPipeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="dash-pipeline-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div style="font-weight:700;font-size:0.9rem;color:white;"><?php echo e($v->title); ?></div>
                                    <div style="font-size:0.72rem;color:#64748b;"><?php echo e($v->department ?: 'Sin depto.'); ?> ·
                                        <?php echo e($v->steps_count); ?> pasos</div>
                                </div>
                                <div class="d-flex gap-1">
                                    <span class="badge-status badge-active" style="font-size:0.6rem;"><?php echo e($v->candidates_count); ?>

                                        cand.</span>
                                    <?php if($v->hired_count > 0): ?>
                                        <span class="badge-status badge-completed" style="font-size:0.6rem;"><?php echo e($v->hired_count); ?>

                                            contrat.</span>
                                    <?php endif; ?>
                                    <?php if($v->discarded_count > 0): ?>
                                        <span class="badge-status badge-rejected"
                                            style="font-size:0.6rem;"><?php echo e($v->discarded_count); ?> desc.</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                                $total = $v->candidates_count ?: 1;
                                $hiredPct = ($v->hired_count / $total) * 100;
                                $discardedPct = ($v->discarded_count / $total) * 100;
                                $activePct = 100 - $hiredPct - $discardedPct;
                            ?>
                            <div class="dash-pipeline-bar">
                                <div style="width:<?php echo e($hiredPct); ?>%;background:var(--success);"></div>
                                <div style="width:<?php echo e($activePct); ?>%;background:var(--primary);"></div>
                                <div style="width:<?php echo e($discardedPct); ?>%;background:var(--danger);opacity:0.6;"></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-white text-center py-4" style="font-size:0.85rem;">No hay vacantes abiertas</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4">
            
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="text-secondary"><i class="bi bi-send-fill me-2"></i>Solicitudes RRHH</span>
                    <span class="badge-status badge-pending"><?php echo e($pendingRequests); ?> pend.</span>
                </div>
                <div class="card-body py-2 px-3 text-white">
                    <?php
                        $typeLabels = ['vacation' => 'Vacaciones', 'permission' => 'Permisos', 'work_letter' => 'Carta Laboral', 'overtime' => 'Horas Extra'];
                        $typeIcons = ['vacation' => 'bi-sun-fill', 'permission' => 'bi-calendar2-check', 'work_letter' => 'bi-file-earmark-text', 'overtime' => 'bi-alarm-fill'];
                        $typeColors = ['vacation' => '#22d3ee', 'permission' => '#fbbf24', 'work_letter' => '#c084fc', 'overtime' => '#fb923c'];
                    ?>
                    <?php $__empty_1 = true; $__currentLoopData = $requestsByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $statuses): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="d-flex align-items-center gap-2 py-2"
                            style="border-bottom:1px solid rgba(255,255,255,0.04);">
                            <div
                                style="width:30px;height:30px;border-radius:8px;background:rgba(255,255,255,0.04);display:flex;align-items:center;justify-content:center;">
                                <i class="bi <?php echo e($typeIcons[$type] ?? 'bi-question-circle'); ?>"
                                    style="color:<?php echo e($typeColors[$type] ?? '#94a3b8'); ?>;font-size:0.85rem;"></i>
                            </div>
                            <div style="flex:1;">
                                <div style="font-size:0.8rem;font-weight:600;"><?php echo e($typeLabels[$type] ?? ucfirst($type)); ?></div>
                            </div>
                            <?php $__currentLoopData = ['pending' => 'badge-pending', 'approved' => 'badge-approved', 'rejected' => 'badge-rejected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st => $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(isset($statuses[$st]) && $statuses[$st] > 0): ?>
                                    <span class="badge-status <?php echo e($cls); ?>" style="font-size:0.6rem;"><?php echo e($statuses[$st]); ?></span>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-white text-center py-3" style="font-size:0.85rem;">Sin solicitudes</p>
                    <?php endif; ?>
                    <a href="<?php echo e(route('requests.index')); ?>" class="btn btn-outline-custom btn-sm w-100 mt-2">Ver
                        solicitudes</a>
                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-header text-secondary"><i class="bi bi-diagram-3-fill me-2"></i>Empleados por Departamento
                </div>
                <div class="card-body py-2 px-3 text-white">
                    <?php $maxDept = $departmentDistribution->max('count') ?: 1; ?>
                    <?php $__empty_1 = true; $__currentLoopData = $departmentDistribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span
                                style="font-size:0.8rem;width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                title="<?php echo e($dept->department); ?>"><?php echo e($dept->department); ?></span>
                            <div style="flex:1;height:8px;background:rgba(255,255,255,0.06);border-radius:4px;overflow:hidden;">
                                <div
                                    style="height:100%;width:<?php echo e(($dept->count / $maxDept) * 100); ?>%;background:var(--gradient-1);border-radius:4px;">
                                </div>
                            </div>
                            <span
                                style="font-size:0.78rem;font-weight:700;color:white;min-width:20px;text-align:right;"><?php echo e($dept->count); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-white text-center py-2" style="font-size:0.85rem;">Sin departamentos</p>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-header text-secondary"><i class="bi bi-clock-history me-2"></i>Accesos Recientes</div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $recentAccess; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="d-flex align-items-center gap-3 px-3 py-2"
                            style="border-bottom:1px solid rgba(255,255,255,0.04);">
                            <div
                                style="width:30px;height:30px;border-radius:8px;background:var(--gradient-1);display:flex;align-items:center;justify-content:center;font-size:0.65rem;color:white;font-weight:700;">
                                <?php echo e(strtoupper(substr($log->user->name ?? '', 0, 2))); ?>

                            </div>
                            <div>
                                <div style="font-size:0.78rem;font-weight:600;color:white;"><?php echo e($log->user->name ?? ''); ?></div>
                                <div style="font-size:0.68rem;color:#64748b;"><?php echo e($log->login_at->format('d/m H:i')); ?> —
                                    <?php echo e($log->logout_at ? $log->logout_at->format('H:i') : 'Activo'); ?></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-3 text-center text-white" style="font-size:0.85rem;">Sin registros</div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-header text-secondary"><i class="bi bi-person-lines-fill me-2"></i>Candidatos Recientes
                </div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $recentCandidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="d-flex align-items-center gap-3 px-3 py-2"
                            style="border-bottom:1px solid rgba(255,255,255,0.04);">
                            <div
                                style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#06b6d4,#3b82f6);display:flex;align-items:center;justify-content:center;font-size:0.65rem;color:white;font-weight:700;">
                                <?php echo e(strtoupper(substr($c->name, 0, 2))); ?>

                            </div>
                            <div style="flex:1;min-width:0;">
                                <div
                                    style="font-size:0.8rem;font-weight:600;color:white;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    <?php echo e($c->name); ?></div>
                                <div
                                    style="font-size:0.68rem;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    <?php echo e($c->vacancy->title ?? ''); ?></div>
                            </div>
                            <span class="badge-status badge-<?php echo e($c->status); ?>"><?php echo e($c->status); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-3 text-center text-white" style="font-size:0.85rem;">Sin candidatos</div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="card">
                <div class="card-header text-secondary"><i class="bi bi-person-check-fill me-2"></i>Contrataciones Recientes
                </div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = $recentHires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="d-flex align-items-center gap-3 px-3 py-2"
                            style="border-bottom:1px solid rgba(255,255,255,0.04);">
                            <div
                                style="width:32px;height:32px;border-radius:8px;background:var(--gradient-3);display:flex;align-items:center;justify-content:center;font-size:0.65rem;color:white;font-weight:700;">
                                <?php echo e(strtoupper(substr($h->name, 0, 2))); ?>

                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:0.8rem;font-weight:600;color:white;"><?php echo e($h->name); ?></div>
                                <div style="font-size:0.68rem;color:#64748b;"><?php echo e($h->vacancy->title ?? ''); ?></div>
                            </div>
                            <span style="font-size:0.68rem;color:#34d399;"><i
                                    class="bi bi-check-circle-fill me-1"></i>Contratado</span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-3 text-center text-white" style="font-size:0.85rem;">Sin contrataciones</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <?php if($showPayrollFrequencyModal): ?>
        <div class="pf-overlay" id="payrollFreqModal">
            <div class="pf-modal">
                <div class="pf-modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="pf-icon-wrap">
                            <i class="bi bi-calendar2-week-fill" style="color:white;font-size:1.4rem;"></i>
                        </div>
                        <div>
                            <h5 class="pf-title">Configuración de Nómina</h5>
                            <p class="pf-subtitle">Este paso es obligatorio antes de continuar</p>
                        </div>
                    </div>
                </div>
                <div class="pf-modal-body">
                    <p class="pf-desc">
                        Para calcular correctamente los periodos, impuestos, descuentos y bonificaciones, configure la nómina de
                        <strong style="color:white;"><?php echo e(Auth::user()->company->name); ?></strong>.
                    </p>

                    <!-- PASO 1: Frecuencia de Nómina -->
                    <div class="pf-section" id="step1">
                        <div class="pf-step-title"><i class="bi bi-1-circle-fill me-2"></i>Frecuencia de Nómina</div>
                        <div class="pf-options">
                            <label class="pf-option" data-step="1" data-value="monthly">
                                <input type="radio" name="pf_frequency" value="monthly" hidden>
                                <div class="pf-option-icon pf-icon-monthly"><i class="bi bi-calendar-month-fill"></i></div>
                                <div class="pf-option-content">
                                    <span class="pf-option-label">Mensual</span>
                                    <span class="pf-option-desc">Un pago por mes · 12 períodos al año</span>
                                </div>
                                <div class="pf-check"><i class="bi bi-check-circle-fill"></i></div>
                            </label>
                            <label class="pf-option" data-step="1" data-value="biweekly">
                                <input type="radio" name="pf_frequency" value="biweekly" hidden>
                                <div class="pf-option-icon pf-icon-biweekly"><i class="bi bi-calendar2-range-fill"></i></div>
                                <div class="pf-option-content">
                                    <span class="pf-option-label">Quincenal</span>
                                    <span class="pf-option-desc">Dos pagos por mes · 24 períodos al año</span>
                                </div>
                                <div class="pf-check"><i class="bi bi-check-circle-fill"></i></div>
                            </label>
                        </div>
                    </div>

                    <!-- PASO 2: Método de Pago de Bonificaciones -->
                    <div class="pf-section" id="step2" style="display:none;">
                        <div class="pf-step-title"><i class="bi bi-2-circle-fill me-2"></i>Pago de Bonificaciones de Ley</div>
                        <div class="pf-options">
                            <label class="pf-option" data-step="2" data-value="payroll">
                                <input type="radio" name="pf_bonus_method" value="payroll" hidden>
                                <div class="pf-option-icon"
                                    style="background:linear-gradient(135deg,rgba(16,185,129,0.15),rgba(5,150,105,0.1));color:#10b981;">
                                    <i class="bi bi-cash-stack"></i></div>
                                <div class="pf-option-content">
                                    <span class="pf-option-label">Pagar con la Nómina</span>
                                    <span class="pf-option-desc">Incluir bonificaciones en nómina regular</span>
                                </div>
                                <div class="pf-check"><i class="bi bi-check-circle-fill"></i></div>
                            </label>
                            <label class="pf-option" data-step="2" data-value="separate">
                                <input type="radio" name="pf_bonus_method" value="separate" hidden>
                                <div class="pf-option-icon"
                                    style="background:linear-gradient(135deg,rgba(168,85,247,0.15),rgba(147,51,234,0.1));color:#a855f7;">
                                    <i class="bi bi-credit-card-2-front"></i></div>
                                <div class="pf-option-content">
                                    <span class="pf-option-label">Pago Separado</span>
                                    <span class="pf-option-desc">Procesar bonificaciones aparte de la nómina</span>
                                </div>
                                <div class="pf-check"><i class="bi bi-check-circle-fill"></i></div>
                            </label>
                        </div>
                    </div>

                    <!-- PASO 3: División de Bonificación (solo si frecuencia=quincenal y método=payroll) -->
                    <div class="pf-section" id="step3" style="display:none;">
                        <div class="pf-step-title"><i class="bi bi-3-circle-fill me-2"></i>División de Bonificación (Quincenal)
                        </div>
                        <div class="pf-options">
                            <label class="pf-option" data-step="3" data-value="both">
                                <input type="radio" name="pf_bonus_split" value="both" hidden>
                                <div class="pf-option-icon"
                                    style="background:linear-gradient(135deg,rgba(34,211,238,0.15),rgba(14,165,233,0.1));color:#22d3ee;">
                                    <i class="bi bi-chevron-double-right"></i></div>
                                <div class="pf-option-content">
                                    <span class="pf-option-label">Ambas Quincenas</span>
                                    <span class="pf-option-desc">Dividir bonificación en Q1 y Q2</span>
                                </div>
                                <div class="pf-check"><i class="bi bi-check-circle-fill"></i></div>
                            </label>
                            <label class="pf-option" data-step="3" data-value="q1">
                                <input type="radio" name="pf_bonus_split" value="q1" hidden>
                                <div class="pf-option-icon"
                                    style="background:linear-gradient(135deg,rgba(251,191,36,0.15),rgba(245,158,11,0.1));color:#fbbf24;">
                                    <i class="bi bi-1-square"></i></div>
                                <div class="pf-option-content">
                                    <span class="pf-option-label">Primera Quincena</span>
                                    <span class="pf-option-desc">Pagar bonificación completa en Q1</span>
                                </div>
                                <div class="pf-check"><i class="bi bi-check-circle-fill"></i></div>
                            </label>
                            <label class="pf-option" data-step="3" data-value="q2">
                                <input type="radio" name="pf_bonus_split" value="q2" hidden>
                                <div class="pf-option-icon"
                                    style="background:linear-gradient(135deg,rgba(239,68,68,0.15),rgba(220,38,38,0.1));color:#ef4444;">
                                    <i class="bi bi-2-square"></i></div>
                                <div class="pf-option-content">
                                    <span class="pf-option-label">Segunda Quincena</span>
                                    <span class="pf-option-desc">Pagar bonificación completa en Q2</span>
                                </div>
                                <div class="pf-check"><i class="bi bi-check-circle-fill"></i></div>
                            </label>
                        </div>
                    </div>

                    <div class="pf-warning">
                        <i class="bi bi-info-circle-fill me-2" style="color:#06b6d4;"></i>
                        <span>Esta configuración puede modificarse después desde <strong>Configuración de
                                Empresa</strong>.</span>
                    </div>
                </div>
                <div class="pf-modal-footer">
                    <form method="POST" action="<?php echo e(route('company.payrollFrequency')); ?>" id="pfForm">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="payroll_frequency" id="pfFrequency" value="">
                        <input type="hidden" name="bonus_payment_method" id="pfBonusMethod" value="">
                        <input type="hidden" name="bonus_biweekly_split" id="pfBonusSplit" value="">
                        <button type="submit" class="pf-btn-confirm" id="pfConfirmBtn" disabled>
                            <i class="bi bi-check-lg me-2"></i>Confirmar y Continuar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    
    <?php if($showTodayModal): ?>
        <div class="today-modal-overlay" id="todayModal">
            <div class="today-modal">
                <div class="today-modal-header">
                    <div class="d-flex align-items-center gap-2">
                        <div
                            style="width:38px;height:38px;border-radius:10px;background:var(--gradient-1);display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-calendar-check" style="color:white;font-size:1.1rem;"></i>
                        </div>
                        <div>
                            <h5 style="margin:0;font-weight:700;font-size:1rem;color:white;">Actividades de Hoy</h5>
                            <small
                                style="color:var(--dark-4);font-size:0.7rem;"><?php echo e(now()->translatedFormat('l, d \\d\\e F Y')); ?></small>
                        </div>
                    </div>
                    <button class="today-modal-close" onclick="closeTodayModal()"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="today-modal-body">
                    <?php $__currentLoopData = $todayEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="today-event-card">
                            <div class="today-event-time"><i
                                    class="bi bi-clock me-1"></i><?php echo e(\Carbon\Carbon::parse($event->event_time)->format('h:i A')); ?>

                            </div>
                            <div class="today-event-title"><?php echo e($event->title); ?></div>
                            <?php if($event->description): ?>
                            <div class="today-event-desc"><?php echo e($event->description); ?></div><?php endif; ?>
                            <?php if($event->links->isNotEmpty()): ?>
                                <div class="today-event-links">
                                    <?php $__currentLoopData = $event->links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e($link->url); ?>" target="_blank"><i
                                                class="bi bi-link-45deg"></i><?php echo e($link->label ?: $link->url); ?></a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="today-modal-footer">
                    <a href="<?php echo e(route('calendar.index')); ?>" class="btn btn-primary-custom w-100"><i
                            class="bi bi-calendar-event me-2"></i>Ir al Calendario</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php $__env->startPush('styles'); ?>
        <style>
            .dash-salary-bar {
                position: relative;
                height: 10px;
                background: linear-gradient(90deg, #818cf8, #34d399, #fbbf24);
                border-radius: 5px;
                margin: 0.5rem 0;
            }

            .dash-salary-avg {
                position: absolute;
                top: -4px;
                width: 4px;
                height: 18px;
                background: white;
                border-radius: 2px;
                transform: translateX(-50%);
                box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
            }

            .dash-pipeline-item {
                padding: 1rem;
                background: rgba(255, 255, 255, 0.02);
                border: 1px solid rgba(255, 255, 255, 0.06);
                border-radius: 12px;
                margin-bottom: 0.6rem;
                transition: all 0.2s;
            }

            .dash-pipeline-item:hover {
                background: rgba(99, 102, 241, 0.04);
                border-color: rgba(99, 102, 241, 0.15);
            }

            .dash-pipeline-bar {
                display: flex;
                height: 6px;
                border-radius: 3px;
                overflow: hidden;
                gap: 1px;
            }

            .dash-pipeline-bar>div {
                transition: width 0.6s ease;
            }

            .today-modal-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.65);
                backdrop-filter: blur(6px);
                z-index: 3000;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: todayOverlayIn 0.3s ease;
            }

            @keyframes todayOverlayIn {
                from {
                    opacity: 0
                }

                to {
                    opacity: 1
                }
            }

            .today-modal {
                background: var(--dark-2);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 20px;
                width: 100%;
                max-width: 460px;
                max-height: 75vh;
                display: flex;
                flex-direction: column;
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
                animation: todayModalIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            }

            @keyframes todayModalIn {
                from {
                    opacity: 0;
                    transform: scale(0.9) translateY(20px)
                }

                to {
                    opacity: 1;
                    transform: scale(1) translateY(0)
                }
            }

            .today-modal-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 1.25rem 1.5rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            }

            .today-modal-close {
                background: rgba(255, 255, 255, 0.05);
                border: none;
                color: #94a3b8;
                font-size: 1.1rem;
                cursor: pointer;
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s;
            }

            .today-modal-close:hover {
                color: white;
                background: rgba(239, 68, 68, 0.15);
            }

            .today-modal-body {
                padding: 1rem 1.5rem;
                overflow-y: auto;
                flex: 1;
            }

            .today-event-card {
                padding: 0.9rem 1rem;
                background: rgba(255, 255, 255, 0.02);
                border: 1px solid rgba(255, 255, 255, 0.06);
                border-radius: 12px;
                margin-bottom: 0.6rem;
                border-left: 3px solid var(--primary);
            }

            .today-event-time {
                font-size: 0.72rem;
                color: var(--primary-light);
                font-weight: 600;
                margin-bottom: 0.25rem;
            }

            .today-event-title {
                font-weight: 600;
                font-size: 0.9rem;
                color: white;
                margin-bottom: 0.2rem;
            }

            .today-event-desc {
                font-size: 0.78rem;
                color: #94a3b8;
                line-height: 1.5;
            }

            .today-event-links {
                margin-top: 0.45rem;
                display: flex;
                flex-wrap: wrap;
                gap: 0.35rem;
            }

            .today-event-links a {
                font-size: 0.68rem;
                padding: 2px 7px;
                border-radius: 6px;
                background: rgba(6, 182, 212, 0.1);
                color: #67e8f9;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 3px;
                transition: all 0.15s;
            }

            .today-event-links a:hover {
                background: rgba(6, 182, 212, 0.22);
            }

            .today-modal-footer {
                padding: 1rem 1.5rem;
                border-top: 1px solid rgba(255, 255, 255, 0.06);
            }

            .badge-hired {
                background: rgba(16, 185, 129, 0.15);
                color: #34d399;
            }

            .badge-discarded {
                background: rgba(239, 68, 68, 0.15);
                color: #f87171;
            }

            /* ── Payroll Configuration Modal ──────────────────────────── */
            .pf-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.82);
                backdrop-filter: blur(10px);
                z-index: 4000;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: pfOverlayIn 0.4s ease;
            }

            @keyframes pfOverlayIn {
                from {
                    opacity: 0
                }

                to {
                    opacity: 1
                }
            }

            .pf-modal {
                background: var(--dark-2);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 24px;
                width: 100%;
                max-width: 580px;
                max-height: 90vh;
                display: flex;
                flex-direction: column;
                box-shadow: 0 30px 80px rgba(0, 0, 0, 0.6);
                animation: pfModalIn 0.45s cubic-bezier(0.16, 1, 0.3, 1);
                overflow: hidden;
                transition: transform 0.15s ease;
            }

            @keyframes pfModalIn {
                from {
                    opacity: 0;
                    transform: scale(0.88) translateY(30px)
                }

                to {
                    opacity: 1;
                    transform: scale(1) translateY(0)
                }
            }

            .pf-modal-header {
                background: linear-gradient(135deg, rgba(99, 102, 241, 0.18), rgba(139, 92, 246, 0.12));
                border-bottom: 1px solid rgba(255, 255, 255, 0.07);
                padding: 1.5rem 1.75rem;
                flex-shrink: 0;
            }

            .pf-icon-wrap {
                width: 52px;
                height: 52px;
                border-radius: 14px;
                background: linear-gradient(135deg, #6366f1, #8b5cf6);
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
                flex-shrink: 0;
            }

            .pf-title {
                margin: 0;
                font-weight: 700;
                font-size: 1.1rem;
                color: white;
            }

            .pf-subtitle {
                margin: 0;
                font-size: 0.72rem;
                color: #94a3b8;
                margin-top: 2px;
            }

            .pf-modal-body {
                padding: 1.5rem 1.75rem;
                overflow-y: auto;
                flex: 1;
            }

            .pf-desc {
                font-size: 0.85rem;
                color: #94a3b8;
                line-height: 1.6;
                margin-bottom: 1.5rem;
            }

            .pf-section {
                margin-bottom: 1.5rem;
            }

            .pf-section:last-of-type {
                margin-bottom: 0;
            }

            .pf-step-title {
                font-size: 0.8rem;
                font-weight: 700;
                color: #818cf8;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-bottom: 0.75rem;
                display: flex;
                align-items: center;
            }

            .pf-options {
                display: flex;
                flex-direction: column;
                gap: 0.65rem;
            }

            .pf-option {
                display: flex;
                align-items: center;
                gap: 0.9rem;
                padding: 0.95rem 1.1rem;
                background: rgba(255, 255, 255, 0.03);
                border: 2px solid rgba(255, 255, 255, 0.07);
                border-radius: 12px;
                cursor: pointer;
                transition: all 0.25s;
                position: relative;
            }

            .pf-option:hover {
                background: rgba(99, 102, 241, 0.06);
                border-color: rgba(99, 102, 241, 0.25);
            }

            .pf-option.selected {
                background: rgba(99, 102, 241, 0.10);
                border-color: #6366f1;
                box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.3);
            }

            .pf-option-icon {
                width: 40px;
                height: 40px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                flex-shrink: 0;
            }

            .pf-icon-monthly {
                background: linear-gradient(135deg, rgba(34, 211, 238, 0.15), rgba(59, 130, 246, 0.1));
                color: #22d3ee;
            }

            .pf-icon-biweekly {
                background: linear-gradient(135deg, rgba(251, 191, 36, 0.15), rgba(249, 115, 22, 0.1));
                color: #fbbf24;
            }

            .pf-option-content {
                flex: 1;
            }

            .pf-option-label {
                display: block;
                font-weight: 700;
                font-size: 0.88rem;
                color: white;
                margin-bottom: 2px;
            }

            .pf-option-desc {
                font-size: 0.72rem;
                color: #64748b;
                line-height: 1.4;
            }

            .pf-check {
                color: #6366f1;
                font-size: 1.1rem;
                opacity: 0;
                transition: opacity 0.2s;
                flex-shrink: 0;
            }

            .pf-option.selected .pf-check {
                opacity: 1;
            }

            .pf-warning {
                background: rgba(6, 182, 212, 0.07);
                border: 1px solid rgba(6, 182, 212, 0.2);
                border-radius: 10px;
                padding: 0.7rem 0.95rem;
                font-size: 0.74rem;
                color: #94a3b8;
                line-height: 1.5;
                margin-top: 1rem;
            }

            .pf-modal-footer {
                padding: 1.25rem 1.75rem;
                border-top: 1px solid rgba(255, 255, 255, 0.06);
                flex-shrink: 0;
            }

            .pf-btn-confirm {
                width: 100%;
                padding: 0.8rem 1.5rem;
                background: linear-gradient(135deg, #6366f1, #8b5cf6);
                border: none;
                border-radius: 12px;
                color: white;
                font-weight: 700;
                font-size: 0.9rem;
                cursor: not-allowed;
                transition: all 0.2s;
                opacity: 0.4;
            }

            .pf-btn-confirm.ready {
                opacity: 1;
                cursor: pointer;
            }

            .pf-btn-confirm.ready:hover {
                transform: translateY(-1px);
                box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
            }
        </style>
    <?php $__env->stopPush(); ?>

    <?php if($showTodayModal): ?>
        <?php $__env->startPush('scripts'); ?>
            <script>
                function closeTodayModal() { const m = document.getElementById('todayModal'); m.style.opacity = '0'; m.style.transition = 'opacity 0.25s ease'; setTimeout(() => m.remove(), 250); }
                document.getElementById('todayModal').addEventListener('click', function (e) { if (e.target === this) closeTodayModal(); });
                document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeTodayModal(); });
            </script>
        <?php $__env->stopPush(); ?>
    <?php endif; ?>

    <?php if($showPayrollFrequencyModal): ?>
        <?php $__env->startPush('scripts'); ?>
            <script>
                (function () {
                    const step1 = document.getElementById('step1');
                    const step2 = document.getElementById('step2');
                    const step3 = document.getElementById('step3');
                    const confirmBtn = document.getElementById('pfConfirmBtn');
                    const pfFrequency = document.getElementById('pfFrequency');
                    const pfBonusMethod = document.getElementById('pfBonusMethod');
                    const pfBonusSplit = document.getElementById('pfBonusSplit');

                    let selectedFrequency = null;
                    let selectedBonusMethod = null;
                    let selectedBonusSplit = null;

                    // Manejar selección de opciones
                    document.querySelectorAll('.pf-option').forEach(option => {
                        option.addEventListener('click', function () {
                            const step = this.dataset.step;
                            const value = this.dataset.value;
                            const radio = this.querySelector('input[type=radio]');

                            // Deseleccionar otras opciones del mismo paso
                            document.querySelectorAll(`.pf-option[data-step="${step}"]`).forEach(opt => {
                                opt.classList.remove('selected');
                            });

                            // Seleccionar esta opción
                            this.classList.add('selected');
                            radio.checked = true;

                            // Guardar selección según el paso
                            if (step === '1') {
                                selectedFrequency = value;
                                pfFrequency.value = value;
                                // Mostrar paso 2
                                step2.style.display = 'block';
                                // Ocultar paso 3 y resetear selección
                                step3.style.display = 'none';
                                selectedBonusSplit = null;
                                pfBonusSplit.value = '';
                                document.querySelectorAll('.pf-option[data-step="3"]').forEach(opt => {
                                    opt.classList.remove('selected');
                                });
                            } else if (step === '2') {
                                selectedBonusMethod = value;
                                pfBonusMethod.value = value;

                                // Si es quincenal + pagar con nómina, mostrar paso 3
                                if (selectedFrequency === 'biweekly' && value === 'payroll') {
                                    step3.style.display = 'block';
                                } else {
                                    step3.style.display = 'none';
                                    selectedBonusSplit = null;
                                    pfBonusSplit.value = '';
                                    // Si no necesita paso 3, habilitar botón
                                    enableButton();
                                }
                            } else if (step === '3') {
                                selectedBonusSplit = value;
                                pfBonusSplit.value = value;
                                enableButton();
                            }

                            checkCompletion();
                        });
                    });

                    function checkCompletion() {
                        // Verificar si todos los pasos necesarios están completos
                        const step3Visible = step3.style.display !== 'none';

                        if (selectedFrequency && selectedBonusMethod) {
                            if (step3Visible && !selectedBonusSplit) {
                                // Paso 3 visible pero no seleccionado
                                disableButton();
                            } else {
                                // Todo completo
                                enableButton();
                            }
                        } else {
                            disableButton();
                        }
                    }

                    function enableButton() {
                        confirmBtn.removeAttribute('disabled');
                        confirmBtn.classList.add('ready');
                    }

                    function disableButton() {
                        confirmBtn.setAttribute('disabled', 'disabled');
                        confirmBtn.classList.remove('ready');
                    }

                    // Bloquear cierre al hacer clic en el overlay con efecto shake
                    document.getElementById('payrollFreqModal').addEventListener('click', function (e) {
                        if (e.target === this) {
                            const modal = this.querySelector('.pf-modal');
                            modal.style.transform = 'scale(1.025)';
                            setTimeout(() => { modal.style.transform = ''; }, 160);
                        }
                    });

                    // Bloquear tecla Escape
                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape' && document.getElementById('payrollFreqModal')) {
                            e.preventDefault();
                        }
                    });
                })();
            </script>
        <?php $__env->stopPush(); ?>
    <?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/dashboard/index.blade.php ENDPATH**/ ?>