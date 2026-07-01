@extends('layouts.app')
@section('title', 'Empresa')
@section('page-title', 'Configuración de Empresa')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-building me-2"></i>Información de la Empresa</div>
            <div class="card-body">
                <form method="POST" action="{{ route('company.update') }}" enctype="multipart/form-data">
                    @csrf
                    {{-- Logo Upload --}}
                    <div class="mb-4">
                        <label class="form-label">Logo de la Empresa</label>
                        <div class="d-flex gap-4 align-items-start">
                            <div class="flex-shrink-0">
                                @if($company->logo)
                                    <img src="{{ \Storage::disk('s3')->url($company->logo) }}" alt="Logo" class="rounded-3" style="max-width: 200px; max-height: 167px; object-fit: contain; border: 2px solid rgba(255,255,255,0.1);">
                                @else
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 200px; height: 167px; background: rgba(255,255,255,0.05); border: 2px dashed rgba(255,255,255,0.2);">
                                        <i class="bi bi-image fs-1 text-white-50"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" class="form-control" name="logo" accept="image/jpeg,image/png,image/jpg">
                                <small class="text-white mt-2 d-block">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Formatos: JPEG, PNG, JPG | Máximo: 2MB | Dimensiones máximas: 600x500px
                                </small>
                                @if($company->logo)
                                    <button type="button" class="btn btn-outline-custom btn-sm mt-2" onclick="deleteLogo()">
                                        <i class="bi bi-trash me-1"></i> Eliminar Logo
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre de la Empresa</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $company->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">RNC</label>
                            <input type="text" class="form-control" name="rnc" value="{{ old('rnc', $company->rnc) }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email', $company->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="phone" value="{{ old('phone', $company->phone) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <textarea class="form-control" name="address" rows="3">{{ old('address', $company->address) }}</textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Tasa SRL (%)</label>
                            <input type="number" step="0.01" class="form-control" name="srl_rate" value="{{ old('srl_rate', $company->srl_rate) }}" min="1.0" max="1.5">
                            <small class="text-white">Riesgos Laborales (Promedio 1.10% - 1.30%)</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Plan</label>
                            <input type="text" class="form-control" value="{{ ucfirst($company->plan) }}" disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estado</label>
                            <input type="text" class="form-control" value="{{ ucfirst($company->status) }}" disabled>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label"><i class="bi bi-calendar2-week me-1"></i>Frecuencia de Nómina</label>
                            <select class="form-select" name="payroll_frequency" id="payroll_frequency">
                                <option value="" @selected(is_null($company->payroll_frequency))>— Seleccionar —</option>
                                <option value="monthly"   @selected($company->payroll_frequency === 'monthly')>Mensual (12 períodos / año)</option>
                                <option value="biweekly"  @selected($company->payroll_frequency === 'biweekly')>Quincenal (24 períodos / año)</option>
                            </select>
                            <small class="text-white">Afecta el cálculo de ISR, ARS, AFP y la generación de períodos.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pago Bonificación de Ley</label>
                            <select class="form-select" name="bonus_payment_method" id="bonus_payment_method">
                                <option value="payroll" @selected(old('bonus_payment_method', $company->bonus_payment_method) === 'payroll')>Pagar con la Nómina</option>
                                <option value="separate" @selected(old('bonus_payment_method', $company->bonus_payment_method) === 'separate')>Pago Separado</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-4" id="bonus_split_container" style="display: none;">
                        <div class="col-md-12">
                            <label class="form-label">División de Bonificación (Nómina Quincenal)</label>
                            <select class="form-select" name="bonus_biweekly_split">
                                <option value="">— Seleccionar —</option>
                                <option value="both" @selected(old('bonus_biweekly_split', $company->bonus_biweekly_split) === 'both')>Ambas Quincenas (Dividido)</option>
                                <option value="q1" @selected(old('bonus_biweekly_split', $company->bonus_biweekly_split) === 'q1')>1ra Quincena (Completo)</option>
                                <option value="q2" @selected(old('bonus_biweekly_split', $company->bonus_biweekly_split) === 'q2')>2da Quincena (Completo)</option>
                            </select>
                            <small class="text-white">Define cómo se agregará la bonificación si se paga con la nómina quincenal.</small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteLogo() {
    if (confirm('¿Está seguro de que desea eliminar el logo de la empresa?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('company.deleteLogo') }}';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const payrollFrequency = document.getElementById('payroll_frequency');
    const bonusPaymentMethod = document.getElementById('bonus_payment_method');
    const bonusSplitContainer = document.getElementById('bonus_split_container');

    function toggleBonusSplit() {
        if (payrollFrequency && bonusPaymentMethod && bonusSplitContainer) {
            if (payrollFrequency.value === 'biweekly' && bonusPaymentMethod.value === 'payroll') {
                bonusSplitContainer.style.display = 'flex';
            } else {
                bonusSplitContainer.style.display = 'none';
            }
        }
    }

    if (payrollFrequency && bonusPaymentMethod) {
        payrollFrequency.addEventListener('change', toggleBonusSplit);
        bonusPaymentMethod.addEventListener('change', toggleBonusSplit);
        toggleBonusSplit();
    }
});
</script>
@endpush
