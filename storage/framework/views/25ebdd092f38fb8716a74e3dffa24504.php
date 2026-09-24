<?php $__env->startSection('title', 'Incidencias'); ?>
<?php $__env->startSection('page-title', 'Incidencias'); ?>

<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0 text-white">Reporte de Incidencias</h5>
            <p class="text-secondary mb-0" style="font-size: 0.875rem;">Gestiona y visualiza las incidencias reportadas en la plataforma.</p>
        </div>
        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createIncidentModal">
            <i class="bi bi-plus-lg me-1"></i>Reportar Incidencia
        </button>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Descripción</th>
                            <th>Reportado por</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <?php if(auth()->user()->isSupervisor()): ?>
                                <th class="text-end">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $incidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incident): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="fw-semibold"><?php echo e($incident->title); ?></td>
                                <td>
                                    <div><?php echo e(Str::limit($incident->description, 50)); ?></div>
                                    <?php if($incident->attachments->isNotEmpty()): ?>
                                        <div class="mt-2 d-flex flex-wrap gap-1">
                                            <?php $__currentLoopData = $incident->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a href="<?php echo e(\Illuminate\Support\Facades\Storage::disk('s3')->url($attachment->file_path)); ?>" target="_blank" class="btn btn-sm btn-outline-custom py-0 px-2" style="font-size: 0.75rem; border-radius: 20px;">
                                                    <?php if($attachment->file_type === 'video'): ?>
                                                        <i class="bi bi-play-btn-fill text-danger me-1"></i>Video
                                                    <?php else: ?>
                                                        <i class="bi bi-image-fill text-info me-1"></i>Imagen
                                                    <?php endif; ?>
                                                </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($incident->user->name ?? '—'); ?></td>
                                <td>
                                    <?php if($incident->priority === 'high'): ?>
                                        <span class="badge bg-danger">Alta</span>
                                    <?php elseif($incident->priority === 'medium'): ?>
                                        <span class="badge bg-warning text-dark">Media</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark">Baja</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($incident->status === 'resolved'): ?>
                                        <span class="badge bg-success">Resuelta</span>
                                    <?php elseif($incident->status === 'in_progress'): ?>
                                        <span class="badge bg-primary">En Progreso</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($incident->created_at->format('d/m/Y h:i A')); ?></td>
                                
                                <?php if(auth()->user()->isSupervisor()): ?>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-custom" data-bs-toggle="modal" data-bs-target="#updateIncidentModal<?php echo e($incident->id); ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="<?php echo e(route('incidencias.destroy', $incident)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta incidencia?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                <?php elseif($incident->status === 'pending' && $incident->user_id === auth()->id()): ?>
                                    <td class="text-end">
                                        <form action="<?php echo e(route('incidencias.destroy', $incident)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta incidencia?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>

                            
                            <?php if(auth()->user()->isSupervisor()): ?>
                                <div class="modal fade" id="updateIncidentModal<?php echo e($incident->id); ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content" style="background: #1e293b; border: 1px solid #334155;">
                                            <div class="modal-header" style="border-bottom: 1px solid #334155;">
                                                <h5 class="modal-title text-white"><i class="bi bi-pencil me-2"></i>Actualizar Estado</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="<?php echo e(route('incidencias.update', $incident)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label" style="color: #94a3b8;">Estado</label>
                                                        <select class="form-select" name="status" style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                                                            <option value="pending" <?php echo e($incident->status === 'pending' ? 'selected' : ''); ?>>Pendiente</option>
                                                            <option value="in_progress" <?php echo e($incident->status === 'in_progress' ? 'selected' : ''); ?>>En Progreso</option>
                                                            <option value="resolved" <?php echo e($incident->status === 'resolved' ? 'selected' : ''); ?>>Resuelta</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer" style="border-top: 1px solid #334155;">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary-custom">Actualizar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-4">No hay incidencias reportadas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="createIncidentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #1e293b; border: 1px solid #334155;">
                <div class="modal-header" style="border-bottom: 1px solid #334155;">
                    <h5 class="modal-title text-white"><i class="bi bi-plus-circle me-2"></i>Reportar Incidencia</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="<?php echo e(route('incidencias.store')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" style="color: #94a3b8;">Título</label>
                            <input type="text" class="form-control" name="title" required placeholder="Ej: Fallo en el sistema de pagos" style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="color: #94a3b8;">Descripción detallada</label>
                            <textarea class="form-control" name="description" rows="4" required placeholder="Describe la incidencia..." style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="color: #94a3b8;">Prioridad</label>
                            <select class="form-select" name="priority" required style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                                <option value="low">Baja (No afecta el trabajo diario)</option>
                                <option value="medium">Media (Dificulta algunas tareas)</option>
                                <option value="high">Alta (Bloqueo total)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="color: #94a3b8;">Adjuntar Archivos (Imágenes o Videos - Máx. 25MB)</label>
                            <input type="file" name="attachments[]" class="form-control" multiple accept="image/*,video/*" style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                            <small class="text-secondary" style="font-size: 0.75rem;">Puedes seleccionar varias imágenes y videos. Cada archivo debe pesar menos de 25MB.</small>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #334155;">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom"><i class="bi bi-send me-1"></i>Enviar Reporte</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/incidents/index.blade.php ENDPATH**/ ?>