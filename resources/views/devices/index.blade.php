@extends('layouts.app')
@section('title', 'Dispositivos')
@section('page-title', 'Gestión de Dispositivos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="text-white small">
        <i class="bi bi-info-circle me-1"></i> Registre las IPs de los dispositivos autorizados para identificarlos en los registros de acceso.
    </div>
    <a href="{{ route('devices.create') }}" class="btn btn-primary-custom">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Dispositivo
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Nombre del Dispositivo</th>
                        <th>Dirección IP</th>
                        <th>Descripción</th>
                        <th>Fecha Registro</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $device)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3 sm" style="width: 32px; height: 32px; background: rgba(99, 102, 241, 0.1); color: var(--primary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-laptop"></i>
                                </div>
                                <span class="fw-semibold">{{ $device->name }}</span>
                            </div>
                        </td>
                        <td><code>{{ $device->ip_address }}</code></td>
                        <td class="text-muted small">{{ Str::limit($device->description, 50) }}</td>
                        <td>{{ $device->created_at->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('devices.edit', $device) }}" class="btn btn-outline-custom btn-sm" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('devices.destroy', $device) }}" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este dispositivo?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-custom btn-sm" title="Eliminar" style="color: #f87171;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-cpu mb-3 d-block" style="font-size: 3rem; opacity: 0.2;"></i>
                            No hay dispositivos registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $devices->links() }}</div>
@endsection
