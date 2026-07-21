<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'PayrollTask'); ?> | PayrollTask</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            border-right: 1px solid rgba(255, 255, 255, 0.06);
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
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
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
            background: rgba(255, 255, 255, 0.05);
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
            border-top: 1px solid rgba(255, 255, 255, 0.06);
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
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
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

        .topbar .breadcrumb-item a {
            color: var(--dark-4);
            text-decoration: none;
        }

        .topbar .breadcrumb-item.active {
            color: #94a3b8;
        }

        .content-area {
            padding: 1.5rem;
        }

        /* Cards */
        .card {
            background: var(--dark-2);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.25rem;
        }

        /* Stat Cards */
        .stat-card {
            background: var(--dark-2);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
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

        .stat-card.purple .stat-icon {
            background: var(--gradient-1);
        }

        .stat-card.purple::after {
            background: var(--primary);
        }

        .stat-card.blue .stat-icon {
            background: var(--gradient-2);
        }

        .stat-card.blue::after {
            background: var(--accent);
        }

        .stat-card.green .stat-icon {
            background: var(--gradient-3);
        }

        .stat-card.green::after {
            background: var(--success);
        }

        .stat-card.orange .stat-icon {
            background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
        }

        .stat-card.orange::after {
            background: var(--warning);
        }

        /* Tables */
        .table {
            color: #e2e8f0;
        }

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

        .table thead th:first-child {
            border-radius: 10px 0 0 10px;
        }

        .table thead th:last-child {
            border-radius: 0 10px 10px 0;
        }

        .table tbody td {
            padding: 0.85rem 1rem;
            border-color: rgba(255, 255, 255, 0.04);
            vertical-align: middle;
            font-size: 0.875rem;
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Badges */
        .badge-status {
            padding: 0.35em 0.75em;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .badge-pending {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
        }

        .badge-in_progress,
        .badge-in-progress {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
        }

        .badge-review {
            background: rgba(168, 85, 247, 0.15);
            color: #c084fc;
        }

        .badge-completed,
        .badge-approved,
        .badge-paid {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
        }

        .badge-cancelled,
        .badge-rejected {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
        }

        .badge-active {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
        }

        .badge-inactive {
            background: rgba(100, 116, 139, 0.15);
            color: #94a3b8;
        }

        .badge-low {
            background: rgba(100, 116, 139, 0.15);
            color: #94a3b8;
        }

        .badge-medium {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
        }

        .badge-high {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
        }

        .badge-urgent {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
        }

        .badge-super {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
        }

        .badge-admin {
            background: rgba(168, 85, 247, 0.15);
            color: #c084fc;
        }

        .badge-supervisor {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
        }

        .badge-usuario {
            background: rgba(100, 116, 139, 0.15);
            color: #94a3b8;
        }

        .badge-vacation {
            background: rgba(6, 182, 212, 0.15);
            color: #22d3ee;
        }

        .badge-permission {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
        }

        .badge-work_letter {
            background: rgba(168, 85, 247, 0.15);
            color: #c084fc;
        }

        .badge-overtime {
            background: rgba(239, 68, 68, 0.15);
            color: #fb923c;
        }

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
            border: 1px solid rgba(255, 255, 255, 0.1);
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
        .form-control,
        .form-select {
            background: var(--dark-3);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: white;
            border-radius: 10px;
            padding: 0.6rem 0.9rem;
            font-size: 0.875rem;
        }

        .form-control:focus,
        .form-select:focus {
            background: var(--dark-3);
            border-color: var(--primary);
            color: white;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .form-control::placeholder {
            color: var(--dark-4);
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.4rem;
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            border: none;
            font-size: 0.875rem;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
        }

        /* Pagination */
        .pagination .page-link {
            background: var(--dark-3);
            border-color: rgba(255, 255, 255, 0.06);
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .pagination .page-link:hover {
            background: var(--dark-4);
            color: white;
        }

        .pagination .active .page-link {
            background: var(--primary);
            border-color: var(--primary);
        }

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
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block;
            }

            .content-area {
                padding: 1rem;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--dark-3);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--dark-4);
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modal */
        .modal-content {
            background: var(--dark-2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
        }

        .modal-header {
            border-color: rgba(255, 255, 255, 0.06);
        }

        .modal-footer {
            border-color: rgba(255, 255, 255, 0.06);
        }

        .btn-close {
            filter: invert(1);
        }

        /* Global Loader */
        .global-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.3s ease;
        }

        .global-loader .spinner-border {
            width: 3.5rem;
            height: 3.5rem;
            border-width: 0.25em;
            color: var(--primary) !important;
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <!-- Global Loader Overlay -->
    <div id="global-loader" class="global-loader d-none">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <div class="mt-3 fw-semibold text-white tracking-wide" style="letter-spacing: 1px; font-size: 0.9rem;">
            PROCESANDO...</div>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div>
                <?php if(auth()->user()->company && auth()->user()->company->logo): ?>
                    <img src="<?php echo e(\Storage::disk('s3')->url(auth()->user()->company->logo)); ?>" alt="Logo" width="200">
                <?php else: ?>
                    <img src="https://payrolltask-s3-cloud-852128327213-us-east-2-an.s3.us-east-2.amazonaws.com/logo.png"
                        alt="Logo" width="200">
                <?php endif; ?>
                <small>Panel Empresarial</small>
            </div>
        </div>

        <?php $company = auth()->user()->company; ?>
        <nav class="sidebar-nav">
            <div class="nav-section">Principal</div>
            <?php if($company->hasFeature('dashboard')): ?>
            <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <?php endif; ?>
            <?php if($company->hasFeature('org_chart')): ?>
            <a href="<?php echo e(route('org-chart.index')); ?>" class="nav-link <?php echo e(request()->routeIs('org-chart.*') ? 'active' : ''); ?>">
                <i class="bi bi-diagram-3"></i> Organigrama
            </a>
            <?php endif; ?>
            <?php if(auth()->user()->isAdmin() && $company->hasFeature('documents')): ?>
                <div class="nav-section">Documentación</div>
                <a href="<?php echo e(route('documents.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('documents.*') ? 'active' : ''); ?>">
                    <i class="bi bi-file-earmark-richtext-fill"></i> Generación de Documentos
                </a>
            <?php endif; ?>

            <?php if(auth()->user()->isAdmin()): ?>
                <div class="nav-section">Administración</div>
                <?php if($company->hasFeature('vacations')): ?>
                <a href="<?php echo e(route('vacations.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('vacations.*') ? 'active' : ''); ?>">
                    <i class="bi bi-calendar-check-fill"></i> Gestión de Vacaciones
                </a>
                <?php endif; ?>
                <?php if($company->hasFeature('access_logs')): ?>
                <a href="<?php echo e(route('access-logs.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('access-logs.*') ? 'active' : ''); ?>">
                    <i class="bi bi-clock-history"></i> Registro de Accesos
                </a>
                <?php endif; ?>
                <?php if($company->hasFeature('recruitment')): ?>
                <a href="<?php echo e(route('recruitment.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('recruitment.*') ? 'active' : ''); ?>">
                    <i class="bi bi-person-plus-fill"></i> Reclutamiento
                </a>
                <?php endif; ?>
                <?php if($company->hasFeature('employees')): ?>
                <a href="<?php echo e(route('employees.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('employees.*') ? 'active' : ''); ?>">
                    <i class="bi bi-people-fill"></i> Empleados
                </a>
                <?php endif; ?>
                <?php if($company->hasFeature('fichaje')): ?>
                <a href="<?php echo e(route('fichajes.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('fichajes.*') ? 'active' : ''); ?>">
                    <i class="bi bi-fingerprint"></i> Fichajes
                </a>
                <?php endif; ?>
                <?php if($company->hasFeature('evaluations')): ?>
                <a href="<?php echo e(route('evaluations.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('evaluations.*') ? 'active' : ''); ?>">
                    <i class="bi bi-clipboard2-check"></i> Evaluaciones de Personal
                </a>
                <?php endif; ?>
                <?php if($company->hasFeature('departments')): ?>
                <a href="<?php echo e(route('departments.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('departments.*', 'positions.*') ? 'active' : ''); ?>">
                    <i class="bi bi-diagram-3"></i> Departamentos
                </a>
                <?php endif; ?>
                <?php if($company->hasFeature('payroll')): ?>
                <a href="<?php echo e(route('payroll.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('payroll.*') ? 'active' : ''); ?>">
                    <i class="bi bi-cash-stack"></i> Nómina
                </a>
                <?php endif; ?>
                <?php if($company->hasFeature('reports')): ?>
                <a href="<?php echo e(route('reports.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>">
                    <i class="bi bi-graph-up-arrow"></i> Reportes
                </a>
                <?php endif; ?>
            <?php endif; ?>

            <div class="nav-section">Gestión</div>
            <?php if($company->hasFeature('regulations')): ?>
            <a href="<?php echo e(route('regulations.index')); ?>"
                class="nav-link <?php echo e(request()->routeIs('regulations.*') ? 'active' : ''); ?>">
                <i class="bi bi-file-earmark-text-fill"></i> Reglamentos
            </a>
            <?php endif; ?>
            <?php if($company->hasFeature('tasks')): ?>
            <a href="<?php echo e(route('tasks.index')); ?>" class="nav-link <?php echo e(request()->routeIs('tasks.*') ? 'active' : ''); ?>">
                <i class="bi bi-kanban-fill"></i> Tablero de Tareas
            </a>
            <?php endif; ?>
            <?php if(auth()->user()->isSupervisor()): ?>
                <?php if($company->hasFeature('projects')): ?>
                <a href="<?php echo e(route('projects.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('projects.*') ? 'active' : ''); ?>">
                    <i class="bi bi-folder-fill"></i> Proyectos
                </a>
                <?php endif; ?>
                <?php if($company->hasFeature('devices')): ?>
                <a href="<?php echo e(route('devices.index')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('devices.*') ? 'active' : ''); ?>">
                    <i class="bi bi-laptop-fill"></i> Dispositivos
                </a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if($company->hasFeature('requests')): ?>
            <a href="<?php echo e(route('requests.index')); ?>"
                class="nav-link <?php echo e(request()->routeIs('requests.*') ? 'active' : ''); ?>">
                <i class="bi bi-send-fill"></i> Solicitudes
            </a>
            <?php endif; ?>
            <?php if($company->hasFeature('incidents')): ?>
            <a href="<?php echo e(route('incidencias.index')); ?>"
                class="nav-link <?php echo e(request()->routeIs('incidencias.*') ? 'active' : ''); ?>">
                <i class="bi bi-exclamation-triangle-fill"></i> Incidencias
            </a>
            <?php endif; ?>
            <?php if($company->hasFeature('calendar')): ?>
            <a href="<?php echo e(route('calendar.index')); ?>"
                class="nav-link <?php echo e(request()->routeIs('calendar.*') ? 'active' : ''); ?>">
                <i class="bi bi-calendar-event-fill"></i> Calendario
            </a>
            <?php endif; ?>

            <div class="nav-section">Sistema</div>
            <?php if($company->hasFeature('settings')): ?>
            <a href="<?php echo e(route('settings.index')); ?>"
                class="nav-link <?php echo e(request()->routeIs('settings.*') ? 'active' : ''); ?>">
                <i class="bi bi-gear-fill"></i> Configuraciones
            </a>
            <?php endif; ?>
            <?php if(auth()->user()->isSuper() && $company->hasFeature('company')): ?>
                <a href="<?php echo e(route('company.edit')); ?>"
                    class="nav-link <?php echo e(request()->routeIs('company.*') ? 'active' : ''); ?>">
                    <i class="bi bi-building-fill"></i> Empresa
                </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-user">
            <div class="user-avatar"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 2))); ?></div>
            <div class="user-info">
                <h6><?php echo e(auth()->user()->name); ?></h6>
                <small><?php echo e(ucfirst(auth()->user()->role)); ?> · <?php echo e(auth()->user()->company->name ?? ''); ?></small>
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
                    <h4><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></h4>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span
                    class="badge badge-status badge-<?php echo e(auth()->user()->role); ?>"><?php echo e(ucfirst(auth()->user()->role)); ?></span>
                <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline-custom btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Salir
                    </button>
                </form>
            </div>
        </header>

        <div class="content-area fade-in">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // Prevent double submissions and show global loader
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('form');
            const loader = document.getElementById('global-loader');

            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    // Check if form has target="_blank"
                    if (form.getAttribute('target') === '_blank') {
                        return;
                    }

                    // Allow HTML5 validation to kick in
                    if (!form.checkValidity()) {
                        return;
                    }

                    // Check if a confirmation dialog prevented the submission (e.g. onsubmit="return confirm(...)")
                    // This is trickier because inline onsubmit runs before event listeners if added as HTML attribute.
                    // If the form is actually submitting, we can show the loader.

                    if (loader) {
                        loader.classList.remove('d-none');
                    }

                    // Disable submit buttons to prevent double clicking
                    const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                    submitButtons.forEach(btn => {
                        // Use a small timeout so the button's name/value can be submitted if needed
                        setTimeout(() => {
                            btn.disabled = true;
                            // Optionally change text
                            if (btn.tagName === 'BUTTON' && !btn.innerHTML.includes('spinner')) {
                                const originalContent = btn.innerHTML;
                                btn.dataset.originalText = originalContent;
                                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Procesando...';
                            }
                        }, 50);
                    });
                });
            });

            // Ocultar loader si el usuario navega hacia atrás usando la caché del navegador (BFCache)
            window.addEventListener('pageshow', function (event) {
                if (event.persisted && loader) {
                    loader.classList.add('d-none');
                    const buttons = document.querySelectorAll('button[type="submit"], input[type="submit"]');
                    buttons.forEach(btn => {
                        btn.disabled = false;
                        if (btn.tagName === 'BUTTON' && btn.dataset.originalText) {
                            btn.innerHTML = btn.dataset.originalText;
                        }
                    });
                }
            });
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/layouts/app.blade.php ENDPATH**/ ?>