@extends('layouts.app')
@section('title', 'Calendario')
@section('page-title', 'Calendario')

@section('content')
@push('styles')
<style>
    .calendar-wrapper {
        background: var(--dark-2);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px;
        overflow: hidden;
    }

    .calendar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .calendar-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .calendar-nav {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .calendar-nav button {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.1);
        background: transparent;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .calendar-nav button:hover {
        background: rgba(99, 102, 241, 0.15);
        border-color: var(--primary-light);
        color: white;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
    }

    .calendar-day-name {
        padding: 0.75rem 0.5rem;
        text-align: center;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--dark-4);
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }

    .calendar-day {
        min-height: 100px;
        padding: 0.5rem;
        border: 1px solid rgba(255,255,255,0.03);
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .calendar-day:hover {
        background: rgba(99, 102, 241, 0.05);
    }

    .calendar-day.other-month {
        opacity: 0.3;
    }

    .calendar-day.today {
        background: rgba(99, 102, 241, 0.08);
    }

    .calendar-day.today .day-number {
        background: var(--gradient-1);
        color: white;
        border-radius: 8px;
        padding: 2px 8px;
    }

    .day-number {
        font-size: 0.85rem;
        font-weight: 600;
        color: #cbd5e1;
        margin-bottom: 0.35rem;
    }

    .day-event {
        font-size: 0.68rem;
        padding: 3px 6px;
        border-radius: 6px;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.15s ease;
    }

    .day-event:hover {
        transform: scale(1.02);
    }

    .day-event.own {
        background: rgba(99, 102, 241, 0.2);
        color: #a5b4fc;
        border-left: 2px solid var(--primary);
    }

    .day-event.other {
        background: rgba(6, 182, 212, 0.15);
        color: #67e8f9;
        border-left: 2px solid var(--accent);
    }

    .day-more {
        font-size: 0.65rem;
        color: var(--dark-4);
        padding: 2px 6px;
        cursor: pointer;
        font-weight: 500;
    }

    .day-more:hover { color: var(--primary-light); }

    /* Event Detail Modal */
    .event-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        z-index: 2000;
        align-items: center;
        justify-content: center;
    }

    .event-modal-overlay.show {
        display: flex;
    }

    .event-modal {
        background: var(--dark-2);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 16px;
        width: 100%;
        max-width: 520px;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 25px 60px rgba(0,0,0,0.4);
        animation: modalIn 0.25s ease;
    }

    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }

    .event-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .event-modal-header h5 {
        font-weight: 700;
        font-size: 1rem;
        color: white;
        margin: 0;
    }

    .event-modal-close {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 1.25rem;
        cursor: pointer;
        padding: 4px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .event-modal-close:hover {
        color: white;
        background: rgba(255,255,255,0.05);
    }

    .event-modal-body {
        padding: 1.25rem 1.5rem;
    }

    .event-item {
        padding: 1rem;
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 12px;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }

    .event-item:hover {
        border-color: rgba(99, 102, 241, 0.2);
        background: rgba(99, 102, 241, 0.03);
    }

    .event-item-title {
        font-weight: 600;
        font-size: 0.9rem;
        color: white;
        margin-bottom: 0.25rem;
    }

    .event-item-time {
        font-size: 0.75rem;
        color: var(--primary-light);
        font-weight: 500;
        margin-bottom: 0.35rem;
    }

    .event-item-desc {
        font-size: 0.8rem;
        color: #94a3b8;
        margin-bottom: 0.5rem;
        line-height: 1.5;
    }

    .event-item-user {
        font-size: 0.7rem;
        color: var(--dark-4);
    }

    .event-item-links {
        margin-top: 0.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
    }

    .event-item-links a {
        font-size: 0.7rem;
        padding: 3px 8px;
        border-radius: 6px;
        background: rgba(6, 182, 212, 0.12);
        color: #67e8f9;
        text-decoration: none;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .event-item-links a:hover {
        background: rgba(6, 182, 212, 0.25);
    }

    .event-item-actions {
        display: flex;
        gap: 0.4rem;
        margin-top: 0.5rem;
    }

    .event-item-actions a,
    .event-item-actions button {
        font-size: 0.7rem;
        padding: 3px 10px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.15s ease;
        text-decoration: none;
    }

    .btn-edit-event {
        background: rgba(99, 102, 241, 0.15);
        color: #a5b4fc;
    }

    .btn-edit-event:hover {
        background: rgba(99, 102, 241, 0.3);
        color: white;
    }

    .btn-delete-event {
        background: rgba(239, 68, 68, 0.12);
        color: #f87171;
    }

    .btn-delete-event:hover {
        background: rgba(239, 68, 68, 0.25);
        color: white;
    }

    .add-event-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.75rem;
        color: var(--primary-light);
        background: rgba(99,102,241,0.1);
        border: 1px dashed rgba(99,102,241,0.3);
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        font-weight: 500;
        width: 100%;
        justify-content: center;
    }

    .add-event-btn:hover {
        background: rgba(99,102,241,0.2);
        border-color: var(--primary);
        color: white;
    }

    .view-toggle {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 1rem;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.1);
        background: transparent;
        color: #94a3b8;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .view-toggle:hover {
        border-color: var(--primary-light);
        color: white;
        background: rgba(99,102,241,0.1);
    }

    .view-toggle.active {
        background: rgba(6, 182, 212, 0.15);
        border-color: var(--accent);
        color: #67e8f9;
    }

    @media (max-width: 768px) {
        .calendar-day { min-height: 65px; padding: 0.25rem; }
        .day-number { font-size: 0.75rem; }
        .day-event { font-size: 0.6rem; padding: 2px 4px; }
        .event-modal { margin: 1rem; }
    }
</style>
@endpush

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="text-white mb-1" style="font-weight:700;">
            <i class="bi bi-calendar-event-fill me-2" style="color:var(--primary-light)"></i>Calendario de Actividades
        </h5>
        <p class="mb-0" style="font-size:0.85rem;color:var(--dark-4)">Registra y gestiona tus actividades por fecha</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if(auth()->user()->role !== 'usuario')
        <button class="view-toggle" id="viewToggle" onclick="toggleView()" title="Ver actividades del equipo">
            <i class="bi bi-people-fill"></i> <span id="viewToggleText">Ver equipo</span>
        </button>
        @endif
        <a href="{{ route('calendar.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i> Nueva Actividad
        </a>
    </div>
</div>

<div class="calendar-wrapper">
    <div class="calendar-header">
        <div class="calendar-nav">
            <button onclick="changeMonth(-1)" title="Mes anterior"><i class="bi bi-chevron-left"></i></button>
        </div>
        <h3 id="calendarTitle"></h3>
        <div class="calendar-nav">
            <button onclick="changeMonth(1)" title="Mes siguiente"><i class="bi bi-chevron-right"></i></button>
            <button onclick="goToday()" title="Hoy" style="width:auto;padding:0 12px;font-size:0.75rem;font-weight:600;">Hoy</button>
        </div>
    </div>
    <div class="calendar-grid" id="calendarGrid"></div>
</div>

<!-- Event Detail Modal -->
<div class="event-modal-overlay" id="eventModal">
    <div class="event-modal">
        <div class="event-modal-header">
            <h5 id="modalTitle"></h5>
            <button class="event-modal-close" onclick="closeModal()"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="event-modal-body" id="modalBody"></div>
    </div>
</div>

@push('scripts')
<script>
    const MONTHS = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    const DAYS   = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
    const today  = new Date();
    let currentYear  = today.getFullYear();
    let currentMonth = today.getMonth(); // 0-indexed
    let eventsCache  = {};
    let currentView  = 'mine'; // 'mine' or 'team'

    function toggleView() {
        currentView = currentView === 'mine' ? 'team' : 'mine';
        const btn = document.getElementById('viewToggle');
        const txt = document.getElementById('viewToggleText');
        if (currentView === 'team') {
            btn.classList.add('active');
            txt.textContent = 'Mis actividades';
        } else {
            btn.classList.remove('active');
            txt.textContent = 'Ver equipo';
        }
        loadCalendar();
    }

    function changeMonth(dir) {
        currentMonth += dir;
        if (currentMonth < 0)  { currentMonth = 11; currentYear--; }
        if (currentMonth > 11) { currentMonth = 0;  currentYear++; }
        loadCalendar();
    }

    function goToday() {
        currentYear  = today.getFullYear();
        currentMonth = today.getMonth();
        loadCalendar();
    }

    function loadCalendar() {
        document.getElementById('calendarTitle').textContent = MONTHS[currentMonth] + ' ' + currentYear;
        fetch(`{{ route('calendar.apiEvents') }}?year=${currentYear}&month=${currentMonth + 1}&view=${currentView}`)
            .then(r => r.json())
            .then(events => {
                eventsCache = {};
                events.forEach(e => {
                    if (!eventsCache[e.day]) eventsCache[e.day] = [];
                    eventsCache[e.day].push(e);
                });
                renderGrid();
            });
    }

    function renderGrid() {
        const grid = document.getElementById('calendarGrid');
        let html = '';

        // Day names
        DAYS.forEach(d => { html += `<div class="calendar-day-name">${d}</div>`; });

        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const daysInPrev  = new Date(currentYear, currentMonth, 0).getDate();

        // Previous month padding
        for (let i = firstDay - 1; i >= 0; i--) {
            html += `<div class="calendar-day other-month"><div class="day-number">${daysInPrev - i}</div></div>`;
        }

        // Current month days
        for (let d = 1; d <= daysInMonth; d++) {
            const isToday = (d === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear());
            const dateStr = `${currentYear}-${String(currentMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const dayEvents = eventsCache[d] || [];

            html += `<div class="calendar-day ${isToday ? 'today' : ''}" onclick="dayClick(${d}, '${dateStr}')">`;
            html += `<div class="day-number">${d}</div>`;

            const maxShow = 2;
            dayEvents.slice(0, maxShow).forEach(e => {
                const cls = e.is_owner ? 'own' : 'other';
                html += `<div class="day-event ${cls}" title="${e.time} — ${e.title}">${e.time.split(' ')[0]} ${e.title}</div>`;
            });
            if (dayEvents.length > maxShow) {
                html += `<div class="day-more">+${dayEvents.length - maxShow} más</div>`;
            }

            html += '</div>';
        }

        // Next month padding
        const totalCells = firstDay + daysInMonth;
        const remaining = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
        for (let i = 1; i <= remaining; i++) {
            html += `<div class="calendar-day other-month"><div class="day-number">${i}</div></div>`;
        }

        grid.innerHTML = html;
    }

    function dayClick(day, dateStr) {
        const dayEvents = eventsCache[day] || [];
        const modal = document.getElementById('eventModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');

        modalTitle.textContent = `${day} de ${MONTHS[currentMonth]}, ${currentYear}`;

        let html = '';
        if (dayEvents.length > 0) {
            dayEvents.forEach(e => {
                html += `<div class="event-item">`;
                html += `<div class="event-item-time"><i class="bi bi-clock me-1"></i>${e.time}</div>`;
                html += `<div class="event-item-title">${e.title}</div>`;
                if (e.description) {
                    html += `<div class="event-item-desc">${e.description}</div>`;
                }
                html += `<div class="event-item-user"><i class="bi bi-person me-1"></i>${e.user}</div>`;

                if (e.links && e.links.length > 0) {
                    html += `<div class="event-item-links">`;
                    e.links.forEach(l => {
                        html += `<a href="${l.url}" target="_blank"><i class="bi bi-link-45deg"></i>${l.label}</a>`;
                    });
                    html += `</div>`;
                }

                if (e.is_owner) {
                    html += `<div class="event-item-actions">`;
                    html += `<a href="/calendar/${e.id}/edit" class="btn-edit-event"><i class="bi bi-pencil me-1"></i>Editar</a>`;
                    html += `<form method="POST" action="/calendar/${e.id}" style="display:inline" onsubmit="return confirm('¿Eliminar esta actividad?')">`;
                    html += `@csrf @method('DELETE')`;
                    html += `<button type="submit" class="btn-delete-event"><i class="bi bi-trash me-1"></i>Eliminar</button>`;
                    html += `</form>`;
                    html += `</div>`;
                }

                html += `</div>`;
            });
        }

        html += `<a href="{{ route('calendar.create') }}?date=${dateStr}" class="add-event-btn mt-2"><i class="bi bi-plus-circle"></i> Agregar actividad</a>`;

        modalBody.innerHTML = html;
        modal.classList.add('show');
    }

    function closeModal() {
        document.getElementById('eventModal').classList.remove('show');
    }

    // Close modal on overlay click
    document.getElementById('eventModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    // Initial load
    loadCalendar();
</script>
@endpush
@endsection
