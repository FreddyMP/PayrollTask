@extends('layouts.app')
@section('title', 'Solicitudes')
@section('page-title', 'Solicitudes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
        <select id="filterType" class="form-select form-select-sm" style="width: auto;" onchange="filterRequests()">
            <option value="">Todos los tipos</option>
            <option value="vacation" {{ request('type') == 'vacation' ? 'selected' : '' }}>Vacaciones</option>
            <option value="permission" {{ request('type') == 'permission' ? 'selected' : '' }}>Permisos</option>
            <option value="work_letter" {{ request('type') == 'work_letter' ? 'selected' : '' }}>Carta de Trabajo</option>
            <option value="overtime" {{ request('type') == 'overtime' ? 'selected' : '' }}>Horas Extra</option>
        </select>
        <select id="filterStatus" class="form-select form-select-sm" style="width: auto;" onchange="filterRequests()">
            <option value="">Todos los estados</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprobada</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rechazada</option>
        </select>
    </div>
    <a href="{{ route('requests.create') }}" class="btn btn-primary-custom">
        <i class="bi bi-plus-lg me-1"></i> Nueva Solicitud
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Solicitante</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Fechas / Horas</th>
                        <th>Aprobador</th>
                        <th>Adjuntos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td class="fw-semibold">{{ $req->user->name ?? '—' }}</td>
                        <td>
                            @php
                                $typeLabels = [
                                    'vacation'    => 'Vacaciones',
                                    'permission'  => 'Permiso',
                                    'work_letter' => 'Carta de Trabajo',
                                    'overtime'    => 'Horas Extra',
                                ];
                            @endphp
                            <span class="badge-status badge-{{ $req->type }}">{{ $typeLabels[$req->type] ?? $req->type }}</span>
                        </td>
                        <td>
                            @php
                                $statusLabels = ['pending' => 'Pendiente', 'approved' => 'Aprobada', 'rejected' => 'Rechazada'];
                            @endphp
                            <span class="badge-status badge-{{ $req->status }}">{{ $statusLabels[$req->status] ?? $req->status }}</span>
                        </td>
                        <td>
                            @if($req->type === 'overtime')
                                @if($req->overtime_date)
                                    <div class="fw-semibold" style="font-size:0.85rem;">{{ $req->overtime_date->format('d/m/Y') }}</div>
                                    @if($req->overtime_start && $req->overtime_end)
                                        <div class="text-muted" style="font-size:0.78rem;">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($req->overtime_start)->format('H:i') }}
                                            —
                                            {{ \Carbon\Carbon::parse($req->overtime_end)->format('H:i') }}
                                        </div>
                                    @endif
                                    @if($req->overtime_hours)
                                        <span class="badge-status badge-overtime" style="font-size:0.68rem;">
                                            <i class="bi bi-hourglass-split me-1"></i>{{ $req->overtime_hours }} h
                                        </span>
                                    @endif
                                @else
                                    —
                                @endif
                            @else
                                @if($req->start_date)
                                    {{ $req->start_date->format('d/m/Y') }}
                                    @if($req->end_date)
                                        — {{ $req->end_date->format('d/m/Y') }}
                                    @endif
                                @else
                                    —
                                @endif
                            @endif
                        </td>
                        <td>
                            @if($req->type === 'overtime' && $req->approvedBy)
                                <div class="d-flex align-items-center gap-1">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width:24px;height:24px;background:rgba(251,146,60,0.2);font-size:0.65rem;font-weight:700;color:#fb923c;flex-shrink:0;">
                                        {{ strtoupper(substr($req->approvedBy->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div style="font-size:0.8rem;font-weight:600;">{{ $req->approvedBy->name }}</div>
                                        <div style="font-size:0.68rem;color:#64748b;">{{ ucfirst($req->approvedBy->role) }}</div>
                                    </div>
                                </div>
                            @elseif($req->reviewer)
                                <span style="font-size:0.75rem;color:#64748b;">
                                    <i class="bi bi-person-check me-1"></i>{{ $req->reviewer->name }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($req->attachments->count() > 0)
                                <button class="btn btn-outline-custom btn-sm view-request-attachments"
                                    data-attachments="{{ $req->attachments->map(fn($a) => ['path' => Storage::url($a->file_path), 'type' => $a->file_type, 'user' => $a->user->name])->toJson() }}"
                                    title="Ver Adjuntos">
                                    <i class="bi bi-paperclip"></i> {{ $req->attachments->count() }}
                                </button>
                            @else
                                <span class="text-muted small">Sin adjuntos</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-outline-custom btn-sm" onclick="showRequestDetails('{{ addslashes($req->description) }}')" title="Ver Descripción">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                                @if(auth()->user()->isSupervisor() && $req->status === 'pending')
                                <button class="btn btn-outline-custom btn-sm" style="color: #34d399;" onclick="reviewRequest({{ $req->id }}, 'approved')" title="Aprobar">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button class="btn btn-outline-custom btn-sm" style="color: #f87171;" onclick="reviewRequest({{ $req->id }}, 'rejected')" title="Rechazar">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                @endif
                                @if($req->reviewer && $req->type !== 'overtime')
                                <span style="font-size: 0.7rem; color: #64748b;" title="Revisado por {{ $req->reviewer->name }}">
                                    <i class="bi bi-person-check"></i> {{ $req->reviewer->name }}
                                </span>
                                @endif
                                <form method="POST" action="{{ route('requests.destroy', $req) }}" onsubmit="return confirm('¿Eliminar solicitud?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 ms-1"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay solicitudes</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $requests->links() }}</div>

<!-- Attachments Modal -->
<div class="modal fade" id="requestAttachmentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Archivos Adjuntos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3" id="requestAttachmentsContent"></div>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="requestDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Descripción de la Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="requestDetailContent" class="p-3 rounded bg-dark-2 text-white opacity-75" style="white-space: pre-wrap;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<form id="reviewForm" method="POST" style="display: none;">
    @csrf @method('PATCH')
    <input type="hidden" name="status" id="reviewStatus">
    <input type="hidden" name="admin_notes" id="reviewNotes">
</form>
@endsection

@push('scripts')
<script>
function showRequestDetails(content) {
    document.getElementById('requestDetailContent').textContent = content || 'Sin descripción';
    new bootstrap.Modal(document.getElementById('requestDetailModal')).show();
}

$('.view-request-attachments').on('click', function() {
    const attachments = $(this).data('attachments');
    const $content = $('#requestAttachmentsContent');
    $content.empty();

    attachments.forEach(att => {
        const itemHtml = `
            <div class="col-md-6">
                <div class="rounded-3 overflow-hidden border border-light border-opacity-10 position-relative h-100" style="background: rgba(0,0,0,0.2);">
                    ${att.type === 'video'
                        ? `<video src="${att.path}" class="w-100" style="height: 150px; object-fit: cover;" controls></video>`
                        : `<img src="${att.path}" class="w-100" style="height: 150px; object-fit: cover;">`
                    }
                    <div class="p-2 small text-center bg-dark-2">
                        <a href="${att.path}" target="_blank" class="text-white opacity-75 text-decoration-none">Ver pantalla completa</a>
                        <div class="text-muted" style="font-size: 0.65rem;">Subido por ${att.user}</div>
                    </div>
                </div>
            </div>
        `;
        $content.append(itemHtml);
    });

    new bootstrap.Modal(document.getElementById('requestAttachmentsModal')).show();
});

function filterRequests() {
    var params = new URLSearchParams(window.location.search);
    var type = document.getElementById('filterType').value;
    var status = document.getElementById('filterStatus').value;
    if (type) params.set('type', type); else params.delete('type');
    if (status) params.set('status', status); else params.delete('status');
    window.location.search = params.toString();
}

function reviewRequest(id, status) {
    var labels = { approved: 'aprobar', rejected: 'rechazar' };
    var notes = prompt('Notas (opcional) para ' + labels[status] + ' esta solicitud:');
    if (notes === null) return;

    var form = document.getElementById('reviewForm');
    form.action = '/requests/' + id + '/review';
    document.getElementById('reviewStatus').value = status;
    document.getElementById('reviewNotes').value = notes;
    form.submit();
}
</script>
@endpush
