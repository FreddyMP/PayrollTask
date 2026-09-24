<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Portal de Referentes - PayrollTask</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --emerald: #10b981;
            --emerald-dark: #059669;
            --emerald-light: #34d399;
            --bg: #090e1c;
            --surface: rgba(15,23,42,0.9);
            --surface-2: rgba(20,32,55,0.8);
            --border: rgba(255,255,255,0.07);
            --text: #f1f5f9;
            --text-muted: #64748b;
            --text-sub: #94a3b8;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── Navbar ─────────────────────────────────────────────── */
        .navbar-ref {
            background: rgba(9,14,28,0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 0.85rem 1.5rem;
            position: sticky; top: 0; z-index: 100;
        }
        .navbar-ref .brand {
            display: flex; align-items: center; gap: 0.65rem;
            text-decoration: none;
        }
        .navbar-ref .brand .icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--emerald) 0%, var(--emerald-dark) 100%);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; color: white;
            box-shadow: 0 4px 12px rgba(16,185,129,0.3);
        }
        .navbar-ref .brand span { font-weight: 700; color: var(--text); font-size: 1rem; }
        .navbar-ref .brand small { color: var(--text-muted); font-size: 0.7rem; display: block; }
        .badge-portal {
            background: rgba(16,185,129,0.12);
            color: var(--emerald);
            border: 1px solid rgba(16,185,129,0.2);
            border-radius: 6px;
            padding: 0.2rem 0.6rem;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.03em;
        }
        .user-pill {
            display: flex; align-items: center; gap: 0.5rem;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 0.35rem 0.85rem 0.35rem 0.35rem;
        }
        .user-pill .avatar {
            width: 30px; height: 30px;
            background: linear-gradient(135deg, var(--emerald) 0%, var(--emerald-dark) 100%);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; color: white; font-weight: 700;
        }
        .user-pill span { font-size: 0.82rem; font-weight: 500; color: var(--text-sub); }
        .btn-logout {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            color: #f87171;
            border-radius: 8px;
            padding: 0.35rem 0.75rem;
            font-size: 0.82rem;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-logout:hover {
            background: rgba(239,68,68,0.2);
            color: #fca5a5;
        }

        /* ── Main Content ────────────────────────────────────────── */
        .main { padding: 2rem 1.5rem; max-width: 1200px; margin: 0 auto; }

        /* ── Stats Cards ─────────────────────────────────────────── */
        .stat-card {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.3);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 2px;
        }
        .stat-card.green::before { background: linear-gradient(90deg, var(--emerald), var(--emerald-dark)); }
        .stat-card.indigo::before { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
        .stat-card.amber::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
        .stat-card.cyan::before { background: linear-gradient(90deg, #06b6d4, #0284c7); }

        .stat-icon {
            width: 46px; height: 46px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; margin-bottom: 1rem;
        }
        .stat-icon.green { background: rgba(16,185,129,0.12); color: var(--emerald); }
        .stat-icon.indigo { background: rgba(99,102,241,0.12); color: #818cf8; }
        .stat-icon.amber { background: rgba(245,158,11,0.12); color: #fbbf24; }
        .stat-icon.cyan { background: rgba(6,182,212,0.12); color: #22d3ee; }

        .stat-value { font-size: 1.75rem; font-weight: 800; line-height: 1; margin-bottom: 0.35rem; }
        .stat-label { color: var(--text-muted); font-size: 0.8rem; font-weight: 500; }

        /* ── Referral Box ────────────────────────────────────────── */
        .referral-box {
            background: linear-gradient(135deg, rgba(16,185,129,0.08) 0%, rgba(5,150,105,0.05) 100%);
            border: 1px solid rgba(16,185,129,0.2);
            border-radius: 18px;
            padding: 1.75rem;
        }
        .referral-box h5 { color: var(--text); font-weight: 700; margin-bottom: 0.25rem; }
        .referral-box p { color: var(--text-muted); font-size: 0.85rem; }
        .code-display {
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(16,185,129,0.25);
            border-radius: 12px;
            padding: 0.85rem 1.25rem;
            font-family: 'Courier New', monospace;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--emerald);
            letter-spacing: 0.2em;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            user-select: all;
        }
        .code-display:hover {
            background: rgba(16,185,129,0.08);
            border-color: rgba(16,185,129,0.5);
        }
        .url-display {
            background: rgba(0,0,0,0.2);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.6rem 1rem;
            font-size: 0.8rem;
            color: var(--text-sub);
            word-break: break-all;
            cursor: pointer;
            transition: all 0.2s;
        }
        .url-display:hover { background: rgba(255,255,255,0.04); }
        .btn-copy {
            background: var(--emerald);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .btn-copy:hover { background: var(--emerald-dark); color: white; }

        /* ── Table ───────────────────────────────────────────────── */
        .section-card {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 18px;
            overflow: hidden;
        }
        .section-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .section-header h5 { font-size: 0.95rem; font-weight: 700; color: var(--text); margin: 0; }
        .table-dark-custom {
            margin: 0;
        }
        .table-dark-custom thead th {
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.85rem 1.5rem;
        }
        .table-dark-custom tbody td {
            background: transparent;
            border-bottom: 1px solid var(--border);
            color: var(--text-sub);
            font-size: 0.87rem;
            padding: 0.9rem 1.5rem;
            vertical-align: middle;
        }
        .table-dark-custom tbody tr:last-child td { border-bottom: none; }
        .table-dark-custom tbody tr:hover td { background: rgba(255,255,255,0.02); }
        .company-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.8rem; color: white;
            flex-shrink: 0;
        }
        .plan-badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 600;
        }
        .plan-badge.starter { background: rgba(99,102,241,0.15); color: #818cf8; }
        .plan-badge.growth  { background: rgba(16,185,129,0.15); color: var(--emerald); }
        .plan-badge.business { background: rgba(245,158,11,0.15); color: #fbbf24; }
        .plan-badge.enterprise { background: rgba(139,92,246,0.15); color: #c4b5fd; }
        .plan-badge.trial { background: rgba(100,116,139,0.15); color: #94a3b8; }
        .comision-badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .comision-badge.activa { background: rgba(16,185,129,0.15); color: var(--emerald); }
        .comision-badge.pendiente { background: rgba(245,158,11,0.12); color: #fbbf24; }
        .empty-state {
            text-align: center;
            padding: 3rem;
        }
        .empty-state .icon { font-size: 2.5rem; color: var(--text-muted); margin-bottom: 1rem; }
        .empty-state p { color: var(--text-muted); font-size: 0.9rem; }

        /* ── Modals ──────────────────────────────────────────────── */
        .modal-dark .modal-content {
            background: #0f1829;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            color: var(--text);
        }
        .modal-dark .modal-header {
            border-bottom: 1px solid var(--border);
            padding: 1.5rem;
        }
        .modal-dark .modal-body { padding: 1.5rem; }
        .modal-dark .modal-footer {
            border-top: 1px solid var(--border);
            padding: 1.25rem 1.5rem;
        }
        .modal-dark .modal-title { font-weight: 700; font-size: 1.1rem; }
        .modal-dark .btn-close { filter: invert(1) brightness(0.7); }
        .modal-dark .form-label { color: var(--text-sub); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
        .modal-dark .form-control,
        .modal-dark .form-select {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: var(--text);
            padding: 0.65rem 1rem;
            font-size: 0.92rem;
        }
        .modal-dark .form-control:focus,
        .modal-dark .form-select:focus {
            background: rgba(255,255,255,0.07);
            border-color: var(--emerald);
            box-shadow: 0 0 0 3px rgba(16,185,129,0.15);
            color: var(--text);
        }
        .modal-dark .form-select option { background: #1e293b; }
        .btn-emerald {
            background: linear-gradient(135deg, var(--emerald) 0%, var(--emerald-dark) 100%);
            border: none; border-radius: 10px; color: white;
            font-weight: 600; padding: 0.65rem 1.5rem;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(16,185,129,0.3);
        }
        .btn-emerald:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(16,185,129,0.4); color: white; }
        .modal-welcome-icon {
            width: 70px; height: 70px;
            background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: white;
            box-shadow: 0 10px 30px rgba(16,185,129,0.4);
            margin: 0 auto 1.25rem;
        }
        .info-item {
            display: flex; align-items: flex-start; gap: 0.85rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.9rem 1rem;
            margin-bottom: 0.6rem;
        }
        .info-item .info-icon {
            width: 34px; height: 34px;
            background: rgba(16,185,129,0.12);
            color: var(--emerald);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; flex-shrink: 0;
        }
        .info-item .info-text strong { display: block; color: var(--text); font-size: 0.88rem; margin-bottom: 0.15rem; }
        .info-item .info-text span { color: var(--text-muted); font-size: 0.8rem; }

        /* ── Success alert ───────────────────────────────────────── */
        .alert-success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25); color: #6ee7b7; border-radius: 10px; }
        .alert-danger { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; border-radius: 10px; }

        /* ── Copy toast ──────────────────────────────────────────── */
        .copy-toast {
            position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999;
            background: rgba(16,185,129,0.9);
            color: white; padding: 0.65rem 1.25rem;
            border-radius: 10px; font-size: 0.85rem; font-weight: 600;
            transform: translateY(100px); opacity: 0;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(16,185,129,0.4);
        }
        .copy-toast.show { transform: translateY(0); opacity: 1; }

        /* Banking info alert box */
        .banking-alert {
            background: rgba(245,158,11,0.08);
            border: 1px solid rgba(245,158,11,0.25);
            border-radius: 12px;
            padding: 0.9rem 1.25rem;
            display: flex; align-items: center; gap: 0.75rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .banking-alert:hover { background: rgba(245,158,11,0.13); }
        .banking-alert i { color: #fbbf24; font-size: 1.1rem; }
        .banking-alert span { color: #fbbf24; font-size: 0.85rem; font-weight: 500; }
    </style>
</head>
<body>

    {{-- ═══ Navbar ═══ --}}
    <nav class="navbar-ref d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('referentes.dashboard') }}" class="brand">
                <div class="icon"><i class="bi bi-people-fill"></i></div>
                <div>
                    <span>PayrollTask</span>
                    <small>Portal de Referentes</small>
                </div>
            </a>
            <span class="badge-portal d-none d-sm-inline">Referente</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="user-pill d-none d-sm-flex">
                <div class="avatar">{{ strtoupper(substr($referente->nombre_completo, 0, 1)) }}</div>
                <span>{{ explode(' ', $referente->nombre_completo)[0] }}</span>
            </div>
            <form method="POST" action="{{ route('referentes.logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="bi bi-box-arrow-right me-1"></i>Salir
                </button>
            </form>
        </div>
    </nav>

    {{-- ═══ Main ═══ --}}
    <div class="main">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success mb-3">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        {{-- Banking data missing warning --}}
        @if($showBankingModal)
            <div class="banking-alert mb-4" data-bs-toggle="modal" data-bs-target="#bankingModal">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>¡Completa tus datos bancarios para recibir tus comisiones!</span>
                <i class="bi bi-arrow-right ms-auto"></i>
            </div>
        @endif

        {{-- Page header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="fs-4 fw-800 mb-1">Mi Dashboard</h1>
                <p style="color:var(--text-muted);font-size:0.85rem;">
                    Código: <strong style="color:var(--emerald)">{{ $referente->codigo_referido }}</strong>
                </p>
            </div>
            <button class="btn-emerald" data-bs-toggle="modal" data-bs-target="#bankingModal">
                <i class="bi bi-bank me-1"></i>
                <span class="d-none d-sm-inline">Datos Bancarios</span>
            </button>
        </div>

        {{-- ── Stats ── --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="stat-card green">
                    <div class="stat-icon green"><i class="bi bi-buildings-fill"></i></div>
                    <div class="stat-value">{{ $empresas->count() }}</div>
                    <div class="stat-label">Empresas Referidas</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card indigo">
                    <div class="stat-icon indigo"><i class="bi bi-cash-coin"></i></div>
                    <div class="stat-value">RD${{ number_format($totalComision, 0, '.', ',') }}</div>
                    <div class="stat-label">Comisión Activa</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card amber">
                    <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
                    <div class="stat-value">RD${{ number_format($comisionPendiente, 0, '.', ',') }}</div>
                    <div class="stat-label">Pendiente (2 meses)</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card cyan">
                    <div class="stat-icon cyan"><i class="bi bi-percent"></i></div>
                    <div class="stat-value">15%</div>
                    <div class="stat-label">Tu Comisión por Plan</div>
                </div>
            </div>
        </div>

        {{-- ── Referral Box ── --}}
        <div class="referral-box mb-4">
            <div class="row align-items-center g-3">
                <div class="col-md-5">
                    <h5><i class="bi bi-share-fill me-2" style="color:var(--emerald)"></i>Tu Código de Referido</h5>
                    <p class="mt-1">Compártelo o usa el enlace directo</p>
                    <div class="code-display mt-3" id="codeDisplay" onclick="copyToClipboard('{{ $referente->codigo_referido }}', 'Código copiado')">
                        {{ $referente->codigo_referido }}
                    </div>
                </div>
                <div class="col-md-7">
                    <p style="color:var(--text-sub);font-size:0.82rem;margin-bottom:0.5rem;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;">
                        Enlace de Referido
                    </p>
                    <div class="d-flex gap-2 align-items-center">
                        <div class="url-display flex-grow-1" id="urlDisplay"
                             onclick="copyToClipboard('{{ $referralUrl }}', 'Enlace copiado')">
                            {{ $referralUrl }}
                        </div>
                        <button class="btn-copy" onclick="copyToClipboard('{{ $referralUrl }}', 'Enlace copiado')">
                            <i class="bi bi-copy me-1"></i>Copiar
                        </button>
                    </div>
                    <p style="color:var(--text-muted);font-size:0.75rem;margin-top:0.5rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Al abrir el enlace, el código se cargará automáticamente en el registro.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Empresas Table ── --}}
        <div class="section-card">
            <div class="section-header">
                <h5><i class="bi bi-table me-2" style="color:var(--emerald)"></i>Empresas Registradas con mi Código</h5>
                <span style="color:var(--text-muted);font-size:0.8rem;">{{ $empresas->count() }} empresa(s)</span>
            </div>

            @if($empresas->isEmpty())
                <div class="empty-state">
                    <div class="icon"><i class="bi bi-buildings"></i></div>
                    <p>Aún no tienes empresas registradas con tu código.</p>
                    <p class="mt-1" style="color:var(--text-muted);font-size:0.8rem;">Comparte tu código o enlace para empezar a ganar comisiones.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-dark-custom">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>Fecha Registro</th>
                                <th>Plan</th>
                                <th>Meses</th>
                                <th>Comisión</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $precios = ['starter'=>2000,'growth'=>4000,'business'=>7000,'enterprise'=>12000];
                            @endphp
                            @foreach($empresas as $empresa)
                                @php
                                    $meses = (int) $empresa->created_at->diffInMonths(now());
                                    $precio = $precios[$empresa->subscription_plan] ?? 0;
                                    $comision = $precio * 0.15;
                                    $activa = $meses >= 2 && $empresa->subscription_plan;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="company-avatar">
                                                {{ strtoupper(substr($empresa->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight:600;color:var(--text)">{{ $empresa->name }}</div>
                                                <div style="font-size:0.75rem;color:var(--text-muted)">{{ $empresa->email ?? '—' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $empresa->created_at->format('d/m/Y') }}</div>
                                        <div style="font-size:0.75rem;color:var(--text-muted)">{{ $empresa->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        @if($empresa->subscription_plan)
                                            <span class="plan-badge {{ $empresa->subscription_plan }}">
                                                {{ ucfirst($empresa->subscription_plan) }}
                                            </span>
                                        @else
                                            <span class="plan-badge trial">Trial</span>
                                        @endif
                                    </td>
                                    <td>{{ $meses }} mes(es)</td>
                                    <td>
                                        @if($empresa->subscription_plan)
                                            <strong style="color:var(--emerald)">
                                                RD${{ number_format($comision, 0, '.', ',') }}/mes
                                            </strong>
                                        @else
                                            <span style="color:var(--text-muted)">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$empresa->subscription_plan)
                                            <span class="comision-badge pendiente">Sin plan</span>
                                        @elseif($activa)
                                            <span class="comision-badge activa"><i class="bi bi-check-circle-fill me-1"></i>Activa</span>
                                        @else
                                            <span class="comision-badge pendiente"><i class="bi bi-hourglass-split me-1"></i>Mes {{ $meses + 1 }}/3</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>{{-- /main --}}

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Welcome (first time) --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="modal fade modal-dark" id="welcomeModal" tabindex="-1" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div></div>
                </div>
                <div class="modal-body text-center pt-0">
                    <div class="modal-welcome-icon">
                        <i class="bi bi-stars"></i>
                    </div>
                    <h4 class="fw-800 mb-1">¡Bienvenido al Programa!</h4>
                    <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:1.5rem;">
                        Aquí tienes todo lo que necesitas saber para empezar a ganar comisiones.
                    </p>

                    <div class="text-start">
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-calendar3"></i></div>
                            <div class="info-text">
                                <strong>Pagos desde el 3er mes</strong>
                                <span>Las comisiones de cada empresa comienzan a contarse a partir del tercer mes de suscripción activa.</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-percent"></i></div>
                            <div class="info-text">
                                <strong>15% por cada suscripción</strong>
                                <span>Por cada empresa que se registre con tu código y tenga un plan activo, recibirás el 15% del valor mensual de su plan.</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="bi bi-share-fill"></i></div>
                            <div class="info-text">
                                <strong>Tu código y enlace únicos</strong>
                                <span>Tienes un código de referido y un enlace directo. Al usar el enlace, el código se cargará automáticamente en el formulario de registro de las empresas.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn-emerald" data-bs-dismiss="modal"
                            onclick="document.getElementById('bankingModal') && setTimeout(()=>new bootstrap.Modal(document.getElementById('bankingModal')).show(), 300)">
                        <i class="bi bi-check-circle me-2"></i>¡Entendido, empecemos!
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Banking Info --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="modal fade modal-dark" id="bankingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-bank me-2" style="color:var(--emerald)"></i>
                        Datos para Depósitos
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('referentes.banking.save') }}">
                    @csrf
                    <div class="modal-body">
                        @if($referente->datosBancarios)
                            <div class="alert alert-success mb-3">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                Datos bancarios registrados. Puedes actualizarlos aquí.
                            </div>
                        @else
                            <p style="color:var(--text-muted);font-size:0.85rem;margin-bottom:1.25rem;">
                                Completa tus datos bancarios para que podamos enviarte tus comisiones.
                            </p>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Banco</label>
                            <select name="banco" id="banco" class="form-select" required>
                                <option value="">Selecciona tu banco...</option>
                                @foreach(['Popular','BHD','Banreservas','Banesco','Qik','Promerica'] as $banco)
                                    <option value="{{ $banco }}"
                                        {{ (old('banco', $referente->datosBancarios?->banco) === $banco) ? 'selected' : '' }}>
                                        {{ $banco }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipo de Cuenta</label>
                            <select name="tipo_cuenta" id="tipo_cuenta" class="form-select" required>
                                <option value="">Selecciona el tipo...</option>
                                @foreach(['Corriente','Ahorro'] as $tipo)
                                    <option value="{{ $tipo }}"
                                        {{ (old('tipo_cuenta', $referente->datosBancarios?->tipo_cuenta) === $tipo) ? 'selected' : '' }}>
                                        {{ $tipo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Número de Cuenta</label>
                            <input type="text"
                                   name="numero_cuenta"
                                   id="numero_cuenta"
                                   class="form-control"
                                   placeholder="Número de cuenta bancaria"
                                   value="{{ old('numero_cuenta', $referente->datosBancarios?->numero_cuenta) }}"
                                   required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn-emerald">
                            <i class="bi bi-save me-1"></i>Guardar Datos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Copy toast --}}
    <div class="copy-toast" id="copyToast">
        <i class="bi bi-check-circle-fill me-2"></i><span id="copyToastMsg">Copiado</span>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ── Show modals on load ──────────────────────────────────
        document.addEventListener('DOMContentLoaded', function () {
            @if($showWelcomeModal)
                var welcomeModal = new bootstrap.Modal(document.getElementById('welcomeModal'));
                welcomeModal.show();
            @elseif($showBankingModal)
                var bankingModal = new bootstrap.Modal(document.getElementById('bankingModal'));
                bankingModal.show();
            @endif
        });

        // ── Copy to clipboard ────────────────────────────────────
        function copyToClipboard(text, msg) {
            navigator.clipboard.writeText(text).then(function () {
                showToast(msg || 'Copiado');
            });
        }

        function showToast(msg) {
            var toast = document.getElementById('copyToast');
            document.getElementById('copyToastMsg').textContent = msg;
            toast.classList.add('show');
            setTimeout(function () { toast.classList.remove('show'); }, 2500);
        }
    </script>
</body>
</html>
