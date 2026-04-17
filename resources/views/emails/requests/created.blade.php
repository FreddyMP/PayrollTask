<x-mail::message>
# Nueva Solicitud de {{ $userRequest->user->name }}

Se ha registrado una nueva solicitud en el sistema.

**Detalles de la Solicitud:**
- **Tipo:** {{ ucfirst(str_replace('_', ' ', $userRequest->type)) }}
- **Fecha de Inicio:** {{ $userRequest->start_date?->format('d/m/Y') ?? 'N/A' }}
- **Fecha de Fin:** {{ $userRequest->end_date?->format('d/m/Y') ?? 'N/A' }}
- **Descripción:** {{ $userRequest->description ?? 'Sin descripción' }}
@if($userRequest->attachments->count() > 0)
- **Adjuntos:** {{ $userRequest->attachments->count() }} archivos (imágenes/videos)
@endif

<x-mail::button :url="route('requests.index')">
Ver Solicitudes
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
