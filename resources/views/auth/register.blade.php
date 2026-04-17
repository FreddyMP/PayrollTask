<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Compañía | GestiónPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            position: relative;
            overflow: hidden;
            padding: 2rem 0;
        }

        body::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            top: -200px;
            right: -100px;
            border-radius: 50%;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.1) 0%, transparent 70%);
            bottom: -100px;
            left: -100px;
            border-radius: 50%;
        }

        .login-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 1;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }

        .login-brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-brand .brand-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }

        .login-brand h3 {
            color: white;
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.02em;
        }

        .login-brand p {
            color: #64748b;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .form-control {
            background: #334155;
            border: 1px solid rgba(255,255,255,0.08);
            color: white;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }

        .form-control:focus {
            background: #334155;
            border-color: #6366f1;
            color: white;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .form-control::placeholder { color: #64748b; }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
            border: none;
            color: white;
            font-weight: 700;
            padding: 0.75rem;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-login:hover {
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
            transform: translateY(-2px);
            color: white;
        }

        .alert {
            border-radius: 12px;
            font-size: 0.85rem;
            border: none;
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
        }

        .input-group-text {
            background: #334155;
            border: 1px solid rgba(255,255,255,0.08);
            color: #64748b;
            border-radius: 12px 0 0 12px;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: #94a3b8;
        }

        .login-footer a {
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .section-title {
            color: #818cf8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding-bottom: 0.5rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">
            <div class="brand-icon"><i class="bi bi-building-add"></i></div>
            <h3>Crear Compañía</h3>
            <p>Registra tu empresa y comienza a gestionar</p>
        </div>

        @if($errors->any())
            <div class="alert mb-3">
                <i class="bi bi-exclamation-circle me-1"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="section-title">Datos de la Empresa</div>
            
            <div class="mb-3">
                <label class="form-label">Nombre de la Compañía</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                    <input type="text" class="form-control" name="company_name" value="{{ old('company_name') }}"
                           placeholder="Mi Empresa S.R.L" required autofocus style="border-radius: 0 12px 12px 0">
                </div>
            </div>

            <div class="section-title">Datos del Administrador</div>

            <div class="mb-3">
                <label class="form-label">Nombre Completo</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}"
                           placeholder="Juan Pérez" required style="border-radius: 0 12px 12px 0">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                           placeholder="admin@empresa.com" required style="border-radius: 0 12px 12px 0">
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" name="password"
                                   placeholder="••••••••" required style="border-radius: 0 12px 12px 0">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Confirmar Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                            <input type="password" class="form-control" name="password_confirmation"
                                   placeholder="••••••••" required style="border-radius: 0 12px 12px 0">
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="bi bi-check-circle me-2"></i>Registrar Compañía
            </button>
        </form>

        <div class="login-footer">
            ¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia Sesión</a>
        </div>
    </div>
</body>
</html>
