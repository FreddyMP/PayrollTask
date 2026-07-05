<?php $__env->startSection('title', $regulation->title); ?>
<?php $__env->startSection('page-title', 'Reglamento'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="<?php echo e(route('regulations.index')); ?>" class="btn btn-outline-custom btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Volver a Reglamentos
            </a>
            <div class="d-flex gap-2">
                <a href="<?php echo e($fileUrl); ?>"
                   class="btn btn-primary-custom btn-sm"
                   download="<?php echo e($regulation->title); ?>.<?php echo e($regulation->file_type); ?>"
                   target="_blank">
                    <i class="bi bi-download me-2"></i>Descargar <?php echo e(strtoupper($regulation->file_type)); ?>

                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <!-- Header Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <div class="stat-icon bg-primary-light p-3 rounded-3 me-3">
                        <i class="bi bi-file-earmark-text-fill text-white fs-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="text-white mb-2"><?php echo e($regulation->title); ?></h4>
                        <?php if($regulation->description): ?>
                        <p class="text-muted mb-3"><?php echo e($regulation->description); ?></p>
                        <?php endif; ?>
                        <div class="d-flex gap-4 flex-wrap">
                            <span class="text-muted small">
                                <i class="bi bi-person-circle me-1"></i>
                                Publicado por: <strong class="text-white"><?php echo e($regulation->creator->name); ?></strong>
                            </span>
                            <span class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i>
                                Fecha: <strong class="text-white"><?php echo e($regulation->created_at->format('d/m/Y')); ?></strong>
                            </span>
                            <span class="text-muted small">
                                <i class="bi bi-clock-history me-1"></i>
                                Actualizado: <strong class="text-white"><?php echo e($regulation->updated_at->diffForHumans()); ?></strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Display Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-transparent border-bottom border-secondary">
                <h6 class="text-white mb-0">
                    <i class="bi bi-file-earmark-text me-2"></i>Contenido del Documento
                </h6>
            </div>
            <div class="card-body">
                <?php if($regulation->file_type === 'pdf'): ?>
                    <!-- PDF Viewer -->
                    <div class="pdf-viewer-container mb-3">
                        <div class="alert alert-info border-0 mb-3">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Para una mejor experiencia, descarga el documento o ábrelo en una nueva pestaña.
                        </div>
                        <div class="ratio ratio-16x9" style="min-height: 600px;">
                            <iframe
                                src="<?php echo e($fileUrl); ?>"
                                type="application/pdf"
                                class="border-0 rounded-3">
                            </iframe>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="<?php echo e($fileUrl); ?>"
                           target="_blank"
                           class="btn btn-outline-custom btn-sm">
                            <i class="bi bi-box-arrow-up-right me-2"></i>Abrir en Nueva Pestaña
                        </a>
                        <a href="<?php echo e($fileUrl); ?>"
                           download="<?php echo e($regulation->title); ?>.pdf"
                           class="btn btn-primary-custom btn-sm">
                            <i class="bi bi-download me-2"></i>Descargar PDF
                        </a>
                    </div>

                <?php elseif($regulation->file_type === 'txt' && $regulation->content): ?>
                    <!-- Plain Text Content -->
                    <div class="document-content bg-dark-3 p-4 rounded-3">
                        <pre class="text-white mb-0" style="white-space: pre-wrap; font-family: 'Inter', sans-serif; font-size: 0.9rem; line-height: 1.6;"><?php echo e($regulation->content); ?></pre>
                    </div>

                <?php elseif(in_array($regulation->file_type, ['doc', 'docx'])): ?>
                    <!-- Word Document -->
                    <div class="alert alert-warning border-0 mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Documento Word:</strong> Para visualizar este documento correctamente, descárgalo usando el botón de arriba.
                    </div>
                    <div class="text-center py-5">
                        <i class="bi bi-file-word display-1 text-primary mb-3"></i>
                        <h5 class="text-white mb-3">Archivo Microsoft Word</h5>
                        <p class="text-muted mb-4">Este documento requiere Microsoft Word o un lector compatible.</p>
                        <a href="<?php echo e($fileUrl); ?>"
                           download="<?php echo e($regulation->title); ?>.<?php echo e($regulation->file_type); ?>"
                           class="btn btn-primary-custom">
                            <i class="bi bi-download me-2"></i>Descargar Documento
                        </a>
                    </div>

                <?php else: ?>
                    <!-- Generic File -->
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark display-1 text-muted mb-3"></i>
                        <h5 class="text-white mb-3">Vista previa no disponible</h5>
                        <p class="text-muted mb-4">Descarga el documento para visualizarlo.</p>
                        <a href="<?php echo e($fileUrl); ?>"
                           download="<?php echo e($regulation->title); ?>.<?php echo e($regulation->file_type); ?>"
                           class="btn btn-primary-custom">
                            <i class="bi bi-download me-2"></i>Descargar Archivo
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Footer -->
        <div class="alert alert-info border-0">
            <i class="bi bi-shield-check me-2"></i>
            <strong>Documento Oficial:</strong> Este reglamento es parte de las políticas oficiales de <?php echo e(auth()->user()->company->name ?? 'la empresa'); ?>.
            Su contenido debe ser conocido y respetado por todos los colaboradores.
        </div>
    </div>
</div>

<style>
.stat-icon {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
}

.document-content {
    max-height: 800px;
    overflow-y: auto;
}

.document-content::-webkit-scrollbar {
    width: 8px;
}

.document-content::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 4px;
}

.document-content::-webkit-scrollbar-thumb {
    background: rgba(99, 102, 241, 0.5);
    border-radius: 4px;
}

.document-content::-webkit-scrollbar-thumb:hover {
    background: rgba(99, 102, 241, 0.7);
}

iframe {
    background: white;
}

.pdf-viewer-container iframe {
    border: 2px solid rgba(255, 255, 255, 0.1);
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/regulations/show.blade.php ENDPATH**/ ?>