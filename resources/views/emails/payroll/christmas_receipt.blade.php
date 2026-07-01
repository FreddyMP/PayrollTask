<x-mail::message>
# Comprobante de Pago - Salario de Navidad

Hola {{ $payroll->employee->user->name }},

Te enviamos tu comprobante de pago correspondiente al **Salario de Navidad**.

---

## Detalles del Pago

**Empleado:** {{ $payroll->employee->user->name }}
**Departamento:** {{ $payroll->employee->department?->name ?? 'No asignado' }}
**Período:** {{ $payroll->period }}
**Fecha de Pago:** {{ $payroll->payment_date ? $payroll->payment_date->format('d/m/Y') : 'Pendiente' }}

---

## Detalle de Ingresos

| Concepto | Monto |
|----------|-------|
| Salario de Navidad | RD$ {{ number_format($payroll->gross_salary, 2) }} |

*Nota: El Salario de Navidad está exento del pago de Impuesto sobre la Renta (ISR), así como de las cotizaciones a la Seguridad Social (ARS y AFP), por lo que no se aplican deducciones a este pago.*

---

## Neto a Recibir

| Concepto | Monto |
|----------|-------|
| **Total Recibido** | **RD$ {{ number_format($payroll->net_salary, 2) }}** |

---

Este documento es tu comprobante de pago. Si tienes alguna pregunta, por favor contacta al departamento de recursos humanos.

Felices Fiestas,<br>
{{ $companyName }}
</x-mail::message>
