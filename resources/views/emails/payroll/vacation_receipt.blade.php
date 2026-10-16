<x-mail::message>
# Comprobante de Pago de Vacaciones

Hola **{{ $vacation->employee->user->name ?? 'Empleado' }}**,

Te enviamos tu comprobante oficial de pago correspondiente al disfrute de tus **Vacaciones**.

---

## Información del Empleado y Vacaciones

- **Empleado:** {{ $vacation->employee->user->name ?? 'N/A' }}
- **Cédula / Documento:** {{ $vacation->employee->id_number ?? 'N/A' }}
- **Departamento:** {{ $vacation->employee->department_rel->name ?? ($vacation->employee->department ?? 'N/A') }}
- **Cargo:** {{ $vacation->employee->position->name ?? 'N/A' }}
- **Período de Disfrute:** {{ $vacation->start_date ? $vacation->start_date->format('d/m/Y') : 'N/A' }} al {{ $vacation->end_date ? $vacation->end_date->format('d/m/Y') : 'N/A' }}
- **Días Hábiles Pagados:** {{ $vacation->days_taken }} días
- **Salario Ordinario Mensual:** RD$ {{ number_format($vacation->employee->salary ?? 0, 2) }}
- **Salario Ordinario Diario:** RD$ {{ number_format(($vacation->employee->salary ?? 0) / 23.83, 2) }}
- **Fecha de Pago:** {{ $vacation->paid_at ? $vacation->paid_at->format('d/m/Y H:i') : now()->format('d/m/Y') }}

---

## Desglose de Ingresos y Descuentos de Ley

| Concepto | Monto |
|:---------|------:|
| **Ingreso por Vacaciones (Bruto)** ({{ $vacation->days_taken }} días) | **RD$ {{ number_format($vacation->payment_amount ?? $payroll->gross_salary, 2) }}** |
@if(($vacation->payment_ars ?? $payroll->ars) > 0)
| Seguro Familiar de Salud (SFS / TSS 3.04%) | - RD$ {{ number_format($vacation->payment_ars ?? $payroll->ars, 2) }} |
@endif
@if(($vacation->payment_afp ?? $payroll->afp) > 0)
| Fondo de Pensiones (AFP / TSS 2.87%) | - RD$ {{ number_format($vacation->payment_afp ?? $payroll->afp, 2) }} |
@endif
@if(($vacation->payment_isr ?? $payroll->isr) > 0)
| Retención de Impuesto Sobre la Renta (ISR / IR-3) | - RD$ {{ number_format($vacation->payment_isr ?? $payroll->isr, 2) }} |
@endif

---

## Total Neto a Recibir

<x-mail::panel>
**Monto Neto Depositado / Pagado:**  
# RD$ {{ number_format($vacation->payment_net ?? $payroll->net_salary, 2) }}
</x-mail::panel>

---

<small style="color: #64748b;">
Este documento sirve como comprobante formal de recepción de pago por concepto de vacaciones según el Código de Trabajo de la República Dominicana. Si tienes dudas o consultas, comunícate con el departamento de Gestión Humana.
</small>

Saludos cordiales,<br>
**{{ $companyName }}**
</x-mail::message>
