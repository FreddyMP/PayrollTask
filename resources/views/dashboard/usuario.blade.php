@extends('layouts.app')
@section('title', 'Mi Panel')
@section('page-title', 'Mi Panel')

@section('content')
    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card purple">
                <div class="stat-icon"><i class="bi bi-clipboard2-check"></i></div>
                <div class="stat-value">{{ $pendingEvaluations->count() }}</div>
                <div class="stat-label">Evaluaciones Pendientes</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card blue">
                <div class="stat-icon"><i class="bi bi-kanban"></i></div>
                <div class="stat-value">{{ $pendingTasksCount }}</div>
                <div class="stat-label">Tareas Activas</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card orange">
                <div class="stat-icon"><i class="bi bi-send-check"></i></div>
                <div class="stat-value">{{ collect($myRequests)->where('status', 'pending')->count() }}</div>
                <div class="stat-label">Solicitudes en Proceso</div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="row g-3 mb-4">
        {{-- LEFT COLUMN --}}
        <div class="col-lg-8">
            
            {{-- EVALUACIONES PENDIENTES --}}
            <div class="card mb-3" style="border-left: 3px solid var(--primary);">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="text-white" style="font-weight: 600;"><i class="bi bi-card-checklist me-2 text-primary"></i>Evaluaciones Requeridas</span>
                    @if($pendingEvaluations->count() > 0)
                        <span class="badge bg-danger rounded-pill">{{ $pendingEvaluations->count() }} Pendientes</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    @forelse($pendingEvaluations as $assignment)
                        <div class="d-flex align-items-center justify-content-between p-3" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 45px; height: 45px; border-radius: 12px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(139, 92, 246, 0.2)); display: flex; align-items: center; justify-content: center; color: var(--primary-light); font-size: 1.2rem;">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div>
                                    <div style="font-size: 0.95rem; font-weight: 600; color: white;">{{ $assignment->evaluation->title }}</div>
                                    <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">{{ Str::limit($assignment->evaluation->description, 60) }}</div>
                                </div>
                            </div>
                            <a href="{{ route('evaluations.fill', $assignment->evaluation) }}" class="btn btn-primary-custom btn-sm">Completar <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                    @empty
                        <div class="p-4 text-center">
                            <i class="bi bi-check2-circle mb-2" style="font-size: 2rem; color: var(--success);"></i>
                            <div style="font-size: 0.9rem; color: #cbd5e1; font-weight: 500;">¡Estás al día!</div>
                            <div style="font-size: 0.8rem; color: #64748b;">No tienes evaluaciones pendientes de completar.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- MIS SOLICITUDES RECIENTES --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="text-secondary"><i class="bi bi-send-fill me-2"></i>Mis Solicitudes de RRHH</span>
                    <a href="{{ route('requests.index') }}" class="btn btn-outline-custom btn-sm">Ver todas</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $typeLabels = ['vacation' => 'Vacaciones', 'permission' => 'Permiso', 'work_letter' => 'Carta Laboral', 'overtime' => 'Horas Extra'];
                                @endphp
                                @forelse($myRequests as $req)
                                    <tr>
                                        <td>
                                            <span style="font-weight: 500; color: white;">{{ $typeLabels[$req->type] ?? ucfirst($req->type) }}</span>
                                        </td>
                                        <td>{{ $req->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge-status badge-{{ $req->status }}">{{ ucfirst($req->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-dark py-4">No has realizado solicitudes recientes</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            {{-- ACCESO DIRECTO A TAREAS --}}
            @if($pendingTasksCount > 0)
                <div class="card" style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.1), rgba(59, 130, 246, 0.1)); border: 1px solid rgba(59, 130, 246, 0.2);">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div class="d-flex align-items-center gap-3">
                            <div style="font-size: 2rem; color: #60a5fa;"><i class="bi bi-kanban"></i></div>
                            <div>
                                <h5 class="text-white mb-1" style="font-weight: 600;">Tienes {{ $pendingTasksCount }} tareas pendientes</h5>
                                <p class="mb-0" style="color: #94a3b8; font-size: 0.85rem;">Revisa tu tablero para organizarte y avanzar.</p>
                            </div>
                        </div>
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-custom" style="border-color: #3b82f6; color: #60a5fa;">Ir a Tareas</a>
                    </div>
                </div>
            @endif
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-4">
            
            {{-- REGLAMENTOS Y POLÍTICAS --}}
            <div class="card mb-3">
                <div class="card-header text-secondary">
                    <i class="bi bi-journal-text me-2"></i>Reglamentos
                </div>
                <div class="card-body p-0">
                    @forelse($activeRegulations as $reg)
                        <a href="{{ route('regulations.show', $reg) }}" class="d-flex align-items-center gap-3 px-3 py-3 text-decoration-none" style="border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.2s;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(255, 255, 255, 0.05); display: flex; align-items: center; justify-content: center; color: var(--accent);">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="font-size: 0.85rem; font-weight: 600; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $reg->title }}</div>
                                <div style="font-size: 0.7rem; color: #64748b;">Actualizado {{ $reg->updated_at->diffForHumans() }}</div>
                            </div>
                            <i class="bi bi-chevron-right" style="color: #475569; font-size: 0.8rem;"></i>
                        </a>
                    @empty
                        <div class="p-4 text-center text-white" style="font-size: 0.85rem;">No hay reglamentos disponibles</div>
                    @endforelse
                    <div class="p-2 text-center" style="border-top: 1px solid rgba(255,255,255,0.04);">
                        <a href="{{ route('regulations.index') }}" style="font-size: 0.8rem; color: var(--primary-light); text-decoration: none; font-weight: 500;">Explorar todos los documentos <i class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>

            {{-- AGENDA DEL DÍA --}}
            <div class="card">
                <div class="card-header text-secondary">
                    <i class="bi bi-calendar-event me-2"></i>Agenda del Día
                </div>
                <div class="card-body p-0">
                    @forelse($todayEvents as $event)
                        <div class="px-3 py-2" style="border-bottom: 1px solid rgba(255,255,255,0.04); border-left: 3px solid var(--accent);">
                            <div style="font-size: 0.7rem; color: var(--accent); font-weight: 600; margin-bottom: 2px;">
                                <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($event->event_time)->format('h:i A') }}
                            </div>
                            <div style="font-size: 0.85rem; font-weight: 600; color: white;">{{ $event->title }}</div>
                            @if($event->links && $event->links->isNotEmpty())
                                <div class="mt-1 d-flex gap-1 flex-wrap">
                                    @foreach($event->links as $link)
                                        <a href="{{ $link->url }}" target="_blank" style="font-size: 0.65rem; padding: 2px 6px; background: rgba(255,255,255,0.05); color: #94a3b8; border-radius: 4px; text-decoration: none;">
                                            <i class="bi bi-link-45deg"></i> Link
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-4 text-center">
                            <i class="bi bi-cup-hot" style="font-size: 1.5rem; color: #475569; margin-bottom: 10px; display: block;"></i>
                            <span style="font-size: 0.85rem; color: #94a3b8;">No tienes eventos programados para hoy</span>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- TODAY MODAL --}}
    @if($showTodayModal)
        <div class="today-modal-overlay" id="todayModal">
            <div class="today-modal">
                <div class="today-modal-header">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:38px;height:38px;border-radius:10px;background:var(--gradient-1);display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-calendar-check" style="color:white;font-size:1.1rem;"></i>
                        </div>
                        <div>
                            <h5 style="margin:0;font-weight:700;font-size:1rem;color:white;">Actividades de Hoy</h5>
                            <small style="color:var(--dark-4);font-size:0.7rem;">{{ now()->translatedFormat('l, d \d\e F Y') }}</small>
                        </div>
                    </div>
                    <button class="today-modal-close" onclick="closeTodayModal()"><i class="bi bi-x-lg"></i></button>
                </div>
                <div class="today-modal-body">
                    @foreach($todayEvents as $event)
                        <div class="today-event-card">
                            <div class="today-event-time"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($event->event_time)->format('h:i A') }}</div>
                            <div class="today-event-title">{{ $event->title }}</div>
                            @if($event->description)<div class="today-event-desc">{{ $event->description }}</div>@endif
                            @if($event->links->isNotEmpty())
                                <div class="today-event-links">
                                    @foreach($event->links as $link)
                                        <a href="{{ $link->url }}" target="_blank"><i class="bi bi-link-45deg"></i>{{ $link->label ?: $link->url }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="today-modal-footer">
                    <a href="{{ route('calendar.index') }}" class="btn btn-primary-custom w-100"><i class="bi bi-calendar-event me-2"></i>Ir al Calendario</a>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('styles')
<style>
    /* Same styles as index for today modal */
    .today-modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,0.65); backdrop-filter: blur(6px); z-index: 3000;
        display: flex; align-items: center; justify-content: center; animation: todayOverlayIn 0.3s ease;
    }
    @keyframes todayOverlayIn { from { opacity: 0 } to { opacity: 1 } }
    .today-modal {
        background: var(--dark-2); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px;
        width: 100%; max-width: 460px; max-height: 75vh; display: flex; flex-direction: column;
        box-shadow: 0 25px 60px rgba(0,0,0,0.5); animation: todayModalIn 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes todayModalIn { from { opacity: 0; transform: scale(0.9) translateY(20px) } to { opacity: 1; transform: scale(1) translateY(0) } }
    .today-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); }
    .today-modal-close { background: rgba(255,255,255,0.05); border: none; color: #94a3b8; font-size: 1.1rem; cursor: pointer; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
    .today-modal-close:hover { color: white; background: rgba(239,68,68,0.15); }
    .today-modal-body { padding: 1rem 1.5rem; overflow-y: auto; flex: 1; }
    .today-event-card { padding: 0.9rem 1rem; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; margin-bottom: 0.6rem; border-left: 3px solid var(--primary); }
    .today-event-time { font-size: 0.72rem; color: var(--primary-light); font-weight: 600; margin-bottom: 0.25rem; }
    .today-event-title { font-weight: 600; font-size: 0.9rem; color: white; margin-bottom: 0.2rem; }
    .today-event-desc { font-size: 0.78rem; color: #94a3b8; line-height: 1.5; }
    .today-event-links { margin-top: 0.45rem; display: flex; flex-wrap: wrap; gap: 0.35rem; }
    .today-event-links a { font-size: 0.68rem; padding: 2px 7px; border-radius: 6px; background: rgba(6,182,212,0.1); color: #67e8f9; text-decoration: none; display: inline-flex; align-items: center; gap: 3px; transition: all 0.15s; }
    .today-event-links a:hover { background: rgba(6,182,212,0.22); }
    .today-modal-footer { padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.06); }
</style>
<script>
    function closeTodayModal() {
        const modal = document.getElementById('todayModal');
        if (modal) {
            modal.style.opacity = '0';
            setTimeout(() => modal.remove(), 300);
        }
    }
</script>
@endpush
