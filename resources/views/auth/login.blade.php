<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | GestiónPro</title>
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
            max-width: 420px;
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
            margin-top: 0.5rem;
        }

        .btn-login:hover {
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
            transform: translateY(-2px);
            color: white;
        }

        .form-check-input {
            background-color: #334155;
            border-color: rgba(255,255,255,0.1);
        }
        .form-check-input:checked {
            background-color: #6366f1;
            border-color: #6366f1;
        }
        .form-check-label {
            color: #94a3b8;
            font-size: 0.85rem;
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

        .demo-info {
            margin-top: 1.5rem;
            padding: 1rem;
            background: rgba(99, 102, 241, 0.08);
            border-radius: 12px;
            border: 1px solid rgba(99, 102, 241, 0.15);
        }

        .demo-info h6 {
            color: #818cf8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .demo-info p {
            color: #94a3b8;
            font-size: 0.8rem;
            margin: 0;
        }

        .demo-info code {
            color: #34d399;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">
            <div class="brand-icon"><i class="bi bi-rocket-takeoff"></i></div>
            <h3>GestiónPro</h3>
            <p>Plataforma de Gestión Empresarial</p>
        </div>

        @if($errors->any())
            <div class="alert mb-3">
                <i class="bi bi-exclamation-circle me-1"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                           placeholder="correo@empresa.com" required autofocus style="border-radius: 0 12px 12px 0">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" name="password"
                           placeholder="••••••••" required style="border-radius: 0 12px 12px 0">
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                <label class="form-check-label" for="remember">Recordar sesión</label>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </button>
        </form>
            <div class="mb-3 mt-2 d-flex justify-content-end">
                <a href="{{ route('password.request') }}" style="color: #6366f1; font-size: 0.8rem; text-decoration: none; font-weight: 500;">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>
        <div class="text-center mt-3 mb-1">
            <span style="color: #94a3b8; font-size: 0.875rem;">¿No tienes cuenta?</span>
            <a href="{{ route('register') }}" style="color: #6366f1; font-size: 0.875rem; text-decoration: none; font-weight: 600;">Regístrate aquí</a>
        </div>

        <div class="demo-info">
            <h6><i class="bi bi-info-circle me-1"></i>Credenciales Demo</h6>
            <p><strong>Super:</strong> <code>admin@techcorp.com</code></p>
            <p><strong>Admin:</strong> <code>maria@techcorp.com</code></p>
            <p><strong>Supervisor:</strong> <code>juan@techcorp.com</code></p>
            <p><strong>Usuario:</strong> <code>ana@techcorp.com</code></p>
            <p class="mt-1"><strong>Contraseña:</strong> <code>password123</code></p>
        </div>
    </div>
</body>
</html>
