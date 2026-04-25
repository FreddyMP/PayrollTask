<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GestiónPro') | GestiónPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --accent: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --dark-2: #1e293b;
            --dark-3: #334155;
            --dark-4: #475569;
            --light: #f8fafc;
            --sidebar-width: 260px;
            --gradient-1: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
            --gradient-2: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
            --gradient-3: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark);
            color: #e2e8f0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--dark-2);
            border-right: 1px solid rgba(255,255,255,0.06);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-brand {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .sidebar-brand .brand-icon {
            width: 40px;
            height: 40px;
            background: var(--gradient-1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .sidebar-brand h5 {
            font-weight: 700;
            font-size: 1.1rem;
            color: white;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .sidebar-brand small {
            font-size: 0.7rem;
            color: var(--dark-4);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1rem 0.75rem;
            overflow-y: auto;
        }

        .sidebar-nav .nav-section {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--dark-4);
            padding: 0.75rem 0.75rem 0.5rem;
            font-weight: 600;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.75rem;
            color: #94a3b8;
            border-radius: 10px;
            margin-bottom: 2px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
        }

        .sidebar-nav .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.05);
        }

        .sidebar-nav .nav-link.active {
            color: white;
            background: var(--primary);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.25);
        }

        .sidebar-nav .nav-link i {
            font-size: 1.15rem;
            width: 24px;
            text-align: center;
        }

        .sidebar-nav .nav-link .badge {
            margin-left: auto;
            font-size: 0.65rem;
            padding: 0.2em 0.55em;
            border-radius: 20px;
        }

        .sidebar-user {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-user .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--gradient-2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            color: white;
        }

        .sidebar-user .user-info h6 {
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
            margin: 0;
        }

        .sidebar-user .user-info small {
            font-size: 0.7rem;
            color: var(--dark-4);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .topbar {
            height: 64px;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar h4 {
            font-weight: 700;
            font-size: 1.15rem;
            color: white;
            margin: 0;
        }

        .topbar .breadcrumb {
            margin: 0;
            font-size: 0.8rem;
        }

        .topbar .breadcrumb-item a { color: var(--dark-4); text-decoration: none; }
        .topbar .breadcrumb-item.active { color: #94a3b8; }

        .content-area {
            padding: 1.5rem;
        }

        /* Cards */
        .card {
            background: var(--dark-2);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.1);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .card-body { padding: 1.25rem; }

        /* Stat Cards */
        .stat-card {
            background: var(--dark-2);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 16px;
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: white;
            margin-bottom: 1rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: white;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .stat-card .stat-label {
            font-size: 0.8rem;
            color: var(--dark-4);
            font-weight: 500;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            opacity: 0.05;
            transform: translate(30%, -30%);
        }

        .stat-card.purple .stat-icon { background: var(--gradient-1); }
        .stat-card.purple::after { background: var(--primary); }
        .stat-card.blue .stat-icon { background: var(--gradient-2); }
        .stat-card.blue::after { background: var(--accent); }
        .stat-card.green .stat-icon { background: var(--gradient-3); }
        .stat-card.green::after { background: var(--success); }
        .stat-card.orange .stat-icon { background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); }
        .stat-card.orange::after { background: var(--warning); }

        /* Tables */
        .table { color: #e2e8f0; }
        .table thead th {
            background: var(--dark-3);
            color: #94a3b8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            border: none;
            padding: 0.75rem 1rem;
        }
        .table thead th:first-child { border-radius: 10px 0 0 10px; }
        .table thead th:last-child { border-radius: 0 10px 10px 0; }
        .table tbody td {
            padding: 0.85rem 1rem;
            border-color: rgba(255,255,255,0.04);
            vertical-align: middle;
            font-size: 0.875rem;
        }
        .table tbody tr:hover { background: rgba(255,255,255,0.02); }

        /* Badges */
        .badge-status {
            padding: 0.35em 0.75em;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .badge-pending { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .badge-in_progress, .badge-in-progress { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
        .badge-review { background: rgba(168, 85, 247, 0.15); color: #c084fc; }
        .badge-completed, .badge-approved, .badge-paid { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .badge-cancelled, .badge-rejected { background: rgba(239, 68, 68, 0.15); color: #f87171; }
        .badge-active { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .badge-inactive { background: rgba(100, 116, 139, 0.15); color: #94a3b8; }

        .badge-low { background: rgba(100, 116, 139, 0.15); color: #94a3b8; }
        .badge-medium { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
        .badge-high { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .badge-urgent { background: rgba(239, 68, 68, 0.15); color: #f87171; }

        .badge-super { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .badge-admin { background: rgba(168, 85, 247, 0.15); color: #c084fc; }
        .badge-supervisor { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
        .badge-usuario { background: rgba(100, 116, 139, 0.15); color: #94a3b8; }

        .badge-vacation { background: rgba(6, 182, 212, 0.15); color: #22d3ee; }
        .badge-permission { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .badge-work_letter { background: rgba(168, 85, 247, 0.15); color: #c084fc; }
        .badge-overtime { background: rgba(239, 68, 68, 0.15); color: #fb923c; }

        /* Buttons */
        .btn-primary-custom {
            background: var(--gradient-1);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            border-radius: 10px;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        .btn-primary-custom:hover {
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
            transform: translateY(-1px);
            color: white;
        }

        .btn-outline-custom {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.1);
            color: #94a3b8;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        .btn-outline-custom:hover {
            border-color: var(--primary-light);
            color: white;
            background: rgba(99, 102, 241, 0.1);
        }

        /* Forms */
        .form-control, .form-select {
            background: var(--dark-3);
            border: 1px solid rgba(255,255,255,0.08);
            color: white;
            border-radius: 10px;
            padding: 0.6rem 0.9rem;
            font-size: 0.875rem;
        }
        .form-control:focus, .form-select:focus {
            background: var(--dark-3);
            border-color: var(--primary);
            color: white;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }
        .form-control::placeholder { color: var(--dark-4); }
        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.4rem;
        }

        /* Alerts */
        .alert { border-radius: 12px; border: none; font-size: 0.875rem; }
        .alert-success { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .alert-danger { background: rgba(239, 68, 68, 0.15); color: #f87171; }

        /* Pagination */
        .pagination .page-link {
            background: var(--dark-3);
            border-color: rgba(255,255,255,0.06);
            color: #94a3b8;
            font-size: 0.85rem;
        }
        .pagination .page-link:hover { background: var(--dark-4); color: white; }
        .pagination .active .page-link { background: var(--primary); border-color: var(--primary); }

        /* Mobile */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .sidebar-toggle { display: block; }
            .content-area { padding: 1rem; }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--dark); }
        ::-webkit-scrollbar-thumb { background: var(--dark-3); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--dark-4); }

        /* Animations */
        .fade-in { animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* Modal */
        .modal-content {
            background: var(--dark-2);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
        }
        .modal-header { border-color: rgba(255,255,255,0.06); }
        .modal-footer { border-color: rgba(255,255,255,0.06); }
        .btn-close { filter: invert(1); }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-rocket-takeoff"></i></div>
            <div>
                <h5>GestiónPro</h5>
                <small>Panel Empresarial</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Principal</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>

            <div class="nav-section">Gestión</div>
            <a href="{{ route('tasks.index') }}" class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                <i class="bi bi-kanban-fill"></i> Tablero de Tareas
            </a>
            @if(auth()->user()->isSupervisor())
            <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                <i class="bi bi-folder-fill"></i> Proyectos
            </a>
            <a href="{{ route('devices.index') }}" class="nav-link {{ request()->routeIs('devices.*') ? 'active' : '' }}">
                <i class="bi bi-laptop-fill"></i> Dispositivos
            </a>
            @endif
            <a href="{{ route('requests.index') }}" class="nav-link {{ request()->routeIs('requests.*') ? 'active' : '' }}">
                <i class="bi bi-send-fill"></i> Solicitudes
            </a>
            <a href="{{ route('calendar.index') }}" class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-event-fill"></i> Calendario
            </a>

            @if(auth()->user()->isAdmin())
            <div class="nav-section">Administración</div>
            <a href="{{ route('access-logs.index') }}" class="nav-link {{ request()->routeIs('access-logs.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Registro de Accesos
            </a>
            <a href="{{ route('recruitment.index') }}" class="nav-link {{ request()->routeIs('recruitment.*') ? 'active' : '' }}">
                <i class="bi bi-person-plus-fill"></i> Reclutamiento
            </a>

            <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Empleados
            </a>
            <a href="{{ route('payroll.index') }}" class="nav-link {{ request()->routeIs('payroll.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Nómina
            </a>
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i> Reportes
            </a>

            <div class="nav-section">Documentación</div>
            <a href="{{ route('company-fields.index') }}" class="nav-link {{ request()->routeIs('company-fields.*') ? 'active' : '' }}">
                <i class="bi bi-tags-fill"></i> Variables Globales
            </a>
            <a href="{{ route('documents.index') }}" class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-richtext-fill"></i> Plantillas y Contratos
            </a>
            @endif

            <div class="nav-section">Sistema</div>
            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i> Configuraciones
            </a>
            @if(auth()->user()->isSuper())
            <a href="{{ route('company.edit') }}" class="nav-link {{ request()->routeIs('company.*') ? 'active' : '' }}">
                <i class="bi bi-building-fill"></i> Empresa
            </a>
            @endif
        </nav>

        <div class="sidebar-user">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <div class="user-info">
                <h6>{{ auth()->user()->name }}</h6>
                <small>{{ ucfirst(auth()->user()->role) }} · {{ auth()->user()->company->name ?? '' }}</small>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <main class="main-content">
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('active')">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <h4>@yield('page-title', 'Dashboard')</h4>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge badge-status badge-{{ auth()->user()->role }}">{{ ucfirst(auth()->user()->role) }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-custom btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Salir
                    </button>
                </form>
            </div>
        </header>

        <div class="content-area fade-in">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
    </script>
    @stack('scripts')
</body>
</html>
