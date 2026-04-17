@extends('layouts.app')
@section('title', 'Empresa')
@section('page-title', 'Configuración de Empresa')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-building me-2"></i>Información de la Empresa</div>
            <div class="card-body">
                <form method="POST" action="{{ route('company.update') }}">
                    @csrf
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
                        <div class="col-md-6">
                            <label class="form-label">Plan</label>
                            <input type="text" class="form-control" value="{{ ucfirst($company->plan) }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estado</label>
                            <input type="text" class="form-control" value="{{ ucfirst($company->status) }}" disabled>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-check-lg me-1"></i> Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
