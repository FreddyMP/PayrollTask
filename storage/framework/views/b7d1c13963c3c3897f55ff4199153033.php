<?php $__env->startSection('title', 'Reglamentos Internos'); ?>
<?php $__env->startSection('page-title', 'Reglamentos Internos'); ?>

<?php $__env->startSection('content'); ?>
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="text-white mb-1">Reglamentos de la Empresa</h5>
                <p class="small text-white mb-0">Documentos oficiales y políticas internas de
                    <?php echo e(auth()->user()->company->name ?? 'la empresa'); ?>

                </p>
            </div>
            <?php if($canManage): ?>
                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createRegulationModal">
                    <i class="bi bi-file-earmark-plus me-2"></i>Nuevo Reglamento
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $regulations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $regulation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-0 hover-card">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-start mb-3">
                            <div class="stat-icon bg-primary-light p-3 rounded-3 me-3">
                                <i class="bi bi-file-earmark-text-fill text-white fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="text-white mb-1"><?php echo e($regulation->title); ?></h6>
                                <small class="text-white d-block">
                                    <i class="bi bi-person-circle me-1"></i><?php echo e($regulation->creator->name); ?>

                                </small>
                                <small class="text-white">
                                    <i class="bi bi-calendar3 me-1"></i><?php echo e($regulation->created_at->format('d/m/Y')); ?>

                                </small>
                            </div>
                        </div>

                        <?php if($regulation->description): ?>
                            <p class="text-white small mb-3 flex-grow-1">
                                <?php echo e(Str::limit($regulation->description, 120)); ?>

                            </p>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-secondary">
                            <div class="d-flex gap-2 flex-grow-1">
                                <a href="<?php echo e(route('regulations.show', $regulation)); ?>"
                                    class="btn btn-primary-custom btn-sm flex-grow-1">
                                    <i class="bi bi-eye-fill me-1"></i> Ver Reglamento
                                </a>

                                <?php if($canManage): ?>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-custom btn-sm" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#editRegulationModal<?php echo e($regulation->id); ?>">
                                                    <i class="bi bi-pencil-fill me-2"></i>Editar
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="<?php echo e(route('regulations.destroy', $regulation)); ?>" method="POST">
                                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="dropdown-item text-danger"
                                                        onclick="return confirm('¿Está seguro de eliminar este reglamento?')">
                                                        <i class="bi bi-trash3-fill me-2"></i>Eliminar
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if($canManage): ?>
                <!-- Edit Modal -->
                <div class="modal fade" id="editRegulationModal<?php echo e($regulation->id); ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form action="<?php echo e(route('regulations.update', $regulation)); ?>" method="POST"
                                enctype="multipart/form-data">
                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                <div class="modal-header">
                                    <h5 class="modal-title text-white">Editar Reglamento</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Título del Reglamento</label>
                                        <input type="text" name="title" class="form-control" value="<?php echo e($regulation->title); ?>"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Descripción</label>
                                        <textarea name="description" class="form-control" rows="3"
                                            placeholder="Descripción breve del reglamento"><?php echo e($regulation->description); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Reemplazar Documento (Opcional)</label>
                                        <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.txt">
                                        <small class="form-text text-muted">
                                            Formatos: PDF, DOC, DOCX, TXT (Máx. 50MB). Actual:
                                            <?php echo e(strtoupper($regulation->file_type)); ?>

                                        </small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary-custom">
                                        <i class="bi bi-save me-2"></i>Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12 text-center py-5">
                <div class="empty-state">
                    <i class="bi bi-file-earmark-text display-1 text-muted opacity-25 mb-3"></i>
                    <h5 class="text-white mb-2">No hay reglamentos disponibles</h5>
                    <p class="text-white mb-4">
                        <?php if($canManage): ?>
                            Crea el primer reglamento para tu empresa
                        <?php else: ?>
                            Aún no se han publicado reglamentos
                        <?php endif; ?>
                    </p>
                    <?php if($canManage): ?>
                        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#createRegulationModal">
                            <i class="bi bi-plus-circle me-2"></i>Crear Primer Reglamento
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if($canManage): ?>
        <!-- Create Regulation Modal -->
        <div class="modal fade" id="createRegulationModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form action="<?php echo e(route('regulations.store')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="modal-header">
                            <h5 class="modal-title text-white">Nuevo Reglamento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info border-0 mb-4">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <strong>Información:</strong> Los reglamentos serán visibles para todos los empleados de la
                                empresa.
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Título del Reglamento <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control"
                                    placeholder="Ej: Reglamento Interno de Trabajo" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea name="description" class="form-control" rows="3"
                                    placeholder="Descripción breve del reglamento y su contenido"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Documento <span class="text-danger">*</span></label>
                                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.txt" required>
                                <small class="form-text text-muted">
                                    Formatos permitidos: PDF, DOC, DOCX, TXT (Máximo 50MB)
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="bi bi-upload me-2"></i>Subir Reglamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <style>
        .hover-card {
            transition: all 0.3s ease;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.2) !important;
        }

        .stat-icon {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }

        .empty-state i {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/regulations/index.blade.php ENDPATH**/ ?>