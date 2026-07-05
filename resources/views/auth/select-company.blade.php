<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Empresa | PayrollTask</title>
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

        .select-card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 520px;
            position: relative;
            z-index: 1;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-header img { margin-bottom: 0.75rem; }

        .brand-header h4 {
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
        }

        .brand-header p {
            color: #64748b;
            font-size: 0.875rem;
        }

        .company-btn {
            display: flex;
            align-items: center;
            gap: 1rem;
            width: 100%;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            color: white;
            text-align: left;
            transition: all 0.25s ease;
            cursor: pointer;
            margin-bottom: 0.75rem;
            text-decoration: none;
        }

        .company-btn:hover {
            background: rgba(99, 102, 241, 0.12);
            border-color: rgba(99, 102, 241, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99,102,241,0.15);
            color: white;
        }

        .company-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            flex-shrink: 0;
            font-weight: 700;
            letter-spacing: -0.05em;
        }

        .company-name {
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 0.15rem;
        }

        .company-role {
            font-size: 0.75rem;
            color: #64748b;
        }

        .company-arrow {
            margin-left: auto;
            color: #334155;
            font-size: 1rem;
            transition: color 0.2s;
        }

        .company-btn:hover .company-arrow {
            color: #6366f1;
        }

        .divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin: 1.5rem 0;
        }

        .logout-link {
            display: block;
            text-align: center;
            color: #64748b;
            font-size: 0.8rem;
            text-decoration: none;
        }

        .logout-link:hover { color: #f87171; }
    </style>
</head>
<body>
    <div class="select-card">
        <div class="brand-header">
            <img src="https://payrolltask-s3-cloud-852128327213-us-east-2-an.s3.us-east-2.amazonaws.com/logo.png" alt="Logo" width="180">
            <h4>¿A qué empresa deseas acceder?</h4>
            <p>Hola, <strong style="color: #e2e8f0;">{{ Auth::user()->name }}</strong>. Selecciona la empresa para continuar.</p>
        </div>

        @foreach($companies as $employee)
            <form method="POST" action="{{ route('select-company.post') }}">
                @csrf
                <input type="hidden" name="company_id" value="{{ $employee->company_id }}">
                <button type="submit" class="company-btn">
                    <div class="company-icon">
                        {{ strtoupper(substr($employee->company->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="company-name">{{ $employee->company->name }}</div>
                        <div class="company-role">
                            <i class="bi bi-person-badge me-1"></i>
                            {{ ucfirst($employee->role ?? $employee->user->role ?? 'usuario') }}
                        </div>
                    </div>
                    <i class="bi bi-chevron-right company-arrow"></i>
                </button>
            </form>
        @endforeach

        <hr class="divider">

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-link w-100" style="background:none; border:none; cursor:pointer;">
                <i class="bi bi-box-arrow-left me-1"></i>Cerrar sesión y salir
            </button>
        </form>
    </div>
</body>
</html>
