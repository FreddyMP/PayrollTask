@extends('layouts.app')
@section('title', 'Control de Fichaje')
@section('page-title', 'Control de Fichaje')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #mapContainer {
            height: 380px;
            width: 100%;
            border-radius: 10px;
            z-index: 1;
        }
        .map-btn-in {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            transition: opacity 0.2s, transform 0.15s;
        }
        .map-btn-out {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
            transition: opacity 0.2s, transform 0.15s;
        }
        .map-btn-in:hover, .map-btn-out:hover {
            opacity: 0.85;
            transform: scale(1.05);
        }
        .no-location {
            color: #475569;
            font-size: 0.78rem;
        }
        .coords-pill {
            display: block;
            font-size: 0.68rem;
            color: #64748b;
            margin-top: 1px;
            font-family: monospace;
        }
        .location-cell {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .location-entry {
            display: flex;
            flex-direction: column;
        }
        .badge-presencial {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        .badge-remoto {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(168, 85, 247, 0.15);
            color: #c084fc;
            border: 1px solid rgba(168, 85, 247, 0.3);
        }
    </style>
@endpush

@section('content')
    {{-- Filtros --}}
    <div class="card mb-4" style="background-color: #1e293b; border: 1px solid #334155;">
        <div class="card-body">
            <form method="GET" action="{{ route('fichajes.index') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size: 0.875rem; color: #94a3b8;">
                            <i class="bi bi-calendar me-1"></i>Fecha
                        </label>
                        <input type="date" class="form-control" name="date"
                            value="{{ request('date', now()->toDateString()) }}"
                            style="background-color: #0f172a; border-color: #334155; color: #e2e8f0;">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-search me-1"></i>Buscar
                        </button>
                        <a href="{{ route('fichajes.index') }}" class="btn btn-outline-custom">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Hoy
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Resultados --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Empleado</th>
                            <th>Modalidad</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Horas de Descanso</th>
                            <th>Total Horas Trab.</th>
                            <th>Ubicación Entrada</th>
                            <th>Ubicación Salida</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fichajes as $fichaje)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--gradient-2); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: white; font-weight: 700;">
                                            {{ strtoupper(substr($fichaje->employee->user->name ?? '', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $fichaje->employee->user->name ?? '—' }}</div>
                                            <div style="font-size: 0.75rem; color: #64748b;">
                                                ID: {{ $fichaje->employee->id_number ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if(($fichaje->work_mode ?? 'presencial') === 'presencial')
                                        <span class="badge-presencial">
                                            <i class="bi bi-building"></i> Presencial
                                        </span>
                                    @else
                                        <span class="badge-remoto">
                                            <i class="bi bi-laptop"></i> Remoto
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center text-success">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        {{ \Carbon\Carbon::parse($fichaje->clock_in)->format('h:i A') }}
                                    </div>
                                </td>
                                <td>
                                    @if($fichaje->clock_out)
                                        <div class="d-flex align-items-center text-danger">
                                            <i class="bi bi-box-arrow-left me-2"></i>
                                            {{ \Carbon\Carbon::parse($fichaje->clock_out)->format('h:i A') }}
                                        </div>
                                    @else
                                        <span class="badge-status badge-pending">Sin salida</span>
                                    @endif
                                </td>
                                <td>
                                    @if($fichaje->break_start && $fichaje->break_end)
                                        <div class="text-info" style="font-size: 0.85rem;">
                                            <i class="bi bi-cup-hot me-1"></i>
                                            {{ \Carbon\Carbon::parse($fichaje->break_start)->format('h:i A') }} -
                                            {{ \Carbon\Carbon::parse($fichaje->break_end)->format('h:i A') }}
                                        </div>
                                    @else
                                        <span style="color: #64748b; font-size: 0.85rem;">Sin descanso</span>
                                    @endif
                                </td>
                                <td style="font-weight: 600;">
                                    @if($fichaje->clock_out)
                                        {{ $fichaje->total_hours }} hrs
                                    @else
                                        —
                                    @endif
                                </td>

                                {{-- Ubicación Entrada --}}
                                <td>
                                    @if($fichaje->clock_in_latitude && $fichaje->clock_in_longitude)
                                        <div class="location-entry">
                                            <button class="map-btn-in"
                                                onclick="openMapModal(
                                                    {{ $fichaje->clock_in_latitude }},
                                                    {{ $fichaje->clock_in_longitude }},
                                                    '{{ addslashes($fichaje->employee->user->name ?? 'Empleado') }}',
                                                    'Entrada: {{ \Carbon\Carbon::parse($fichaje->clock_in)->format('d/m/Y h:i A') }}',
                                                    'in'
                                                )"
                                                title="Ver ubicación de entrada">
                                                <i class="bi bi-geo-alt-fill"></i> Ver mapa
                                            </button>
                                            <span class="coords-pill">
                                                {{ number_format($fichaje->clock_in_latitude, 5) }},
                                                {{ number_format($fichaje->clock_in_longitude, 5) }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="no-location"><i class="bi bi-geo-alt me-1"></i>Sin datos</span>
                                    @endif
                                </td>

                                {{-- Ubicación Salida --}}
                                <td>
                                    @if($fichaje->clock_out_latitude && $fichaje->clock_out_longitude)
                                        <div class="location-entry">
                                            <button class="map-btn-out"
                                                onclick="openMapModal(
                                                    {{ $fichaje->clock_out_latitude }},
                                                    {{ $fichaje->clock_out_longitude }},
                                                    '{{ addslashes($fichaje->employee->user->name ?? 'Empleado') }}',
                                                    'Salida: {{ \Carbon\Carbon::parse($fichaje->clock_out)->format('d/m/Y h:i A') }}',
                                                    'out'
                                                )"
                                                title="Ver ubicación de salida">
                                                <i class="bi bi-geo-alt-fill"></i> Ver mapa
                                            </button>
                                            <span class="coords-pill">
                                                {{ number_format($fichaje->clock_out_latitude, 5) }},
                                                {{ number_format($fichaje->clock_out_longitude, 5) }}
                                            </span>
                                        </div>
                                    @elseif($fichaje->clock_out)
                                        <span class="no-location"><i class="bi bi-geo-alt me-1"></i>Sin datos</span>
                                    @else
                                        <span class="no-location">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if($fichaje->clock_out)
                                        <span class="badge-status badge-completed">Completado</span>
                                    @else
                                        <span class="badge-status badge-in-progress">En Turno</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-secondary py-4">No hay registros de fichaje para esta fecha</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal de Mapa --}}
    <div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="background: #1e293b; border: 1px solid #334155;">
                <div class="modal-header" style="border-bottom: 1px solid #334155;">
                    <div>
                        <h5 class="modal-title text-white mb-0" id="mapModalLabel">
                            <i id="mapModalIcon" class="bi bi-geo-alt-fill me-2"></i>
                            <span id="mapModalName"></span>
                        </h5>
                        <small class="text-secondary" id="mapModalTime"></small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div id="mapContainer"></div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <span class="text-secondary" style="font-size: 0.8rem;">
                            <i class="bi bi-pin-map me-1"></i>Coordenadas:
                            <code id="mapCoords" style="color: #a5b4fc;"></code>
                        </span>
                        <a id="mapExternalLink" href="#" target="_blank" class="btn btn-sm btn-outline-custom">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Abrir en Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map = null;

        // type: 'in' = entrada (verde), 'out' = salida (rojo)
        function openMapModal(lat, lng, employeeName, label, type) {
            const isIn = type === 'in';
            const color     = isIn ? '#10b981' : '#ef4444';
            const iconClass = isIn ? 'bi-box-arrow-in-right text-success' : 'bi-box-arrow-left text-danger';

            document.getElementById('mapModalIcon').className  = `bi ${iconClass} me-2`;
            document.getElementById('mapModalName').textContent = employeeName;
            document.getElementById('mapModalTime').textContent = label;
            document.getElementById('mapCoords').textContent    = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            document.getElementById('mapExternalLink').href     = `https://www.google.com/maps?q=${lat},${lng}`;

            const modal = new bootstrap.Modal(document.getElementById('mapModal'));
            modal.show();

            document.getElementById('mapModal').addEventListener('shown.bs.modal', function initMap() {
                if (map !== null) { map.remove(); map = null; }

                map = L.map('mapContainer').setView([lat, lng], 16);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    maxZoom: 19,
                }).addTo(map);

                const customIcon = L.divIcon({
                    html: `<div style="
                        background: ${color};
                        width: 34px; height: 34px;
                        border-radius: 50% 50% 50% 0;
                        transform: rotate(-45deg);
                        border: 3px solid #fff;
                        box-shadow: 0 4px 14px ${color}88;
                    "></div>`,
                    className: '',
                    iconSize: [34, 34],
                    iconAnchor: [17, 34],
                    popupAnchor: [0, -36],
                });

                L.marker([lat, lng], { icon: customIcon })
                    .addTo(map)
                    .bindPopup(`
                        <div style="font-family: sans-serif; min-width: 155px;">
                            <strong style="color: #1e293b;">${employeeName}</strong><br>
                            <span style="font-size: 0.78rem; color: #64748b;">${label}</span><br>
                            <span style="font-size: 0.72rem; color: ${color}; font-family: monospace;">
                                ${lat.toFixed(6)}, ${lng.toFixed(6)}
                            </span>
                        </div>
                    `)
                    .openPopup();

                this.removeEventListener('shown.bs.modal', initMap);
            });
        }

        document.getElementById('mapModal').addEventListener('hidden.bs.modal', function () {
            if (map !== null) { map.remove(); map = null; }
        });
    </script>
@endpush
