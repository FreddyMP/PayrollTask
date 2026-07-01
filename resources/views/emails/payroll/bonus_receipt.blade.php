<x-mail::message>
# Comprobante de Pago - Bonificación de Ley

Hola {{ $payroll->employee->user->name }},

Te enviamos tu comprobante de pago correspondiente a la **Bonificación de Ley (Utilidades)**.

---

## Detalles del Pago

**Empleado:** {{ $payroll->employee->user->name }}
**Departamento:** {{ $payroll->employee->department?->name ?? 'No asignado' }}
**Período:** {{ $payroll->period }}
**Fecha de Pago:** {{ $payroll->payment_date ? $payroll->payment_date->format('d/m/Y') : 'Pendiente' }}

---

## Detalle del Pago

| Concepto | Monto |
|----------|-------|
| Bonificación (Bruto) | RD$ {{ number_format($payroll->gross_salary, 2) }} |
@if($payroll->isr > 0)
| Retención ISR | - RD$ {{ number_format($payroll->isr, 2) }} |
@endif

---

## Neto a Recibir

| Concepto | Monto |
|----------|-------|
| **Total Recibido** | **RD$ {{ number_format($payroll->net_salary, 2) }}** |

---

Este documento es tu comprobante de pago. Si tienes alguna pregunta, por favor contacta al departamento de recursos humanos.

Saludos cordiales,<br>
{{ $companyName }}
</x-mail::message>
