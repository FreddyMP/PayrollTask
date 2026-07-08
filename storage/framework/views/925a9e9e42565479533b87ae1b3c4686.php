<?php $__env->startSection('title', 'Gestionar Vacante'); ?>
<?php $__env->startSection('page-title', $vacancy->title); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .step-card {
            border-left: 4px solid var(--primary);
            transition: all 0.2s;
        }

        .step-card:hover {
            transform: translateX(5px);
        }

        .timeline-item {
            position: relative;
            padding-left: 30px;
            padding-bottom: 20px;
            border-left: 1px solid var(--dark-3);
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--dark-3);
        }

        .timeline-item.completed::before {
            background: var(--success);
        }

        .timeline-item.active::before {
            background: var(--primary);
        }

        .timeline-item.discarded::before {
            background: var(--danger);
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php if($vacancy->status === 'closed'): ?>
        <div class="alert alert-warning mb-4">
            <i class="bi bi-lock-fill me-2"></i>Esta vacante se encuentra <strong>Cerrada</strong>. No se pueden realizar más
            cambios en el proceso.
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Left Column: Steps and Config -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="text-white">Pasos del Proceso</span>
                    <?php if($vacancy->status === 'open'): ?>
                        <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#addStepModal">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush bg-transparent">
                        <?php $__empty_1 = true; $__currentLoopData = $vacancy->steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="list-group-item bg-transparent border-0 ps-0 mb-3 step-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white mb-1"><?php echo e($loop->iteration); ?>. <?php echo e($step->name); ?></h6>
                                        <p class="small text-white mb-0">Resp: <?php echo e($step->responsible->name); ?></p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge badge-status badge-primary mb-1 d-block"><?php echo e($step->points); ?>

                                            pts</span>
                                        <?php if($vacancy->status === 'open'): ?>
                                            <button class="btn btn-sm btn-link text-warning p-0 text-decoration-none"
                                                onclick="editStep(<?php echo e($step->id); ?>, '<?php echo e(addslashes($step->name)); ?>', <?php echo e($step->responsible_id); ?>, <?php echo e($step->points); ?>)">
                                                <i class="bi bi-pencil-square"></i> Editar
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-white text-center py-3">Define los pasos para esta vacante.</p>
                        <?php endif; ?>
                    </div>

                    <?php if($vacancy->steps->count() > 0): ?>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between small text-white mb-2">
                                <span>Puntuación Total</span>
                                <span><?php echo e($vacancy->steps->sum('points')); ?> / 100</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: <?php echo e($vacancy->steps->sum('points')); ?>%"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <span class="text-white">Información General</span>
                </div>
                <div class="card-body">
                    <p class="text-white small mb-3"><?php echo e($vacancy->description); ?></p>
                    <div class="d-grid">
                        <a href="<?php echo e(route('recruitment.ranking', $vacancy)); ?>" class="btn btn-outline-custom">
                            <i class="bi bi-trophy-fill me-2 text-warning"></i>Ver Ranking
                        </a>
                    </div>
                </div>
            </div>

            <?php if($vacancy->selected_candidate_id): ?>
                <div class="card" style="border: 2px solid #4510d6  ;">
                    <div class="card-header  py-3" style="background-color: #4510d6;">
                        <span class="text-white fw-bold">Seleccionado para Contrato</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
                                <i class="bi bi-person-check-fill text-success fs-4"></i>
                            </div>
                            <div>
                                <h6 class="text-white mb-0"><?php echo e($vacancy->selectedCandidate->name); ?></h6>
                                <small class="text-white"><?php echo e($vacancy->selectedCandidate->email); ?></small>
                            </div>
                        </div>
                        <div class="d-grid">
                            <form action="<?php echo e(route('recruitment.vacancies.close', $vacancy)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-success w-100"
                                    onclick="return confirm('¿Seguro que desea cerrar esta vacante? No podrá gestionarla más.')">
                                    <i class="bi bi-door-closed-fill me-2"></i>Cerrar Vacante
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Candidates -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-white mb-0">Candidatos Postulados</h5>
                    <?php if($vacancy->status === 'open'): ?>
                        <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addCandidateModal">
                            <i class="bi bi-person-plus-fill me-2"></i>Agregar Candidato
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Candidato</th>
                                    <th>CV</th>
                                    <th>Paso Actual</th>
                                    <th>Puntos</th>
                                    <th class="text-end">Gestión</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $vacancy->candidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="<?php echo e($candidate->status === 'discarded' ? 'opacity-50' : ''); ?>">
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo e($candidate->name); ?></div>
                                            <small class="text-dark"><?php echo e($candidate->email); ?></small>
                                        </td>
                                        <td>
                                            <?php if($candidate->cv_path): ?>
                                                <a href="<?php echo e(Storage::url($candidate->cv_path)); ?>" target="_blank"
                                                    class="text-primary-light">
                                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($candidate->status === 'discarded'): ?>
                                                <span class="badge badge-status badge-rejected">Descartado</span>
                                            <?php else: ?>
                                                <span class="text-primary-light small fw-bold d-block">
                                                    <?php echo e($candidate->current_step->name ?? 'Completado'); ?>

                                                </span>
                                                <?php if($candidate->current_step): ?>
                                                    <?php 
                                                        $currentProg = $candidate->progress->firstWhere('recruitment_step_id', $candidate->current_step->id);
                                                    ?>
                                                    <?php if($currentProg && $currentProg->scheduled_at): ?>
                                                        <div class="text-warning mt-1" style="font-size: 0.75rem;">
                                                            <i class="bi bi-calendar-event"></i> <?php echo e(\Carbon\Carbon::parse($currentProg->scheduled_at)->format('d/m/Y')); ?>

                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-status badge-review"><?php echo e($candidate->total_points); ?>

                                                pts</span>
                                        </td>
                                        <td class="text-end">
                                            <?php if($vacancy->status === 'open'): ?>
                                                <button class="btn btn-outline-custom btn-sm"
                                                    onclick="showProgressModal(<?php echo e($candidate->toJson()); ?>, <?php echo e($candidate->current_step ? $candidate->current_step->toJson() : 'null'); ?>)">
                                                    <i class="bi bi-gear"></i> Procesar
                                                </button>
                                            <?php else: ?>
                                                <span class="text-white small">Finalizado</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-dark">No hay candidatos registrados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Step Modal -->
    <div class="modal fade" id="addStepModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="<?php echo e(route('recruitment.steps.store', $vacancy)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title text-white">Agregar Paso de Reclutamiento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del paso</label>
                            <input type="text" name="name" class="form-control" placeholder="Ej: Entrevista RRHH" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Responsable</label>
                            <select name="responsible_id" class="form-select" required>
                                <option value="">Seleccione un usuario...</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?> (<?php echo e($user->role); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Puntos que vale este paso (Máx 100 )</label>
                            <input type="number" name="points" class="form-control" min="1" max="100" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">Agregar Paso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Step Modal -->
    <div class="modal fade" id="editStepModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="editStepForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <div class="modal-header">
                        <h5 class="modal-title text-white">Editar Paso de Reclutamiento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre del paso</label>
                            <input type="text" name="name" id="editStepName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Responsable</label>
                            <select name="responsible_id" id="editStepResponsible" class="form-select" required>
                                <option value="">Seleccione un usuario...</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?> (<?php echo e($user->role); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Puntos que vale este paso (Máx 100)</label>
                            <input type="number" name="points" id="editStepPoints" class="form-control" min="1" max="100"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Candidate Modal -->
    <div class="modal fade" id="addCandidateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="<?php echo e(route('recruitment.candidates.store', $vacancy)); ?>" method="POST"
                    enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title text-white">Nuevo Candidato</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha del Primer Paso (Opcional)</label>
                            <input type="date" name="first_step_date" class="form-control">
                            <small class="text-secondary">Fecha programada para el primer paso del proceso.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">CV (PDF/Doc)</label>
                            <input type="file" name="cv" class="form-control" accept=".pdf,.doc,.docx" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Progress Modal -->
    <div class="modal fade" id="progressModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="progressModalTitle">Línea de Tiempo: Candidato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Timeline Section -->
                        <div class="col-md-6 border-end border-dark-3">
                            <h6 class="text-white mb-4">Historial de Pasos</h6>
                            <div id="candidateTimeline">
                                <!-- JS will populate this -->
                            </div>
                        </div>
                        <!-- Action Section -->
                        <div class="col-md-6">
                            <div id="currentActionContainer">
                                <h6 class="text-white mb-3">Acción del Paso Actual</h6>
                                <form id="progressForm" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="recruitment_step_id" id="formStepId">
                                    <div class="mb-3">
                                        <label class="form-label">Puntuación Obtenida (Máx <span
                                                id="maxScoreLabel"></span>)</label>
                                        <input type="number" name="score" id="scoreInput" class="form-control" min="0">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Notas / Feedback</label>
                                        <textarea name="notes" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Resultado</label>
                                        <select name="status" id="statusSelect" class="form-select" required>
                                            <option value="completed">Aprobar Paso</option>
                                            <option value="discarded">Descartar Candidato</option>
                                        </select>
                                    </div>
                                    <div class="mb-3" id="nextStepDateContainer">
                                        <label class="form-label" id="nextStepDateLabel">Programar Siguiente Paso
                                            (Opcional)</label>
                                        <input type="date" name="next_step_date" id="nextStepDateInput"
                                            class="form-control">
                                        <small class="text-secondary" id="nextStepDateHelp">Fecha para el paso: <span
                                                id="nextStepNameSpan"></span></small>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary-custom">Guardar Progreso</button>
                                    </div>
                                </form>
                            </div>
                            <div id="candidateDiscardedMessage" class="alert alert-danger d-none">
                                Este candidato ha sido descartado.
                            </div>
                            <div id="allStepsCompletedMessage" class="alert alert-success d-none">
                                Proceso completado para este candidato.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function editStep(id, name, responsibleId, points) {
            $('#editStepForm').attr('action', `/recruitment/steps/${id}`);
            $('#editStepName').val(name);
            $('#editStepResponsible').val(responsibleId);
            $('#editStepPoints').val(points);
            new bootstrap.Modal(document.getElementById('editStepModal')).show();
        }

        function showProgressModal(candidate, currentStep) {
            $('#progressModalTitle').text('Progreso: ' + candidate.name);
            $('#formStepId').val(currentStep ? currentStep.id : '');
            $('#maxScoreLabel').text(currentStep ? currentStep.points : '');
            $('#scoreInput').attr('max', currentStep ? currentStep.points : '');
            $('#progressForm').attr('action', `/recruitment/candidates/${candidate.id}/progress`);

            const timelineContainer = $('#candidateTimeline');
            timelineContainer.empty();

            // Populate timeline via progress data
            const progress = candidate.progress || [];
            const steps = <?php echo json_encode($vacancy->steps, 15, 512) ?>;

            steps.forEach(step => {
                const stepProgress = progress.find(p => p.recruitment_step_id === step.id);
                let statusClass = '';
                let badgeText = 'Pendiente';

                if (stepProgress) {
                    if (stepProgress.status === 'completed') {
                        statusClass = 'completed';
                        badgeText = stepProgress.score + ' pts';
                    } else if (stepProgress.status === 'discarded') {
                        statusClass = 'discarded';
                        badgeText = 'Descartado';
                    } else {
                        statusClass = 'active';
                        badgeText = 'En curso';
                    }
                }

                // Show scheduled date if exists
                let scheduledText = '';
                if (stepProgress && stepProgress.scheduled_at) {
                    const dateObj = new Date(stepProgress.scheduled_at + 'T00:00:00');
                    scheduledText = `<div class="text-warning mt-1" style="font-size: 0.7rem;"><i class="bi bi-calendar-event"></i> Prog: ${dateObj.toLocaleDateString()}</div>`;
                }

                timelineContainer.append(`
                        <div class="timeline-item ${statusClass}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="text-white small fw-bold">${step.name}</div>
                                    <div class="text-white" style="font-size: 0.7rem;">${step.responsible ? step.responsible.name : ''}</div>
                                    ${scheduledText}
                                </div>
                                <span class="badge badge-status badge-${statusClass || 'pending'}">${badgeText}</span>
                            </div>
                        </div>
                    `);
            });

            // Handle next step logic
            if (currentStep) {
                const currentStepIndex = steps.findIndex(s => s.id === currentStep.id);
                const nextStep = steps[currentStepIndex + 1];

                if (nextStep) {
                    $('#nextStepDateContainer').show();
                    $('#nextStepNameSpan').text(nextStep.name);

                    // Check if next step already has a progress record with a date
                    const nextStepProgress = progress.find(p => p.recruitment_step_id === nextStep.id);
                    if (nextStepProgress && nextStepProgress.scheduled_at) {
                        $('#nextStepDateLabel').text('Editar Fecha Siguiente Paso (' + nextStep.name + ')');
                        $('#nextStepDateInput').val(nextStepProgress.scheduled_at.split('T')[0]);
                    } else {
                        $('#nextStepDateLabel').text('Programar Siguiente Paso (' + nextStep.name + ')');
                        $('#nextStepDateInput').val('');
                    }
                } else {
                    $('#nextStepDateContainer').hide();
                }
            }

            // Handle status select change to hide next step date if discarded
            $('#statusSelect').off('change').on('change', function () {
                if ($(this).val() === 'discarded') {
                    $('#nextStepDateContainer').hide();
                } else {
                    if (currentStep && steps.findIndex(s => s.id === currentStep.id) + 1 < steps.length) {
                        $('#nextStepDateContainer').show();
                    }
                }
            });

            // Trigger change to set initial state
            $('#statusSelect').trigger('change');

            // Handle form visibility
            if (candidate.status === 'discarded') {
                $('#currentActionContainer').addClass('d-none');
                $('#candidateDiscardedMessage').removeClass('d-none');
                $('#allStepsCompletedMessage').addClass('d-none');
            } else if (!currentStep) {
                $('#currentActionContainer').addClass('d-none');
                $('#candidateDiscardedMessage').addClass('d-none');
                $('#allStepsCompletedMessage').removeClass('d-none');
            } else {
                $('#currentActionContainer').removeClass('d-none');
                $('#candidateDiscardedMessage').addClass('d-none');
                $('#allStepsCompletedMessage').addClass('d-none');
            }

            const modal = new bootstrap.Modal(document.getElementById('progressModal'));
            modal.show();
        }
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/recruitment/show.blade.php ENDPATH**/ ?>