@extends('layouts.app')

@section('title', 'Resultados de Evaluación')
@section('page-title', 'Resultados: ' . $evaluation->title)

@section('content')
{{-- Header --}}
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="text-white mb-1">Dashboard de Resultados</h5>
            <p class="small mb-0">Total de respuestas: <span class="fw-bold text-white">{{ $evaluation->responses->count() }}</span> de {{ $evaluation->assignments->count() }} asignados</p>
        </div>
        <a href="{{ route('evaluations.show', $evaluation) }}" class="btn btn-outline-custom">
            <i class="bi bi-arrow-left me-2"></i>Volver a Gestión
        </a>
    </div>
</div>

@php
    $totalResponses = $evaluation->responses->count();
    $totalAssignments = $evaluation->assignments->count();
    $completionRate = $totalAssignments > 0 ? round(($totalResponses / $totalAssignments) * 100) : 0;

    $scaleQuestions = $evaluation->questions->where('type', 'scale');
    $booleanQuestions = $evaluation->questions->where('type', 'boolean');
    $textQuestions = $evaluation->questions->whereIn('type', ['text', 'textarea']);

    // Calculate overall average for scale questions
    $overallScaleAvg = 0;
    if ($scaleQuestions->count() > 0 && $totalResponses > 0) {
        $totalScale = 0;
        $scaleCount = 0;
        foreach ($evaluation->responses as $response) {
            foreach ($scaleQuestions as $sq) {
                $answer = $response->answers->where('evaluation_question_id', $sq->id)->first();
                if ($answer && $answer->answer_scale !== null) {
                    $totalScale += $answer->answer_scale;
                    $scaleCount++;
                }
            }
        }
        $overallScaleAvg = $scaleCount > 0 ? round($totalScale / $scaleCount, 1) : 0;
    }
@endphp

{{-- KPI Cards --}}
<div class="row mb-4 g-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background: rgba(52, 152, 219, 0.2);">
                    <i class="bi bi-people-fill text-primary fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Participación</div>
                    <div class="text-white fw-bold fs-4">{{ $completionRate }}%</div>
                    <div class="text-muted" style="font-size: 0.7rem;">{{ $totalResponses }}/{{ $totalAssignments }} empleados</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background: rgba(46, 204, 113, 0.2);">
                    <i class="bi bi-question-circle-fill text-success fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Preguntas</div>
                    <div class="text-white fw-bold fs-4">{{ $evaluation->questions->count() }}</div>
                    <div class="text-muted" style="font-size: 0.7rem;">{{ $scaleQuestions->count() }} escala · {{ $booleanQuestions->count() }} sí/no · {{ $textQuestions->count() }} texto</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background: rgba(241, 196, 15, 0.2);">
                    <i class="bi bi-star-fill text-warning fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Promedio General</div>
                    <div class="text-white fw-bold fs-4">{{ $overallScaleAvg }}<span class="fs-6 text-muted">/10</span></div>
                    <div class="text-muted" style="font-size: 0.7rem;">Preguntas tipo escala</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background: rgba(155, 89, 182, 0.2);">
                    <i class="bi bi-clipboard-check-fill fs-4" style="color: #9b59b6;"></i>
                </div>
                <div>
                    <div class="text-muted small">Estado</div>
                    <div class="text-white fw-bold fs-5 text-capitalize">{{ $evaluation->status }}</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Última respuesta: {{ $evaluation->responses->last()?->created_at?->diffForHumans() ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row mb-4 g-3">
    {{-- Scale Questions Bar Chart --}}
    @if($scaleQuestions->count() > 0)
    <div class="{{ $booleanQuestions->count() > 0 ? 'col-lg-7' : 'col-lg-12' }}">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-dark-2 py-3 border-0">
                <i class="bi bi-bar-chart-fill me-2 text-primary"></i>
                <span class="text-white fw-bold">Promedio por Pregunta (Escala)</span>
            </div>
            <div class="card-body" style="min-height: 300px;">
                <div class="chart-container" style="position: relative; height:300px;">
                    <canvas id="scaleBarChart" style="width:100%;height:100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Boolean Questions Doughnut Charts --}}
    @if($booleanQuestions->count() > 0)
    <div class="{{ $scaleQuestions->count() > 0 ? 'col-lg-5' : 'col-lg-12' }}">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-dark-2 py-3 border-0">
                <i class="bi bi-pie-chart-fill me-2 text-info"></i>
                <span class="text-white fw-bold">Respuestas Sí / No</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($booleanQuestions as $bq)
                        @php
                            $yesCount = 0;
                            $noCount = 0;
                            foreach ($evaluation->responses as $response) {
                                $answer = $response->answers->where('evaluation_question_id', $bq->id)->first();
                                if ($answer && $answer->answer_boolean !== null) {
                                    if ($answer->answer_boolean) { $yesCount++; } else { $noCount++; }
                                }
                            }
                        @endphp
                        <div class="{{ $booleanQuestions->count() === 1 ? 'col-12' : 'col-sm-6' }}">
                            <div class="text-center">
                                 <div class="chart-container" style="position: relative; height:180px;">
                                    <canvas id="boolChart_{{ $bq->id }}" style="width:100%;height:100%;"></canvas>
                                 </div>
                                <p class="text-muted small mt-2 mb-0 px-2" title="{{ $bq->question_text }}">{{ Str::limit($bq->question_text, 40) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Score Distribution + Participation Chart --}}
<div class="row mb-4 g-3">
    @if($scaleQuestions->count() > 0)
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-dark-2 py-3 border-0">
                <i class="bi bi-graph-up me-2 text-warning"></i>
                <span class="text-white fw-bold">Distribución de Puntuaciones</span>
            </div>
            <div class="card-body" style="min-height: 280px;">
                <div class="chart-container" style="position: relative; height:300px;">
                    <canvas id="scoreDistributionChart" style="width:100%;height:100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="{{ $scaleQuestions->count() > 0 ? 'col-lg-6' : 'col-lg-12' }}">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-dark-2 py-3 border-0">
                <i class="bi bi-person-check-fill me-2 text-success"></i>
                <span class="text-white fw-bold">Participación por Empleado</span>
            </div>
            <div class="card-body" style="min-height: 280px;">
                <div class="chart-container" style="position: relative; height:300px;">
                    <canvas id="participationChart" style="width:100%;height:100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Per-Employee Radar Chart (if scale questions exist and there are responses) --}}
@if($scaleQuestions->count() >= 3 && $totalResponses > 0)
<div class="row mb-4 g-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark-2 py-3 border-0 d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-diagram-3-fill me-2 text-info"></i>
                    <span class="text-white fw-bold">Comparativa por Empleado (Radar)</span>
                </div>
                <select id="radarEmployeeSelector" class="form-select form-select-sm" style="width: auto; min-width: 200px;">
                    <option value="all">Todos los empleados</option>
                    @foreach($evaluation->responses as $response)
                        <option value="{{ $response->id }}">{{ $response->employee->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="card-body d-flex justify-content-center" style="min-height: 350px;">
                <div style="max-width: 600px; width: 100%;">
                    <div class="chart-container" style="position: relative; height:400px;">
                    <canvas id="radarChart" style="width:100%;height:100%;"></canvas>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Detailed Responses Table --}}
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark-2 py-3 border-0">
                <i class="bi bi-table me-2"></i>
                <span class="text-white fw-bold">Detalle de Respuestas</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-dark-2">
                            <tr>
                                <th>Empleado</th>
                                <th>Fecha</th>
                                @foreach($evaluation->questions as $question)
                                    <th>P{{ $loop->iteration }} <span class=" fw-normal" style="font-size: 0.7rem;" title="{{ $question->question_text }}">({{ Str::limit($question->question_text, 15) }})</span></th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($evaluation->responses as $response)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary-light  rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            {{ substr($response->employee->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="d-block ">{{ $response->employee->user->name }}</span>
                                            <span class="small ">{{ $response->employee->department }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted small">{{ $response->created_at->format('d/m/Y H:i') }}</td>
                                @foreach($evaluation->questions as $question)
                                    @php
                                        $answer = $response->answers->where('evaluation_question_id', $question->id)->first();
                                    @endphp
                                    <td>
                                        @if($answer)
                                            @if($question->type === 'scale')
                                                <span class="badge {{ $answer->answer_scale >= 7 ? 'bg-success' : ($answer->answer_scale >= 4 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                    {{ $answer->answer_scale }} / 10
                                                </span>
                                            @elseif($question->type === 'boolean')
                                                @if($answer->answer_boolean)
                                                    <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Sí</span>
                                                @else
                                                    <span class="badge bg-danger"><i class="bi bi-x-lg me-1"></i>No</span>
                                                @endif
                                            @else
                                                <span class="d-inline-block text-truncate text-white" style="max-width: 150px;" title="{{ $answer->answer_text }}">
                                                    {{ $answer->answer_text }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted fst-italic small">N/A</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 2 + $evaluation->questions->count() }}" class="text-center py-5 text-muted">
                                    Aún no hay respuestas para esta evaluación.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartTextColor = '#adb5bd';
    const gridColor = 'rgba(255,255,255,0.06)';

    Chart.defaults.color = chartTextColor;
    Chart.defaults.borderColor = gridColor;

    // ─── Scale Bar Chart ───
    @if($scaleQuestions->count() > 0)
    (function() {
        const labels = @json($scaleQuestions->values()->map(fn($q) => 'P' . $q->order . ': ' . Str::limit($q->question_text, 25)));
        const avgData = [];
        const bgColors = [];
        const borderColors = [];

        @foreach($scaleQuestions as $sq)
            @php
                $vals = [];
                foreach ($evaluation->responses as $r) {
                    $a = $r->answers->where('evaluation_question_id', $sq->id)->first();
                    if ($a && $a->answer_scale !== null) $vals[] = $a->answer_scale;
                }
                $avg = count($vals) > 0 ? round(array_sum($vals) / count($vals), 1) : 0;
            @endphp
            avgData.push({{ $avg }});
            @if($avg >= 7)
                bgColors.push('rgba(46, 204, 113, 0.7)');
                borderColors.push('rgba(46, 204, 113, 1)');
            @elseif($avg >= 4)
                bgColors.push('rgba(241, 196, 15, 0.7)');
                borderColors.push('rgba(241, 196, 15, 1)');
            @else
                bgColors.push('rgba(231, 76, 60, 0.7)');
                borderColors.push('rgba(231, 76, 60, 1)');
            @endif
        @endforeach

        new Chart(document.getElementById('scaleBarChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Promedio',
                    data: avgData,
                    backgroundColor: bgColors,
                    borderColor: borderColors,
                    borderWidth: 1,
                    borderRadius: 6,
                    barPercentage: 0.6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => 'Promedio: ' + ctx.parsed.y + ' / 10'
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, max: 10, grid: { color: gridColor } },
                    x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 0, font: { size: 11 } } }
                }
            }
        });
    })();
    @endif

    // ─── Boolean Doughnut Charts ───
    @foreach($booleanQuestions as $bq)
    @php
        $yes = 0; $no = 0;
        foreach ($evaluation->responses as $r) {
            $a = $r->answers->where('evaluation_question_id', $bq->id)->first();
            if ($a && $a->answer_boolean !== null) {
                if ($a->answer_boolean) { $yes++; } else { $no++; }
            }
        }
    @endphp
    (function() {
        new Chart(document.getElementById('boolChart_{{ $bq->id }}'), {
            type: 'doughnut',
            data: {
                labels: ['Sí', 'No'],
                datasets: [{
                    data: [{{ $yes }}, {{ $no }}],
                    backgroundColor: ['rgba(46, 204, 113, 0.8)', 'rgba(231, 76, 60, 0.8)'],
                    borderColor: ['rgba(46, 204, 113, 1)', 'rgba(231, 76, 60, 1)'],
                    borderWidth: 2,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '60%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, pointStyle: 'circle', font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? Math.round((ctx.parsed / total) * 100) : 0;
                                return ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    })();
    @endforeach

    // ─── Score Distribution Chart ───
    @if($scaleQuestions->count() > 0)
    (function() {
        const distribution = [0,0,0,0,0,0,0,0,0,0]; // index 0 = score 1, index 9 = score 10
        @foreach($evaluation->responses as $response)
            @foreach($scaleQuestions as $sq)
                @php
                    $a = $response->answers->where('evaluation_question_id', $sq->id)->first();
                @endphp
                @if($a && $a->answer_scale !== null)
                    distribution[{{ $a->answer_scale }} - 1]++;
                @endif
            @endforeach
        @endforeach

        new Chart(document.getElementById('scoreDistributionChart'), {
            type: 'line',
            data: {
                labels: ['1','2','3','4','5','6','7','8','9','10'],
                datasets: [{
                    label: 'Frecuencia',
                    data: distribution,
                    fill: true,
                    backgroundColor: 'rgba(52, 152, 219, 0.15)',
                    borderColor: 'rgba(52, 152, 219, 0.9)',
                    borderWidth: 2,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(52, 152, 219, 1)',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: gridColor } },
                    x: { title: { display: true, text: 'Puntuación', color: chartTextColor }, grid: { display: false } }
                }
            }
        });
    })();
    @endif

    // ─── Participation Horizontal Bar Chart ───
    (function() {
        const empLabels = [];
        const empStatus = [];
        const empColors = [];

        @foreach($evaluation->assignments as $assignment)
            empLabels.push('{{ $assignment->employee->user->name }}');
            empStatus.push({{ $assignment->is_completed ? 1 : 0 }});
            empColors.push({{ $assignment->is_completed ? "'rgba(46, 204, 113, 0.7)'" : "'rgba(241, 196, 15, 0.7)'" }});
        @endforeach

        new Chart(document.getElementById('participationChart'), {
            type: 'bar',
            data: {
                labels: empLabels,
                datasets: [{
                    label: 'Estado',
                    data: empStatus,
                    backgroundColor: empColors,
                    borderRadius: 6,
                    barPercentage: 0.5,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.parsed.x === 1 ? 'Completado' : 'Pendiente'
                        }
                    }
                },
                scales: {
                    x: {
                        display: false,
                        max: 1,
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 12 } }
                    }
                }
            }
        });
    })();

    // ─── Radar Chart ───
    @if($scaleQuestions->count() >= 3 && $totalResponses > 0)
    (function() {
        const radarLabels = @json($scaleQuestions->values()->map(fn($q) => 'P' . $q->order));
        const radarColors = [
            'rgba(52, 152, 219, 0.8)',
            'rgba(46, 204, 113, 0.8)',
            'rgba(231, 76, 60, 0.8)',
            'rgba(241, 196, 15, 0.8)',
            'rgba(155, 89, 182, 0.8)',
            'rgba(230, 126, 34, 0.8)',
            'rgba(26, 188, 156, 0.8)',
            'rgba(192, 57, 43, 0.8)',
        ];

        const allDatasets = [];
        @foreach($evaluation->responses as $idx => $response)
        (function() {
            const data = [];
            @foreach($scaleQuestions as $sq)
                @php
                    $a = $response->answers->where('evaluation_question_id', $sq->id)->first();
                @endphp
                data.push({{ $a && $a->answer_scale !== null ? $a->answer_scale : 0 }});
            @endforeach

            allDatasets.push({
                id: {{ $response->id }},
                label: '{{ $response->employee->user->name }}',
                data: data,
                borderColor: radarColors[{{ $idx }} % radarColors.length],
                backgroundColor: radarColors[{{ $idx }} % radarColors.length].replace('0.8', '0.1'),
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5,
            });
        })();
        @endforeach

        const radarCtx = document.getElementById('radarChart');
        const radarChart = new Chart(radarCtx, {
            type: 'radar',
            data: {
                labels: radarLabels,
                datasets: allDatasets,
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true, pointStyle: 'circle', font: { size: 11 } } }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 10,
                        ticks: { stepSize: 2, backdropColor: 'transparent', color: chartTextColor },
                        grid: { color: gridColor },
                        angleLines: { color: gridColor },
                        pointLabels: { color: chartTextColor, font: { size: 12 } }
                    }
                }
            }
        });

        // Filter by employee
        document.getElementById('radarEmployeeSelector').addEventListener('change', function() {
            const val = this.value;
            if (val === 'all') {
                radarChart.data.datasets = allDatasets;
            } else {
                radarChart.data.datasets = allDatasets.filter(ds => ds.id == val);
            }
            radarChart.update();
        });
    })();
    @endif
});
</script>
@endsection
