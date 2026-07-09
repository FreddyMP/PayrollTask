<?php if (isset($component)) { $__componentOriginalaa758e6a82983efcbf593f765e026bd9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalaa758e6a82983efcbf593f765e026bd9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => $__env->getContainer()->make(Illuminate\View\Factory::class)->make('mail::message'),'data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mail::message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
# Volante de Pago

Hola <?php echo new \Illuminate\Support\EncodedHtmlString($payroll->employee->user->name); ?>,

Te enviamos tu volante de pago correspondiente al período **<?php echo new \Illuminate\Support\EncodedHtmlString($payroll->period); ?>**.

---

## Detalles del Pago

**Empleado:** <?php echo new \Illuminate\Support\EncodedHtmlString($payroll->employee->user->name); ?>

**Departamento:** <?php echo new \Illuminate\Support\EncodedHtmlString($payroll->employee->department); ?>

**Período:** <?php echo new \Illuminate\Support\EncodedHtmlString($payroll->period); ?>

**Fecha de Pago:** <?php echo new \Illuminate\Support\EncodedHtmlString($payroll->payment_date ? $payroll->payment_date->format('d/m/Y') : 'Pendiente'); ?>


---

## Ingresos

| Concepto | Monto |
|----------|-------|
| Salario Bruto | RD$ <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($payroll->gross_salary, 2)); ?> |
| Extras / Incentivos | RD$ <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($payroll->extras, 2)); ?> |
| **Total Ingresos** | **RD$ <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($payroll->gross_salary + $payroll->extras, 2)); ?>** |

---

## Deducciones

| Concepto | Monto |
|----------|-------|
| ARS (3.04%) | RD$ <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($payroll->ars, 2)); ?> |
| AFP (2.87%) | RD$ <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($payroll->afp, 2)); ?> |
| ISR (Impuesto sobre la Renta) | RD$ <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($payroll->isr, 2)); ?> |
| Otros Descuentos | RD$ <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($payroll->descuentos, 2)); ?> |
| **Total Deducciones** | **RD$ <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($payroll->deductions, 2)); ?>** |

---

## Neto a Recibir

| Concepto | Monto |
|----------|-------|
| **Salario Neto** | **RD$ <?php echo new \Illuminate\Support\EncodedHtmlString(number_format($payroll->net_salary, 2)); ?>** |

---

Este documento es tu comprobante de pago. Si tienes alguna pregunta, por favor contacta al departamento de recursos humanos.

Gracias,<br>
<?php echo new \Illuminate\Support\EncodedHtmlString($companyName); ?>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalaa758e6a82983efcbf593f765e026bd9)): ?>
<?php $attributes = $__attributesOriginalaa758e6a82983efcbf593f765e026bd9; ?>
<?php unset($__attributesOriginalaa758e6a82983efcbf593f765e026bd9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalaa758e6a82983efcbf593f765e026bd9)): ?>
<?php $component = $__componentOriginalaa758e6a82983efcbf593f765e026bd9; ?>
<?php unset($__componentOriginalaa758e6a82983efcbf593f765e026bd9); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/emails/payroll/receipt.blade.php ENDPATH**/ ?>