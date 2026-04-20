@extends('layouts.app')
@section('title', 'Tablero de Tareas')
@section('page-title', 'Tablero de Tareas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
        <select id="filterStatus" class="form-select form-select-sm" style="width: auto;">
            <option value="">Todos los estados</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
            <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>Revisión</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completada</option>
        </select>
        <select id="filterPriority" class="form-select form-select-sm" style="width: auto;">
            <option value="">Todas las prioridades</option>
            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Baja</option>
            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Media</option>
            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Alta</option>
            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgente</option>
        </select>
        <select id="filterProject" class="form-select form-select-sm" style="width: auto;">
            <option value="">Todos los proyectos</option>
            @foreach($projects as $project)
            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
            @endforeach
        </select>
        <div class="btn-group ms-2" role="group">
            <button type="button" class="btn btn-outline-custom active" id="btnListView" title="Vista de Lista">
                <i class="bi bi-list-ul"></i>
            </button>
            <button type="button" class="btn btn-outline-custom" id="btnCardsView" title="Vista de Tarjetas">
                <i class="bi bi-grid-3x3-gap-fill"></i>
            </button>
        </div>
    </div>
    @if(auth()->user()->isSupervisor())
    <a href="{{ route('tasks.create') }}" class="btn btn-primary-custom">
        <i class="bi bi-plus-lg me-1"></i> Nueva Tarea
    </a>
    @endif
</div>

<div id="tasksListView">
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Tarea</th>
                        <th>Estado</th>
                        <th>Prioridad</th>
                        <th>Asignado a</th>
                        <th>Creado por</th>
                        <th>Vence</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                    <tr id="task-{{ $task->id }}">
                        <td>
                            <div class="fw-semibold">{{ $task->title }}</div>
                            @if($task->description)
                            <div style="font-size: 0.75rem; color: #64748b;">{{ Str::limit($task->description, 60) }}</div>
                            @endif
                            @if($task->project)
                            <div class="mt-1">
                                <span class="badge bg-dark-3 text-secondary small" style="font-size: 0.65rem;">
                                    <i class="bi bi-folder2-open me-1"></i> {{ $task->project->name }}
                                </span>
                            </div>
                            @endif
                        </td>
                        <td>
                            <select class="form-select form-select-sm status-select" data-task-id="{{ $task->id }}" style="width: auto; font-size: 0.75rem;">
                                <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                                <option value="review" {{ $task->status == 'review' ? 'selected' : '' }}>Revisión</option>
                                <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completada</option>
                                <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                            </select>
                        </td>
                        <td><span class="badge-status badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span></td>
                        <td>{{ $task->assignedUser->name ?? '—' }}</td>
                        <td>{{ $task->creator->name ?? '—' }}</td>
                        <td>
                            @if($task->due_date)
                                <span class="{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger' : '' }}">
                                    {{ $task->due_date->format('d/m/Y') }}
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-outline-custom btn-sm view-task" 
                                    data-title="{{ $task->title }}" 
                                    data-description="{{ $task->description }}"
                                    data-status="{{ $task->status }}"
                                    data-priority="{{ $task->priority }}"
                                    data-assigned="{{ $task->assignedUser->name ?? '—' }}"
                                    data-creator="{{ $task->creator->name ?? '—' }}"
                                    data-due="{{ $task->due_date ? $task->due_date->format('d/m/Y') : '—' }}"
                                    data-attachments="{{ $task->attachments->map(fn($a) => ['path' => Storage::url($a->file_path), 'type' => $a->file_type, 'group' => $a->uploader_group, 'user' => $a->user->name])->toJson() }}"
                                    title="Ver Detalles">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if(auth()->user()->isSupervisor())
                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-custom btn-sm" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="d-inline" onsubmit="return confirm('¿Estás seguro?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-custom btn-sm" title="Eliminar" style="color: #f87171;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay tareas registradas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<!-- Vista de Tarjetas -->
<div id="tasksCardsView" class="d-none">
    <div class="row g-4">
        @forelse($tasks as $task)
        <div class="col-md-6 col-xl-4">
            <div class="task-card-modern" id="card-task-{{ $task->id }}">
                <div class="task-card-header">
                    <span class="badge-status badge-{{ $task->priority }} ">{{ ucfirst($task->priority) }}</span>
                    <div class="dropdown">
                        <button class="btn btn-link text-white p-0" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                            <li><button class="dropdown-item view-task" 
                                    data-title="{{ $task->title }}" 
                                    data-description="{{ $task->description }}"
                                    data-status="{{ $task->status }}"
                                    data-priority="{{ $task->priority }}"
                                    data-assigned="{{ $task->assignedUser->name ?? '—' }}"
                                    data-creator="{{ $task->creator->name ?? '—' }}"
                                    data-due="{{ $task->due_date ? $task->due_date->format('d/m/Y') : '—' }}"
                                    data-attachments="{{ $task->attachments->map(fn($a) => ['path' => Storage::url($a->file_path), 'type' => $a->file_type, 'group' => $a->uploader_group, 'user' => $a->user->name])->toJson() }}">
                                    <i class="bi bi-eye me-2"></i> Ver
                                </button></li>
                            @if(auth()->user()->isSupervisor())
                            <li><a class="dropdown-item" href="{{ route('tasks.edit', $task) }}"><i class="bi bi-pencil me-2"></i> Editar</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('¿Estás seguro?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i> Eliminar</button>
                                </form>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
                
                <div class="task-card-body">
                    <h5 class="task-title">{{ $task->title }}</h5>
                    <p class="task-desc">{{ Str::limit($task->description, 100) }}</p>
                    
                    @if($task->project)
                    <div class="mb-3">
                        <span class="badge bg-dark-3 text-secondary " style="font-size: 0.7rem;">
                            <i class="bi bi-folder2-open me-1"></i> {{ $task->project->name }}
                        </span>
                    </div>
                    @endif

                    <div class="d-flex align-items-center justify-content-between mt-auto">
                        <div class="task-user">
                            <div class="user-avatar-sm">{{ strtoupper(substr($task->assignedUser->name ?? '?', 0, 1)) }}</div>
                            <span>{{ $task->assignedUser->name ?? 'Sin asignar' }}</span>
                        </div>
                        <div class="task-date {{ $task->due_date && $task->due_date->isPast() && $task->status !== 'completed' ? 'text-danger' : '' }}">
                            <i class="bi bi-calendar3 me-1"></i> {{ $task->due_date ? $task->due_date->format('d M') : '—' }}
                        </div>
                    </div>
                </div>

                <div class="task-card-footer border-top border-light border-opacity-10 mt-3 pt-3">
                    <select class="form-select form-select-sm status-select" data-task-id="{{ $task->id }}">
                        <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>En Progreso</option>
                        <option value="review" {{ $task->status == 'review' ? 'selected' : '' }}>Revisión</option>
                        <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completada</option>
                        <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-5">
            <i class="bi bi-kanban mb-3 d-block" style="font-size: 3rem; opacity: 0.2;"></i>
            No hay tareas registradas
        </div>
        @endforelse
    </div>
</div>

<div class="mt-3">{{ $tasks->links() }}</div>

<!-- Modal de Detalles -->
<div class="modal fade" id="taskDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTaskTitle">Detalles de la Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-md-8">
                        <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Descripción</label>
                        <div id="modalTaskDescription" class="p-3 rounded-3" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); min-height: 120px; white-space: pre-wrap; color: #cbd5e1; line-height: 1.6;"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-4">
                            <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Estado</label>
                            <div id="modalTaskStatus"></div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Prioridad</label>
                            <div id="modalTaskPriority"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Asignado a</label>
                            <div id="modalTaskAssigned" class="fw-semibold text-white"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Creado por</label>
                            <div id="modalTaskCreator" class="fw-semibold text-white opacity-75"></div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Vencimiento</label>
                            <div id="modalTaskDue" class="fw-semibold text-white"></div>
                        </div>
                    </div>
                    <div class="col-12 mt-4" id="modalAttachmentsSupervisor" style="display: none;">
                        <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Archivos de Supervisión</label>
                        <div class="row g-2" id="supervisorAttachmentsContent"></div>
                    </div>
                    <div class="col-12 mt-4" id="modalAttachmentsAssigned" style="display: none;">
                        <label class="form-label text-uppercase small fw-bold" style="color: #94a3b8; letter-spacing: 0.05em;">Archivos del Usuario Asignado</label>
                        <div class="row g-2" id="assignedAttachmentsContent"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .task-card-modern {
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 1.5rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .task-card-modern:hover {
        transform: translateY(-5px);
        background: rgba(30, 41, 59, 0.9);
        border-color: rgba(99, 102, 241, 0.3);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .task-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: transparent;
        transition: all 0.3s ease;
    }

    .task-card-modern:hover::before {
        background: var(--gradient-1);
    }

    .task-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
    }

    .task-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: white;
        margin-bottom: 0.75rem;
        line-height: 1.4;
    }

    .task-desc {
        font-size: 0.875rem;
        color: #94a3b8;
        line-height: 1.6;
        margin-bottom: 1.5rem;
        flex-grow: 1;
    }

    .user-avatar-sm {
        width: 28px;
        height: 28px;
        background: var(--gradient-2);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        color: white;
    }

    .task-user {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 0.8rem;
        color: #cbd5e1;
        font-weight: 500;
    }

    .task-date {
        font-size: 0.8rem;
        color: #94a3b8;
        font-weight: 500;
    }

    #tasksListView.d-none, #tasksCardsView.d-none {
        display: none !important;
    }

    .btn-group .btn-outline-custom.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // View persistence
    const currentView = localStorage.getItem('taskBoardView') || 'list';
    if (currentView === 'cards') {
        switchToCards();
    }

    $('#btnListView').on('click', function() {
        switchToList();
    });

    $('#btnCardsView').on('click', function() {
        switchToCards();
    });

    function switchToList() {
        $('#tasksListView').removeClass('d-none');
        $('#tasksCardsView').addClass('d-none');
        $('#btnListView').addClass('active');
        $('#btnCardsView').removeClass('active');
        localStorage.setItem('taskBoardView', 'list');
    }

    function switchToCards() {
        $('#tasksListView').addClass('d-none');
        $('#tasksCardsView').removeClass('d-none');
        $('#btnListView').removeClass('active');
        $('#btnCardsView').addClass('active');
        localStorage.setItem('taskBoardView', 'cards');
    }

    // Status update via AJAX
    $('.status-select').on('change', function() {
        var taskId = $(this).data('task-id');
        var status = $(this).val();

        $.ajax({
            url: '/tasks/' + taskId + '/status',
            method: 'PATCH',
            data: { status: status },
            success: function(res) {
                // Flash effect
                $('#task-' + taskId).css('background', 'rgba(99, 102, 241, 0.1)')
                    .animate({ backgroundColor: 'transparent' }, 1000);
            },
            error: function() {
                alert('Error al actualizar el estado.');
            }
        });
    });

    // Filter
    $('#filterStatus, #filterPriority, #filterProject').on('change', function() {
        var params = new URLSearchParams(window.location.search);
        var status = $('#filterStatus').val();
        var priority = $('#filterPriority').val();
        var project = $('#filterProject').val();
        if (status) params.set('status', status); else params.delete('status');
        if (priority) params.set('priority', priority); else params.delete('priority');
        if (project) params.set('project_id', project); else params.delete('project_id');
        window.location.search = params.toString();
    });

    // Show Details Modal
    $('.view-task').on('click', function() {
        const btn = $(this);
        const status = btn.data('status');
        const priority = btn.data('priority');
        
        $('#modalTaskTitle').text(btn.data('title'));
        $('#modalTaskDescription').text(btn.data('description') || 'Sin descripción');
        $('#modalTaskAssigned').text(btn.data('assigned'));
        $('#modalTaskCreator').text(btn.data('creator'));
        $('#modalTaskDue').text(btn.data('due'));

        // Badges
        const statusText = {
            'pending': 'Pendiente',
            'in_progress': 'En Progreso',
            'review': 'Revisión',
            'completed': 'Completada',
            'cancelled': 'Cancelada'
        };
        
        $('#modalTaskStatus').html(`<span class="badge-status badge-${status}">${statusText[status]}</span>`);
        $('#modalTaskPriority').html(`<span class="badge-status badge-${priority}">${priority.charAt(0).toUpperCase() + priority.slice(1)}</span>`);

        const myModal = new bootstrap.Modal(document.getElementById('taskDetailModal'));
        myModal.show();

        // Attachments handling
        const attachments = btn.data('attachments') || [];
        const $superGroup = $('#modalAttachmentsSupervisor');
        const $assignedGroup = $('#modalAttachmentsAssigned');
        const $superContent = $('#supervisorAttachmentsContent');
        const $assignedContent = $('#assignedAttachmentsContent');
        
        $superContent.empty();
        $assignedContent.empty();
        $superGroup.hide();
        $assignedGroup.hide();

        if (attachments.length > 0) {
            attachments.forEach(att => {
                const itemHtml = `
                    <div class="col-md-6 col-lg-4">
                        <div class="rounded-3 overflow-hidden border border-light border-opacity-10 position-relative h-100" style="background: rgba(0,0,0,0.2);">
                            ${att.type === 'video' 
                                ? `<video src="${att.path}" class="w-100" style="height: 120px; object-fit: cover;"></video>
                                   <div class="position-absolute top-50 start-50 translate-middle pointer-events-none">
                                       <i class="bi bi-play-circle text-white fs-1 opacity-75"></i>
                                   </div>`
                                : `<img src="${att.path}" class="w-100" style="height: 120px; object-fit: cover;">`
                            }
                            <div class="p-2 small text-center bg-dark-2">
                                <a href="${att.path}" target="_blank" class="text-white opacity-75 text-decoration-none">Ver pantalla completa</a>
                                <div class="text-muted" style="font-size: 0.65rem;">Subido por ${att.user}</div>
                            </div>
                        </div>
                    </div>
                `;

                if (att.group === 'supervisor') {
                    $superContent.append(itemHtml);
                    $superGroup.show();
                } else {
                    $assignedContent.append(itemHtml);
                    $assignedGroup.show();
                }
            });
        }
    });
});
</script>
@endpush
