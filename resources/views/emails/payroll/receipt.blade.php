<x-mail::message>
# Volante de Pago

Hola {{ $payroll->employee->user->name }},

Te enviamos tu volante de pago correspondiente al período **{{ $payroll->period }}**.

---

## Detalles del Pago

**Empleado:** {{ $payroll->employee->user->name }}
**Departamento:** {{ $payroll->employee->department }}
**Período:** {{ $payroll->period }}
**Fecha de Pago:** {{ $payroll->payment_date ? $payroll->payment_date->format('d/m/Y') : 'Pendiente' }}

---

## Ingresos

| Concepto | Monto |
|----------|-------|
| Salario Bruto | RD$ {{ number_format($payroll->gross_salary, 2) }} |
| Extras / Incentivos | RD$ {{ number_format($payroll->extras, 2) }} |
| **Total Ingresos** | **RD$ {{ number_format($payroll->gross_salary + $payroll->extras, 2) }}** |

---

## Deducciones

| Concepto | Monto |
|----------|-------|
| ARS (3.04%) | RD$ {{ number_format($payroll->ars, 2) }} |
| AFP (2.87%) | RD$ {{ number_format($payroll->afp, 2) }} |
| ISR (Impuesto sobre la Renta) | RD$ {{ number_format($payroll->isr, 2) }} |
| Otros Descuentos | RD$ {{ number_format($payroll->descuentos, 2) }} |
| **Total Deducciones** | **RD$ {{ number_format($payroll->deductions, 2) }}** |

---

## Neto a Recibir

| Concepto | Monto |
|----------|-------|
| **Salario Neto** | **RD$ {{ number_format($payroll->net_salary, 2) }}** |

---

Este documento es tu comprobante de pago. Si tienes alguna pregunta, por favor contacta al departamento de recursos humanos.

Gracias,<br>
{{ $companyName }}
</x-mail::message>
