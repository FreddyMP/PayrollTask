<?php $__env->startSection('title', 'Completar Evaluación'); ?>
<?php $__env->startSection('page-title', 'Evaluación: ' . $evaluation->title); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <?php if($evaluation->description): ?>
                <div class="alert alert-dark bg-dark-2 border-secondary text-white mb-4">
                    <i class="bi bi-info-circle me-2"></i> <?php echo e($evaluation->description); ?>

                </div>
                <?php endif; ?>

                <form action="<?php echo e(route('evaluations.submit', $evaluation)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <?php $__currentLoopData = $evaluation->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mb-4 pb-4 <?php echo e(!$loop->last ? 'border-bottom border-secondary' : ''); ?>">
                        <label class="form-label text-white fw-bold mb-3" style="font-size: 1.1rem;">
                            <?php echo e($index + 1); ?>. <?php echo e($question->question_text); ?>

                            <?php if($question->is_required): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>

                        <?php if($question->type === 'scale'): ?>
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-white small">1</span>
                                <input type="range" class="form-range flex-grow-1" name="q_<?php echo e($question->id); ?>" min="1" max="10" step="1" id="range_<?php echo e($question->id); ?>" oninput="document.getElementById('val_<?php echo e($question->id); ?>').innerText = this.value" <?php echo e($question->is_required ? 'required' : ''); ?>>
                                <span class="text-white small">10</span>
                                <span class="badge bg-primary fs-6 ms-3" id="val_<?php echo e($question->id); ?>" style="min-width: 40px;">5</span>
                            </div>
                            <script>
                                // Set initial value display
                                document.getElementById('range_<?php echo e($question->id); ?>').value = 5;
                            </script>
                        <?php elseif($question->type === 'boolean'): ?>
                            <div class="d-flex gap-4 mt-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q_<?php echo e($question->id); ?>" id="q_<?php echo e($question->id); ?>_yes" value="1" <?php echo e($question->is_required ? 'required' : ''); ?>>
                                    <label class="form-check-label text-white" for="q_<?php echo e($question->id); ?>_yes">
                                        <i class="bi bi-check-circle text-success me-1"></i>Sí
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="q_<?php echo e($question->id); ?>" id="q_<?php echo e($question->id); ?>_no" value="0">
                                    <label class="form-check-label text-white" for="q_<?php echo e($question->id); ?>_no">
                                        <i class="bi bi-x-circle text-danger me-1"></i>No
                                    </label>
                                </div>
                            </div>
                        <?php elseif($question->type === 'text'): ?>
                            <input type="text" name="q_<?php echo e($question->id); ?>" class="form-control" placeholder="Escribe tu respuesta aquí..." <?php echo e($question->is_required ? 'required' : ''); ?>>
                        <?php elseif($question->type === 'textarea'): ?>
                            <textarea name="q_<?php echo e($question->id); ?>" class="form-control" rows="4" placeholder="Escribe tu respuesta detallada aquí..." <?php echo e($question->is_required ? 'required' : ''); ?>></textarea>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-primary-custom py-3 fs-5 fw-bold">
                            <i class="bi bi-check-circle-fill me-2"></i>Enviar Respuestas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/evaluations/fill.blade.php ENDPATH**/ ?>