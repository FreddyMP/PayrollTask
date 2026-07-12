<?php $__env->startSection('title', 'Registrar Vacaciones'); ?>
<?php $__env->startSection('page-title', 'Registrar Vacaciones'); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
    <div class="col-12">
        <a href="<?php echo e(route('vacations.index')); ?>" class="btn btn-outline-custom btn-sm">
            <i class="bi bi-arrow-left me-2"></i>Volver a Vacaciones
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent border-bottom border-secondary">
                <h5 class="text-white mb-0">
                    <i class="bi bi-calendar-plus me-2"></i>Nuevo Registro de Vacaciones
                </h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('vacations.store')); ?>" method="POST" id="vacationForm">
                    <?php echo csrf_field(); ?>

                    <div class="mb-4">
                        <label class="form-label">Empleado <span class="text-danger">*</span></label>
                        <select name="employee_id" id="employee_id" class="form-select" required>
                            <option value="">Seleccionar empleado...</option>
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($emp->years_of_service >= 1): ?>
                                    <option value="<?php echo e($emp->id); ?>"
                                            data-entitled="<?php echo e($emp->vacation_days_entitled); ?>"
                                            data-taken="<?php echo e($emp->getVacationDaysTaken()); ?>"
                                            data-remaining="<?php echo e($emp->getVacationDaysRemaining()); ?>"
                                            <?php echo e($selectedEmployee && $selectedEmployee->id == $emp->id ? 'selected' : ''); ?>>
                                        <?php echo e($emp->user->name ?? 'N/A'); ?> -
                                        <?php echo e($emp->vacation_days_entitled); ?> días disponibles
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="form-text text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Solo se muestran empleados con 1 año o más de antigüedad
                        </small>
                    </div>

                    <!-- Employee Info Card -->
                    <div id="employeeInfo" class="alert alert-info border-0 mb-4" style="display: none;">
                        <h6 class="text-dark mb-2">
                            <i class="bi bi-info-circle-fill me-2"></i>Información del Empleado
                        </h6>
                        <div class="row g-2 small">
                            <div class="col-md-4">
                                <strong>Días Correspondientes:</strong>
                                <span id="info-entitled" class="text-dark">-</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Días Tomados (<?php echo e(now()->year); ?>):</strong>
                                <span id="info-taken" class="text-dark">-</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Días Disponibles:</strong>
                                <span id="info-remaining" class="text-dark fw-bold">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                   value="<?php echo e(old('start_date')); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha de Fin <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                   value="<?php echo e(old('end_date')); ?>" required>
                        </div>
                    </div>

                    <!-- Days Calculation -->
                    <div id="daysCalculation" class="alert alert-success border-0 mb-4" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-calendar-check-fill me-2"></i>
                                <strong>Días hábiles a tomar:</strong>
                            </div>
                            <h4 class="mb-0 text-white"><span id="calculated-days">0</span> días</h4>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            No se cuentan sábados ni domingos
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Notas (Opcional)</label>
                        <textarea name="notes" class="form-control" rows="3"
                                  placeholder="Agregar comentarios o detalles adicionales..."><?php echo e(old('notes')); ?></textarea>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="<?php echo e(route('vacations.index')); ?>" class="btn btn-outline-custom">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-save me-2"></i>Registrar Vacaciones
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const employeeSelect = document.getElementById('employee_id');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const employeeInfo = document.getElementById('employeeInfo');
    const daysCalculation = document.getElementById('daysCalculation');

    // Show employee info when selected
    employeeSelect.addEventListener('change', function() {
        if (this.value) {
            const option = this.options[this.selectedIndex];
            const entitled = option.dataset.entitled;
            const taken = option.dataset.taken;
            const remaining = option.dataset.remaining;

            document.getElementById('info-entitled').textContent = entitled + ' días';
            document.getElementById('info-taken').textContent = taken + ' días';
            document.getElementById('info-remaining').textContent = remaining + ' días';

            employeeInfo.style.display = 'block';
        } else {
            employeeInfo.style.display = 'none';
        }
        calculateDays();
    });

    // Calculate days when dates change
    startDateInput.addEventListener('change', calculateDays);
    endDateInput.addEventListener('change', calculateDays);

    function calculateDays() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDate && endDate && startDate <= endDate) {
            // Contamos todos los días del rango (incluyendo sábados y domingos)
            // El servidor se encargará de descontar festivos y días de descanso configurados
            let totalDays = 0;
            let currentDate = new Date(startDate);

            while (currentDate <= endDate) {
                totalDays++;
                currentDate.setDate(currentDate.getDate() + 1);
            }

            document.getElementById('calculated-days').textContent = totalDays;
            daysCalculation.style.display = 'block';

            // Check if exceeds available days
            if (employeeSelect.value) {
                const option = employeeSelect.options[employeeSelect.selectedIndex];
                const remaining = parseInt(option.dataset.remaining);

                if (totalDays > remaining) {
                    daysCalculation.classList.remove('alert-success');
                    daysCalculation.classList.add('alert-danger');
                    daysCalculation.querySelector('small').innerHTML =
                        '<i class="bi bi-exclamation-triangle me-1"></i>¡Atención! Este empleado solo tiene ' +
                        remaining + ' días disponibles';
                } else {
                    daysCalculation.classList.remove('alert-danger');
                    daysCalculation.classList.add('alert-success');
                    daysCalculation.querySelector('small').innerHTML =
                        '<i class="bi bi-info-circle me-1"></i>Se excluirán días festivos y de descanso configurados';
                }
            }
        } else {
            daysCalculation.style.display = 'none';
        }
    }

    // Trigger calculation if employee is preselected
    if (employeeSelect.value) {
        employeeSelect.dispatchEvent(new Event('change'));
    }
});
</script>

<style>
.alert {
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/vacations/create.blade.php ENDPATH**/ ?>