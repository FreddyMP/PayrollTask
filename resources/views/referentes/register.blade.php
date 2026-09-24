<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Referente | PayrollTask</title>
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
            background: #0a0f1e;
            position: relative;
            overflow: hidden;
            padding: 2rem 0;
        }
        .orb-1 {
            position: absolute;
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(16,185,129,0.12) 0%, transparent 70%);
            top: -250px; right: -150px; border-radius: 50%; pointer-events: none;
        }
        .orb-2 {
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(99,102,241,0.10) 0%, transparent 70%);
            bottom: -150px; left: -100px; border-radius: 50%; pointer-events: none;
        }
        .card-wrap {
            background: rgba(18, 28, 48, 0.85);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 1;
            box-shadow: 0 30px 60px rgba(0,0,0,0.4);
        }
        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .brand-icon {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: white;
            margin-bottom: 1rem;
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.35);
        }
        .brand h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 0.25rem;
        }
        .brand p {
            color: #64748b;
            font-size: 0.875rem;
        }
        .form-label {
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.4rem;
        }
        .form-control {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: #f1f5f9;
            padding: 0.65rem 1rem;
            font-size: 0.92rem;
            transition: border-color 0.2s, background 0.2s;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.07);
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16,185,129,0.15);
            color: #f1f5f9;
            outline: none;
        }
        .form-control::placeholder { color: #475569; }
        .input-group-text {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            color: #64748b;
        }
        .btn-register {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.75rem;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(16,185,129,0.3);
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16,185,129,0.4);
            color: white;
        }
        .btn-register:active { transform: translateY(0); }
        .divider {
            text-align: center;
            margin: 1.25rem 0;
            position: relative;
        }
        .divider::before {
            content: '';
            position: absolute;
            top: 50%; left: 0; right: 0;
            height: 1px;
            background: rgba(255,255,255,0.07);
        }
        .divider span {
            background: #12192f;
            padding: 0 0.75rem;
            color: #475569;
            font-size: 0.8rem;
            position: relative;
        }
        .link-accent { color: #10b981; text-decoration: none; font-weight: 500; }
        .link-accent:hover { color: #34d399; text-decoration: underline; }
        .badge-referente {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(16,185,129,0.12);
            color: #10b981;
            border: 1px solid rgba(16,185,129,0.25);
            border-radius: 20px;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .invalid-feedback { display: block; }
        .alert-danger {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="orb-1"></div>
    <div class="orb-2"></div>

    <div class="card-wrap">
        <div class="brand">
            <div class="badge-referente">
                <i class="bi bi-people-fill"></i>
                Portal de Referentes
            </div>
            <div class="brand-icon">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <h3>Conviértete en Referente</h3>
            <p>Gana comisiones refiriendo empresas a PayrollTask</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('referentes.register.post') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nombre Completo</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <input type="text"
                           name="nombre_completo"
                           id="nombre_completo"
                           class="form-control border-start-0 @error('nombre_completo') is-invalid @enderror"
                           placeholder="Tu nombre completo"
                           value="{{ old('nombre_completo') }}"
                           required autofocus>
                </div>
                @error('nombre_completo')
                    <div class="invalid-feedback text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Cédula o RNC <span class="text-muted">(se usará como usuario)</span></label>
                <div class="input-group">
                    <span class="input-group-text border-end-0">
                        <i class="bi bi-card-text"></i>
                    </span>
                    <input type="text"
                           name="cedula_rnc"
                           id="cedula_rnc"
                           class="form-control border-start-0 @error('cedula_rnc') is-invalid @enderror"
                           placeholder="000-0000000-0"
                           value="{{ old('cedula_rnc') }}"
                           required>
                </div>
                @error('cedula_rnc')
                    <div class="invalid-feedback text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Número de Teléfono</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0">
                        <i class="bi bi-telephone-fill"></i>
                    </span>
                    <input type="tel"
                           name="telefono"
                           id="telefono"
                           class="form-control border-start-0 @error('telefono') is-invalid @enderror"
                           placeholder="(809) 000-0000"
                           value="{{ old('telefono') }}"
                           required>
                </div>
                @error('telefono')
                    <div class="invalid-feedback text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0">
                        <i class="bi bi-lock-fill"></i>
                    </span>
                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control border-start-0 @error('password') is-invalid @enderror"
                           placeholder="Mínimo 8 caracteres"
                           required>
                </div>
                @error('password')
                    <div class="invalid-feedback text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Confirmar Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0">
                        <i class="bi bi-lock-fill"></i>
                    </span>
                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           class="form-control border-start-0"
                           placeholder="Repite tu contraseña"
                           required>
                </div>
            </div>

            <button type="submit" class="btn-register">
                <i class="bi bi-rocket-takeoff-fill me-2"></i>
                Crear mi cuenta de referente
            </button>
        </form>

        <div class="divider"><span>¿Ya tienes cuenta?</span></div>

        <div class="text-center">
            <a href="{{ route('referentes.login') }}" class="link-accent">
                <i class="bi bi-box-arrow-in-right me-1"></i>
                Iniciar sesión
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
