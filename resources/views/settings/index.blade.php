@extends('layouts.app')
@section('title', 'Configuraciones')
@section('page-title', 'Configuraciones')

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-person me-2"></i>Perfil</div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.profile') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" class="form-control" name="phone" value="{{ auth()->user()->phone }}">
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-check-lg me-1"></i> Actualizar Perfil</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-envelope me-2"></i>Cambiar Correo</div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.email') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Correo Actual</label>
                        <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nuevo Correo</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña (confirmación)</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-check-lg me-1"></i> Cambiar Correo</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-lock me-2"></i>Cambiar Contraseña</div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.password') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Contraseña Actual</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control" name="password" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-check-lg me-1"></i> Cambiar Contraseña</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
