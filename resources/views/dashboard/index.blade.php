@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card purple">
            <div class="stat-icon"><i class="bi bi-list-task"></i></div>
            <div class="stat-value">{{ $taskStats['pending'] }}</div>
            <div class="stat-label">Tareas Pendientes</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="bi bi-arrow-repeat"></i></div>
            <div class="stat-value">{{ $taskStats['in_progress'] }}</div>
            <div class="stat-label">En Progreso</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="stat-value">{{ $taskStats['completed'] }}</div>
            <div class="stat-label">Completadas</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card orange">
            <div class="stat-icon"><i class="bi bi-people"></i></div>
            <div class="stat-value">{{ $totalEmployees }}</div>
            <div class="stat-label">Empleados</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-secondary"><i class="bi bi-kanban-fill me-2"></i>Tareas Recientes</span>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-custom btn-sm">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Tarea</th>
                                <th>Estado</th>
                                <th>Prioridad</th>
                                <th>Asignado a</th>
                                <th>Vence</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTasks as $task)
                            <tr>
                                <td class="fw-semibold">{{ $task->title }}</td>
                                <td><span class="badge-status badge-{{ $task->status }}">{{ str_replace('_', ' ', ucfirst($task->status)) }}</span></td>
                                <td><span class="badge-status badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span></td>
                                <td>{{ $task->assignedUser->name ?? '—' }}</td>
                                <td>{{ $task->due_date ? $task->due_date->format('d/m/Y') : '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No hay tareas registradas</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header text-secondary">
                <i class="bi bi-bell-fill me-2"></i>Solicitudes Pendientes
            </div>
            <div class="card-body text-center py-4">
                <div class="stat-value mb-1" style="font-size: 2.5rem; color: var(--warning);">{{ $pendingRequests }}</div>
                <div class="stat-label text-white">Pendientes de aprobación</div>
                <a href="{{ route('requests.index') }}" class="btn btn-outline-custom btn-sm mt-3">Ver solicitudes</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header text-secondary">
                <i class="bi bi-clock-history me-2"></i>Accesos Recientes
            </div>
            <div class="card-body p-0">
                @forelse($recentAccess as $log)
                <div class="d-flex align-items-center gap-3 px-3 py-2" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: white; font-weight: 700;">
                        {{ strtoupper(substr($log->user->name ?? '', 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-size: 0.8rem; font-weight: 600; color: white;">{{ $log->user->name ?? '' }}</div>
                        <div style="font-size: 0.7rem; color: #64748b;">{{ $log->login_at->format('d/m H:i') }} — {{ $log->logout_at ? $log->logout_at->format('H:i') : 'Activo' }}</div>
                    </div>
                </div>
                @empty
                <div class="p-3 text-center text-muted" style="font-size: 0.85rem;">Sin registros</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@if($showTodayModal)
<!-- Today's Activities Modal -->
<div class="today-modal-overlay" id="todayModal">
    <div class="today-modal">
        <div class="today-modal-header">
            <div class="d-flex align-items-center gap-2">
                <div style="width:38px;height:38px;border-radius:10px;background:var(--gradient-1);display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-calendar-check" style="color:white;font-size:1.1rem;"></i>
                </div>
                <div>
                    <h5 style="margin:0;font-weight:700;font-size:1rem;color:white;">Actividades de Hoy</h5>
                    <small style="color:var(--dark-4);font-size:0.7rem;">{{ now()->translatedFormat('l, d \\d\\e F Y') }}</small>
                </div>
            </div>
            <button class="today-modal-close" onclick="closeTodayModal()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="today-modal-body">
            @foreach($todayEvents as $event)
            <div class="today-event-card">
                <div class="today-event-time">
                    <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($event->event_time)->format('h:i A') }}
                </div>
                <div class="today-event-title">{{ $event->title }}</div>
                @if($event->description)
                <div class="today-event-desc">{{ $event->description }}</div>
                @endif
                @if($event->links->isNotEmpty())
                <div class="today-event-links">
                    @foreach($event->links as $link)
                    <a href="{{ $link->url }}" target="_blank">
                        <i class="bi bi-link-45deg"></i>{{ $link->label ?: $link->url }}
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        <div class="today-modal-footer">
            <a href="{{ route('calendar.index') }}" class="btn btn-primary-custom w-100">
                <i class="bi bi-calendar-event me-2"></i>Ir al Calendario
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    .today-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.65);
        backdrop-filter: blur(6px);
        z-index: 3000;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: todayOverlayIn 0.3s ease;
    }

    @keyframes todayOverlayIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .today-modal {
        background: var(--dark-2);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 20px;
        width: 100%;
        max-width: 460px;
        max-height: 75vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 60px rgba(0,0,0,0.5), 0 0 40px rgba(99,102,241,0.08);
        animation: todayModalIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes todayModalIn {
        from { opacity: 0; transform: scale(0.9) translateY(20px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .today-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .today-modal-close {
        background: rgba(255,255,255,0.05);
        border: none;
        color: #94a3b8;
        font-size: 1.1rem;
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .today-modal-close:hover {
        color: white;
        background: rgba(239,68,68,0.15);
    }

    .today-modal-body {
        padding: 1rem 1.5rem;
        overflow-y: auto;
        flex: 1;
    }

    .today-event-card {
        padding: 0.9rem 1rem;
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 12px;
        margin-bottom: 0.6rem;
        border-left: 3px solid var(--primary);
        transition: all 0.2s ease;
    }

    .today-event-card:hover {
        background: rgba(99,102,241,0.04);
        border-color: rgba(99,102,241,0.15);
        border-left-color: var(--primary-light);
    }

    .today-event-time {
        font-size: 0.72rem;
        color: var(--primary-light);
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .today-event-title {
        font-weight: 600;
        font-size: 0.9rem;
        color: white;
        margin-bottom: 0.2rem;
    }

    .today-event-desc {
        font-size: 0.78rem;
        color: #94a3b8;
        line-height: 1.5;
    }

    .today-event-links {
        margin-top: 0.45rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }

    .today-event-links a {
        font-size: 0.68rem;
        padding: 2px 7px;
        border-radius: 6px;
        background: rgba(6,182,212,0.1);
        color: #67e8f9;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        transition: all 0.15s;
    }

    .today-event-links a:hover {
        background: rgba(6,182,212,0.22);
    }

    .today-modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid rgba(255,255,255,0.06);
    }

    @media (max-width: 576px) {
        .today-modal { margin: 1rem; border-radius: 16px; }
    }
</style>
@endpush

@push('scripts')
<script>
    function closeTodayModal() {
        const modal = document.getElementById('todayModal');
        modal.style.opacity = '0';
        modal.style.transition = 'opacity 0.25s ease';
        setTimeout(() => modal.remove(), 250);
    }
    // Close on overlay click
    document.getElementById('todayModal').addEventListener('click', function(e) {
        if (e.target === this) closeTodayModal();
    });
    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeTodayModal();
    });
</script>
@endpush
@endif

@endsection
