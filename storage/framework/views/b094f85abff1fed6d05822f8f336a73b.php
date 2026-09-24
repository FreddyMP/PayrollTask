<?php $__env->startSection('title', 'Solicitudes'); ?>
<?php $__env->startSection('page-title', 'Solicitudes'); ?>

<?php $__env->startSection('content'); ?>
    <!-- Botón de Filtros y Nueva Solicitud -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-outline-custom" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCard"
            aria-expanded="false">
            <i class="bi bi-funnel me-1"></i> Filtros
            <?php if(request()->hasAny(['employee', 'type', 'status', 'start_date', 'end_date'])): ?>
                <span class="badge bg-primary ms-1">Activos</span>
            <?php endif; ?>
        </button>
        <a href="<?php echo e(route('requests.create')); ?>" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i> Nueva Solicitud
        </a>
    </div>

    <!-- Card de Filtros Colapsable -->
    <div class="collapse <?php echo e(request()->hasAny(['employee', 'type', 'status', 'start_date', 'end_date']) ? 'show' : ''); ?>"
        id="filtersCard">
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('requests.index')); ?>" id="filtersForm">
                    <div class="row g-3">
                        <!-- Búsqueda por Empleado -->
                        <div class="col-md-3">
                            <label for="filterEmployee" class="form-label small text-white opacity-75">
                                <i class="bi bi-person me-1"></i>Empleado
                            </label>
                            <input type="text" class="form-control" id="filterEmployee" name="employee"
                                value="<?php echo e(request('employee')); ?>" placeholder="Nombre del empleado">
                        </div>

                        <!-- Filtro por Tipo -->
                        <div class="col-md-3">
                            <label for="filterType" class="form-label small text-white opacity-75">
                                <i class="bi bi-tag me-1"></i>Tipo
                            </label>
                            <select id="filterType" name="type" class="form-select">
                                <option value="">Todos los tipos</option>
                                <option value="vacation" <?php echo e(request('type') == 'vacation' ? 'selected' : ''); ?>>Vacaciones
                                </option>
                                <option value="permission" <?php echo e(request('type') == 'permission' ? 'selected' : ''); ?>>Permisos
                                </option>
                                <option value="work_letter" <?php echo e(request('type') == 'work_letter' ? 'selected' : ''); ?>>Carta de
                                    Trabajo</option>
                                <option value="overtime" <?php echo e(request('type') == 'overtime' ? 'selected' : ''); ?>>Horas Extra
                                </option>
                            </select>
                        </div>

                        <!-- Filtro por Estado -->
                        <div class="col-md-3">
                            <label for="filterStatus" class="form-label small text-white opacity-75">
                                <i class="bi bi-check-circle me-1"></i>Estado
                            </label>
                            <select id="filterStatus" name="status" class="form-select">
                                <option value="">Todos los estados</option>
                                <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>Pendiente
                                </option>
                                <option value="approved" <?php echo e(request('status') == 'approved' ? 'selected' : ''); ?>>Aprobada
                                </option>
                                <option value="rejected" <?php echo e(request('status') == 'rejected' ? 'selected' : ''); ?>>Rechazada
                                </option>
                            </select>
                        </div>

                        <!-- Filtro por Fecha de Inicio -->
                        <div class="col-md-3">
                            <label for="filterStartDate" class="form-label small text-white opacity-75">
                                <i class="bi bi-calendar-event me-1"></i>Fecha Inicio
                            </label>
                            <input type="date" class="form-control" id="filterStartDate" name="start_date"
                                value="<?php echo e(request('start_date')); ?>">
                        </div>

                        <!-- Filtro por Fecha de Fin -->
                        <div class="col-md-3">
                            <label for="filterEndDate" class="form-label small text-white opacity-75">
                                <i class="bi bi-calendar-check me-1"></i>Fecha Fin
                            </label>
                            <input type="date" class="form-control" id="filterEndDate" name="end_date"
                                value="<?php echo e(request('end_date')); ?>">
                        </div>

                        <!-- Botones de Acción -->
                        <div class="col-md-9 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="bi bi-search me-1"></i>Aplicar Filtros
                            </button>
                            <a href="<?php echo e(route('requests.index')); ?>" class="btn btn-outline-custom">
                                <i class="bi bi-x-circle me-1"></i>Limpiar
                            </a>
                        </div>
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
                            <th>Solicitante</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Fechas / Horas</th>
                            <th>Aprobador</th>
                            <th>Adjuntos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="fw-semibold"><?php echo e($req->user->name ?? '—'); ?></td>
                                <td>
                                    <?php
                                        $typeLabels = [
                                            'vacation' => 'Vacaciones',
                                            'permission' => 'Permiso',
                                            'work_letter' => 'Carta de Trabajo',
                                            'overtime' => 'Horas Extra',
                                        ];
                                    ?>
                                    <span
                                        class="badge-status badge-<?php echo e($req->type); ?>"><?php echo e($typeLabels[$req->type] ?? $req->type); ?></span>
                                </td>
                                <td>
                                    <?php
                                        $statusLabels = ['pending' => 'Pendiente', 'approved' => 'Aprobada', 'rejected' => 'Rechazada'];
                                    ?>
                                    <span
                                        class="badge-status badge-<?php echo e($req->status); ?>"><?php echo e($statusLabels[$req->status] ?? $req->status); ?></span>
                                </td>
                                <td>
                                    <?php if($req->type === 'overtime'): ?>
                                        <?php if($req->overtime_date): ?>
                                            <div class="fw-semibold" style="font-size:0.85rem;">
                                                <?php echo e($req->overtime_date->format('d/m/Y')); ?></div>
                                            <?php if($req->overtime_start && $req->overtime_end): ?>
                                                <div class="text-white" style="font-size:0.78rem;">
                                                    <i class="bi bi-clock me-1"></i>
                                                    <?php echo e(\Carbon\Carbon::parse($req->overtime_start)->format('H:i')); ?>

                                                    —
                                                    <?php echo e(\Carbon\Carbon::parse($req->overtime_end)->format('H:i')); ?>

                                                </div>
                                            <?php endif; ?>
                                            <?php if($req->overtime_hours): ?>
                                                <span class="badge-status badge-overtime" style="font-size:0.68rem;">
                                                    <i class="bi bi-hourglass-split me-1"></i><?php echo e($req->overtime_hours); ?> h
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if($req->start_date): ?>
                                            <?php echo e($req->start_date->format('d/m/Y')); ?>

                                            <?php if($req->end_date): ?>
                                                — <?php echo e($req->end_date->format('d/m/Y')); ?>

                                            <?php endif; ?>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($req->type === 'overtime' && $req->approvedBy): ?>
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                style="width:24px;height:24px;background:rgba(251,146,60,0.2);font-size:0.65rem;font-weight:700;color:#fb923c;flex-shrink:0;">
                                                <?php echo e(strtoupper(substr($req->approvedBy->name, 0, 2))); ?>

                                            </div>
                                            <div>
                                                <div style="font-size:0.8rem;font-weight:600;"><?php echo e($req->approvedBy->name); ?></div>
                                                <div style="font-size:0.68rem;color:#64748b;"><?php echo e(ucfirst($req->approvedBy->role)); ?>

                                                </div>
                                            </div>
                                        </div>
                                    <?php elseif($req->reviewer): ?>
                                        <span style="font-size:0.75rem;color:#64748b;">
                                            <i class="bi bi-person-check me-1"></i><?php echo e($req->reviewer->name); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-white small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($req->attachments->count() > 0): ?>
                                        <button class="btn btn-outline-custom btn-sm view-request-attachments"
                                            data-attachments="<?php echo e($req->attachments->map(fn($a) => ['path' => Storage::url($a->file_path), 'type' => $a->file_type, 'user' => $a->user->name])->toJson()); ?>"
                                            title="Ver Adjuntos">
                                            <i class="bi bi-paperclip"></i> <?php echo e($req->attachments->count()); ?>

                                        </button>
                                    <?php else: ?>
                                        <span class="text-white small">Sin adjuntos</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-outline-custom btn-sm"
                                            onclick="showRequestDetails('<?php echo e(addslashes($req->description)); ?>')"
                                            title="Ver Descripción">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                        <?php if(auth()->user()->isSupervisor() && $req->status === 'pending'): ?>
                                            <button class="btn btn-outline-custom btn-sm" style="color: #34d399;"
                                                onclick="reviewRequest(<?php echo e($req->id); ?>, 'approved')" title="Aprobar">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button class="btn btn-outline-custom btn-sm" style="color: #f87171;"
                                                onclick="reviewRequest(<?php echo e($req->id); ?>, 'rejected')" title="Rechazar">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if($req->reviewer && $req->type !== 'overtime'): ?>
                                            <span style="font-size: 0.7rem; color: #64748b;"
                                                title="Revisado por <?php echo e($req->reviewer->name); ?>">
                                                <i class="bi bi-person-check"></i> <?php echo e($req->reviewer->name); ?>

                                            </span>
                                        <?php endif; ?>
                                        <form method="POST" action="<?php echo e(route('requests.destroy', $req)); ?>"
                                            onsubmit="return confirm('¿Eliminar solicitud?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-link text-danger p-0 ms-1"><i
                                                    class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center text-dark py-4">No hay solicitudes</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3"><?php echo e($requests->links()); ?></div>

    <!-- Attachments Modal -->
    <div class="modal fade" id="requestAttachmentsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Archivos Adjuntos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3" id="requestAttachmentsContent"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="requestDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Descripción de la Solicitud</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="requestDetailContent" class="p-3 rounded bg-dark-2 text-white opacity-75"
                        style="white-space: pre-wrap;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <form id="reviewForm" method="POST" style="display: none;">
        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
        <input type="hidden" name="status" id="reviewStatus">
        <input type="hidden" name="admin_notes" id="reviewNotes">
    </form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function showRequestDetails(content) {
            document.getElementById('requestDetailContent').textContent = content || 'Sin descripción';
            new bootstrap.Modal(document.getElementById('requestDetailModal')).show();
        }

        $('.view-request-attachments').on('click', function () {
            const attachments = $(this).data('attachments');
            const $content = $('#requestAttachmentsContent');
            $content.empty();

            attachments.forEach(att => {
                const itemHtml = `
                <div class="col-md-6">
                    <div class="rounded-3 overflow-hidden border border-light border-opacity-10 position-relative h-100" style="background: rgba(0,0,0,0.2);">
                        ${att.type === 'video'
                        ? `<video src="${att.path}" class="w-100" style="height: 150px; object-fit: cover;" controls></video>`
                        : `<img src="${att.path}" class="w-100" style="height: 150px; object-fit: cover;">`
                    }
                        <div class="p-2 small text-center bg-dark-2">
                            <a href="${att.path}" target="_blank" class="text-white opacity-75 text-decoration-none">Ver pantalla completa</a>
                            <div class="text-white" style="font-size: 0.65rem;">Subido por ${att.user}</div>
                        </div>
                    </div>
                </div>
            `;
                $content.append(itemHtml);
            });

            new bootstrap.Modal(document.getElementById('requestAttachmentsModal')).show();
        });



        function reviewRequest(id, status) {
            var labels = { approved: 'aprobar', rejected: 'rechazar' };
            var notes = prompt('Notas (opcional) para ' + labels[status] + ' esta solicitud:');
            if (notes === null) return;

            var form = document.getElementById('reviewForm');
            form.action = '/requests/' + id + '/review';
            document.getElementById('reviewStatus').value = status;
            document.getElementById('reviewNotes').value = notes;
            form.submit();
        }
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/requests/index.blade.php ENDPATH**/ ?>