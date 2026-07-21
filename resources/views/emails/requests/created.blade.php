<x-mail::message>
# Nueva Solicitud de {{ $userRequest->user->name }}

Se ha registrado una nueva solicitud en el sistema.

**Detalles de la Solicitud:**
- **Tipo:** {{ ucfirst(str_replace('_', ' ', $userRequest->type)) }}
- **Fecha de Inicio:** {{ $userRequest->start_date?->format('d/m/Y') ?? 'N/A' }}
- **Fecha de Fin:** {{ $userRequest->end_date?->format('d/m/Y') ?? 'N/A' }}
- **Descripción:** {{ $userRequest->description ?? 'Sin descripción' }}
@if($userRequest->attachments->count() > 0)
- **Adjuntos:**
@foreach($userRequest->attachments as $attachment)
  - [{{ $attachment->file_type === 'video' ? '🎥 Ver Video Adjunto' : '🖼️ Ver Imagen Adjunta' }}]({{ \Illuminate\Support\Facades\Storage::disk(config('filesystems.default'))->url($attachment->file_path) }})
@endforeach
@endif

<x-mail::button :url="route('requests.index')">
Ver Solicitudes
</x-mail::button>

Gracias,<br>
{{ $companyName }}
</x-mail::message>
