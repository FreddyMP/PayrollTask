@foreach($nodes as $department)
@php
    $children = $allDepartments->where('parent_department_id', $department->id)->sortBy('name')->values();
@endphp
<li>
    <div class="org-node {{ $children->isNotEmpty() ? 'org-node--has-children' : '' }}">
        <div class="org-node-header">
            <div class="org-node-icon">
                <i class="bi bi-building"></i>
            </div>
            <div class="org-node-info">
                <h6 class="org-node-title">{{ $department->name }}</h6>
                @if($department->description)
                    <p class="org-node-desc">{{ Str::limit($department->description, 60) }}</p>
                @endif
            </div>
        </div>
        <div class="org-node-stats">
            <span style="cursor: pointer; text-decoration: underline;" data-bs-toggle="collapse" data-bs-target="#employees-{{ $department->id }}" aria-expanded="false" aria-controls="employees-{{ $department->id }}">
                <i class="bi bi-people me-1"></i>{{ $department->employees_count }} empleado(s)
            </span>
            <span><i class="bi bi-briefcase me-1"></i>{{ $department->positions_count }} posición(es)</span>
        </div>
        @if($department->employees->isNotEmpty())
            <div class="collapse" id="employees-{{ $department->id }}">
                <div class="org-node-employees mt-2">
                    @foreach($department->employees->take(5) as $employee)
                        <div class="org-employee">
                            <span class="org-employee-avatar">{{ strtoupper(substr($employee->user->name ?? '?', 0, 2)) }}</span>
                            <div>
                                <span class="org-employee-name">{{ $employee->user->name ?? '—' }}</span>
                                <small class="org-employee-role">{{ $employee->position?->title ?? $employee->user->position ?? 'Sin cargo' }}</small>
                            </div>
                        </div>
                    @endforeach
                    @if($department->employees_count > 5)
                        <small class="org-employee-more">+{{ $department->employees_count - 5 }} más</small>
                    @endif
                </div>
            </div>
        @endif
        <div class="org-node-actions">
            <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-outline-custom">
                <i class="bi bi-pencil"></i> Editar
            </a>
        </div>
    </div>
    @if($children->isNotEmpty())
        <ul>
            @include('org-chart._node', ['nodes' => $children, 'allDepartments' => $allDepartments])
        </ul>
    @endif
</li>
@endforeach
