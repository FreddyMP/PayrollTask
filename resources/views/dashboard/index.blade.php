@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
{{-- KPI CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card purple">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-value">{{ $totalEmployees }}</div>
            <div class="stat-label">Empleados Activos</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-value">{{ $payrollSummaryLatest ? number_format($payrollSummaryLatest->total_net, 0, '.', ',') : '0' }}</div>
            <div class="stat-label">Nómina Neta {{ $latestPeriod ?? '' }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card blue">
            <div class="stat-icon"><i class="bi bi-briefcase-fill"></i></div>
            <div class="stat-value">{{ $openVacancies }}</div>
            <div class="stat-label">Vacantes Abiertas</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card orange">
            <div class="stat-icon"><i class="bi bi-person-badge-fill"></i></div>
            <div class="stat-value">{{ $activeCandidates }}</div>
            <div class="stat-label">Candidatos Activos</div>
        </div>
    </div>
</div>

{{-- MAIN CONTENT --}}
<div class="row g-3 mb-4">
    {{-- LEFT: PAYROLL --}}
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-secondary"><i class="bi bi-receipt-cutoff me-2"></i>Historial de Nómina</span>
                <a href="{{ route('payroll.index') }}" class="btn btn-outline-custom btn-sm">Ver nómina</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Período</th>
                                <th>Empleados</th>
                                <th>Bruto</th>
                                <th>Deducciones</th>
                                <th>Neto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrollHistory as $ph)
                            <tr>
                                <td class="fw-semibold">{{ $ph->period }}</td>
                                <td>{{ $ph->employee_count }}</td>
                                <td>RD$ {{ number_format($ph->total_gross, 2) }}</td>
                                <td style="color:#f87171;">RD$ {{ number_format($ph->total_deductions, 2) }}</td>
                                <td style="color:#34d399;font-weight:600;">RD$ {{ number_format($ph->total_net, 2) }}</td>
                                <td>
                                    @if($ph->pending_count > 0)
                                        <span class="badge-status badge-pending">{{ $ph->pending_count }} pend.</span>
                                    @endif
                                    @if($ph->paid_count > 0)
                                        <span class="badge-status badge-paid">{{ $ph->paid_count }} pag.</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-white py-4">Sin registros de nómina</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- SALARY DISTRIBUTION --}}
        <div class="card mb-3">
            <div class="card-header text-secondary"><i class="bi bi-bar-chart-fill me-2"></i>Distribución Salarial & Deducciones</div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between mb-2" style="font-size:0.78rem;color:#94a3b8;">
                            <span>Mínimo</span><span>Promedio</span><span>Máximo</span>
                        </div>
                        @php
                            $min = $salaryStats->min_salary ?? 0;
                            $max = $salaryStats->max_salary ?? 1;
                            $avg = $salaryStats->avg_salary ?? 0;
                            $pct = $max > $min ? (($avg - $min) / ($max - $min)) * 100 : 50;
                        @endphp
                        <div class="dash-salary-bar">
                            <div class="dash-salary-avg" style="left:{{ $pct }}%;" title="Promedio"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1" style="font-size:0.8rem;font-weight:600;">
                            <span style="color:#818cf8;">RD$ {{ number_format($min, 0) }}</span>
                            <span style="color:#34d399;">RD$ {{ number_format($avg, 0) }}</span>
                            <span style="color:#fbbf24;">RD$ {{ number_format($max, 0) }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 text-white">
                        <div style="font-size:0.75rem;color:#94a3b8;margin-bottom:0.5rem;font-weight:600;">DESGLOSE PROMEDIO DEDUCCIONES</div>
                        
                        @if($deductionBreakdown)
                        
                            @php
                                $dItems = [
                                    ['label' => 'ARS (SFS)', 'val' => $deductionBreakdown->avg_ars, 'color' => '#818cf8'],
                                    ['label' => 'AFP', 'val' => $deductionBreakdown->avg_afp, 'color' => '#06b6d4'],
                                    ['label' => 'ISR', 'val' => $deductionBreakdown->avg_isr, 'color' => '#f59e0b'],
                                    ['label' => 'Otros', 'val' => $deductionBreakdown->avg_otros, 'color' => '#ef4444'],
                                ];
                                $dTotal = array_sum(array_column($dItems, 'val')) ?: 1;
                            @endphp
                            @foreach($dItems as $d)
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div style="width:10px;height:10px;border-radius:3px;background:{{ $d['color'] }};flex-shrink:0;"></div>
                                <span style="font-size:0.8rem;width:70px;">{{ $d['label'] }}</span>
                                <div style="flex:1;height:8px;background:rgba(255,255,255,0.06);border-radius:4px;overflow:hidden;">
                                    <div style="height:100%;width:{{ ($d['val']/$dTotal)*100 }}%;background:{{ $d['color'] }};border-radius:4px;transition:width 0.6s ease;"></div>
                                </div>
                                <span style="font-size:0.78rem;font-weight:600;color:{{ $d['color'] }};min-width:75px;text-align:right;">RD$ {{ number_format($d['val'], 0) }}</span>
                            </div>
                            @endforeach
                        @else
                            <p  style="font-size:0.85rem;">Sin datos de deducciones</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- PIPELINE DE RECLUTAMIENTO --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-secondary"><i class="bi bi-funnel-fill me-2"></i>Pipeline de Reclutamiento</span>
                <a href="{{ route('recruitment.index') }}" class="btn btn-outline-custom btn-sm">Ver vacantes</a>
            </div>
            <div class="card-body">
                @forelse($vacancyPipeline as $v)
                <div class="dash-pipeline-item">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div style="font-weight:700;font-size:0.9rem;color:white;">{{ $v->title }}</div>
                            <div style="font-size:0.72rem;color:#64748b;">{{ $v->department ?: 'Sin depto.' }} · {{ $v->steps_count }} pasos</div>
                        </div>
                        <div class="d-flex gap-1">
                            <span class="badge-status badge-active" style="font-size:0.6rem;">{{ $v->candidates_count }} cand.</span>
                            @if($v->hired_count > 0)
                            <span class="badge-status badge-completed" style="font-size:0.6rem;">{{ $v->hired_count }} contrat.</span>
                            @endif
                            @if($v->discarded_count > 0)
                            <span class="badge-status badge-rejected" style="font-size:0.6rem;">{{ $v->discarded_count }} desc.</span>
                            @endif
                        </div>
                    </div>
                    @php
                        $total = $v->candidates_count ?: 1;
                        $hiredPct = ($v->hired_count / $total) * 100;
                        $discardedPct = ($v->discarded_count / $total) * 100;
                        $activePct = 100 - $hiredPct - $discardedPct;
                    @endphp
                    <div class="dash-pipeline-bar">
                        <div style="width:{{ $hiredPct }}%;background:var(--success);"></div>
                        <div style="width:{{ $activePct }}%;background:var(--primary);"></div>
                        <div style="width:{{ $discardedPct }}%;background:var(--danger);opacity:0.6;"></div>
                    </div>
                </div>
                @empty
                <p class="text-white text-center py-4" style="font-size:0.85rem;">No hay vacantes abiertas</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- RIGHT: RECRUITMENT + HR --}}
    <div class="col-lg-4">
        {{-- Solicitudes RRHH --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-secondary"><i class="bi bi-send-fill me-2"></i>Solicitudes RRHH</span>
                <span class="badge-status badge-pending">{{ $pendingRequests }} pend.</span>
            </div>
            <div class="card-body py-2 px-3 text-white">
                @php
                    $typeLabels = ['vacation'=>'Vacaciones','permission'=>'Permisos','work_letter'=>'Carta Laboral','overtime'=>'Horas Extra'];
                    $typeIcons = ['vacation'=>'bi-sun-fill','permission'=>'bi-calendar2-check','work_letter'=>'bi-file-earmark-text','overtime'=>'bi-alarm-fill'];
                    $typeColors = ['vacation'=>'#22d3ee','permission'=>'#fbbf24','work_letter'=>'#c084fc','overtime'=>'#fb923c'];
                @endphp
                @forelse($requestsByType as $type => $statuses)
                <div class="d-flex align-items-center gap-2 py-2" style="border-bottom:1px solid rgba(255,255,255,0.04);">
                    <div style="width:30px;height:30px;border-radius:8px;background:rgba(255,255,255,0.04);display:flex;align-items:center;justify-content:center;">
                        <i class="bi {{ $typeIcons[$type] ?? 'bi-question-circle' }}" style="color:{{ $typeColors[$type] ?? '#94a3b8' }};font-size:0.85rem;"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:0.8rem;font-weight:600;">{{ $typeLabels[$type] ?? ucfirst($type) }}</div>
                    </div>
                    @foreach(['pending'=>'badge-pending','approved'=>'badge-approved','rejected'=>'badge-rejected'] as $st => $cls)
                        @if(isset($statuses[$st]) && $statuses[$st] > 0)
                        <span class="badge-status {{ $cls }}" style="font-size:0.6rem;">{{ $statuses[$st] }}</span>
                        @endif
                    @endforeach
                </div>
                @empty
                <p class="text-white text-center py-3" style="font-size:0.85rem;">Sin solicitudes</p>
                @endforelse
                <a href="{{ route('requests.index') }}" class="btn btn-outline-custom btn-sm w-100 mt-2">Ver solicitudes</a>
            </div>
        </div>

        {{-- Departamentos --}}
        <div class="card mb-3">
            <div class="card-header text-secondary"><i class="bi bi-diagram-3-fill me-2"></i>Empleados por Departamento</div>
            <div class="card-body py-2 px-3 text-white">
                @php $maxDept = $departmentDistribution->max('count') ?: 1; @endphp
                @forelse($departmentDistribution as $dept)
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span style="font-size:0.8rem;width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $dept->department }}">{{ $dept->department }}</span>
                    <div style="flex:1;height:8px;background:rgba(255,255,255,0.06);border-radius:4px;overflow:hidden;">
                        <div style="height:100%;width:{{ ($dept->count/$maxDept)*100 }}%;background:var(--gradient-1);border-radius:4px;"></div>
                    </div>
                    <span style="font-size:0.78rem;font-weight:700;color:white;min-width:20px;text-align:right;">{{ $dept->count }}</span>
                </div>
                @empty
                <p class="text-white text-center py-2" style="font-size:0.85rem;">Sin departamentos</p>
                @endforelse
            </div>
        </div>

        {{-- Accesos Recientes --}}
        <div class="card mb-3">
            <div class="card-header text-secondary"><i class="bi bi-clock-history me-2"></i>Accesos Recientes</div>
            <div class="card-body p-0">
                @forelse($recentAccess as $log)
                <div class="d-flex align-items-center gap-3 px-3 py-2" style="border-bottom:1px solid rgba(255,255,255,0.04);">
                    <div style="width:30px;height:30px;border-radius:8px;background:var(--gradient-1);display:flex;align-items:center;justify-content:center;font-size:0.65rem;color:white;font-weight:700;">
                        {{ strtoupper(substr($log->user->name ?? '', 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-size:0.78rem;font-weight:600;color:white;">{{ $log->user->name ?? '' }}</div>
                        <div style="font-size:0.68rem;color:#64748b;">{{ $log->login_at->format('d/m H:i') }} — {{ $log->logout_at ? $log->logout_at->format('H:i') : 'Activo' }}</div>
                    </div>
                </div>
                @empty
                <div class="p-3 text-center text-white" style="font-size:0.85rem;">Sin registros</div>
                @endforelse
            </div>
        </div>

        {{-- Candidatos Recientes --}}
        <div class="card mb-3">
            <div class="card-header text-secondary"><i class="bi bi-person-lines-fill me-2"></i>Candidatos Recientes</div>
            <div class="card-body p-0">
                @forelse($recentCandidates as $c)
                <div class="d-flex align-items-center gap-3 px-3 py-2" style="border-bottom:1px solid rgba(255,255,255,0.04);">
                    <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#06b6d4,#3b82f6);display:flex;align-items:center;justify-content:center;font-size:0.65rem;color:white;font-weight:700;">
                        {{ strtoupper(substr($c->name, 0, 2)) }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.8rem;font-weight:600;color:white;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $c->name }}</div>
                        <div style="font-size:0.68rem;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $c->vacancy->title ?? '' }}</div>
                    </div>
                    <span class="badge-status badge-{{ $c->status }}">{{ $c->status }}</span>
                </div>
                @empty
                <div class="p-3 text-center text-white" style="font-size:0.85rem;">Sin candidatos</div>
                @endforelse
            </div>
        </div>

        {{-- Contrataciones Recientes --}}
        <div class="card">
            <div class="card-header text-secondary"><i class="bi bi-person-check-fill me-2"></i>Contrataciones Recientes</div>
            <div class="card-body p-0">
                @forelse($recentHires as $h)
                <div class="d-flex align-items-center gap-3 px-3 py-2" style="border-bottom:1px solid rgba(255,255,255,0.04);">
                    <div style="width:32px;height:32px;border-radius:8px;background:var(--gradient-3);display:flex;align-items:center;justify-content:center;font-size:0.65rem;color:white;font-weight:700;">
                        {{ strtoupper(substr($h->name, 0, 2)) }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:0.8rem;font-weight:600;color:white;">{{ $h->name }}</div>
                        <div style="font-size:0.68rem;color:#64748b;">{{ $h->vacancy->title ?? '' }}</div>
                    </div>
                    <span style="font-size:0.68rem;color:#34d399;"><i class="bi bi-check-circle-fill me-1"></i>Contratado</span>
                </div>
                @empty
                <div class="p-3 text-center text-white" style="font-size:0.85rem;">Sin contrataciones</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- TODAY MODAL (kept from original) --}}
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
                    <small style="color:var(--dark-4);font-size:0.7rem;">{{ now()->translatedFormat('l, d \\d\\e F Y') }}</small>
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

@push('styles')
<style>
    .dash-salary-bar{position:relative;height:10px;background:linear-gradient(90deg,#818cf8,#34d399,#fbbf24);border-radius:5px;margin:0.5rem 0;}
    .dash-salary-avg{position:absolute;top:-4px;width:4px;height:18px;background:white;border-radius:2px;transform:translateX(-50%);box-shadow:0 0 8px rgba(255,255,255,0.5);}
    .dash-pipeline-item{padding:1rem;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:12px;margin-bottom:0.6rem;transition:all 0.2s;}
    .dash-pipeline-item:hover{background:rgba(99,102,241,0.04);border-color:rgba(99,102,241,0.15);}
    .dash-pipeline-bar{display:flex;height:6px;border-radius:3px;overflow:hidden;gap:1px;}
    .dash-pipeline-bar>div{transition:width 0.6s ease;}
    .today-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(6px);z-index:3000;display:flex;align-items:center;justify-content:center;animation:todayOverlayIn 0.3s ease;}
    @keyframes todayOverlayIn{from{opacity:0}to{opacity:1}}
    .today-modal{background:var(--dark-2);border:1px solid rgba(255,255,255,0.1);border-radius:20px;width:100%;max-width:460px;max-height:75vh;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,0.5);animation:todayModalIn 0.35s cubic-bezier(0.16,1,0.3,1);}
    @keyframes todayModalIn{from{opacity:0;transform:scale(0.9) translateY(20px)}to{opacity:1;transform:scale(1) translateY(0)}}
    .today-modal-header{display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid rgba(255,255,255,0.06);}
    .today-modal-close{background:rgba(255,255,255,0.05);border:none;color:#94a3b8;font-size:1.1rem;cursor:pointer;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;transition:all 0.2s;}
    .today-modal-close:hover{color:white;background:rgba(239,68,68,0.15);}
    .today-modal-body{padding:1rem 1.5rem;overflow-y:auto;flex:1;}
    .today-event-card{padding:0.9rem 1rem;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:12px;margin-bottom:0.6rem;border-left:3px solid var(--primary);}
    .today-event-time{font-size:0.72rem;color:var(--primary-light);font-weight:600;margin-bottom:0.25rem;}
    .today-event-title{font-weight:600;font-size:0.9rem;color:white;margin-bottom:0.2rem;}
    .today-event-desc{font-size:0.78rem;color:#94a3b8;line-height:1.5;}
    .today-event-links{margin-top:0.45rem;display:flex;flex-wrap:wrap;gap:0.35rem;}
    .today-event-links a{font-size:0.68rem;padding:2px 7px;border-radius:6px;background:rgba(6,182,212,0.1);color:#67e8f9;text-decoration:none;display:inline-flex;align-items:center;gap:3px;transition:all 0.15s;}
    .today-event-links a:hover{background:rgba(6,182,212,0.22);}
    .today-modal-footer{padding:1rem 1.5rem;border-top:1px solid rgba(255,255,255,0.06);}
    .badge-hired{background:rgba(16,185,129,0.15);color:#34d399;}
    .badge-discarded{background:rgba(239,68,68,0.15);color:#f87171;}
</style>
@endpush

@if($showTodayModal)
@push('scripts')
<script>
    function closeTodayModal(){const m=document.getElementById('todayModal');m.style.opacity='0';m.style.transition='opacity 0.25s ease';setTimeout(()=>m.remove(),250);}
    document.getElementById('todayModal').addEventListener('click',function(e){if(e.target===this)closeTodayModal();});
    document.addEventListener('keydown',function(e){if(e.key==='Escape')closeTodayModal();});
</script>
@endpush
@endif

@endsection
