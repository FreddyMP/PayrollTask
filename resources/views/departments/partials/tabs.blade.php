@php
    $activeTab = $activeTab ?? 'departments';
@endphp
<ul class="nav nav-tabs mb-4 px-3" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'departments' ? 'active' : '' }}"
           href="{{ route('departments.index') }}"
           style="{{ $activeTab === 'departments' ? 'color: white; border-bottom: 2px solid var(--primary);' : 'color: #94a3b8;' }} background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
            Departamentos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'positions' ? 'active' : '' }}"
           href="{{ route('positions.index') }}"
           style="{{ $activeTab === 'positions' ? 'color: white; border-bottom: 2px solid var(--primary);' : 'color: #94a3b8;' }} background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
            Posiciones
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'org-chart' ? 'active' : '' }}"
           href="{{ route('org-chart.index') }}"
           style="{{ $activeTab === 'org-chart' ? 'color: white; border-bottom: 2px solid var(--primary);' : 'color: #94a3b8;' }} background: transparent; border-top: 0; border-left: 0; border-right: 0; padding: 0.75rem 1.25rem;">
            Organigrama
        </a>
    </li>
</ul>
