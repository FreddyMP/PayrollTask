<x-mail::message>
# Actualización de tu Solicitud

Hola {{ $userRequest->user->name }},

Tu solicitud de **{{ ucfirst(str_replace('_', ' ', $userRequest->type)) }}** ha sido revisada.

**Estado Actual:** {{ ucfirst($userRequest->status) }}

@if($userRequest->admin_notes)
**Notas del Administrador:**
{{ $userRequest->admin_notes }}
@endif

<x-mail::button :url="route('requests.index')">
Ver Mis Solicitudes
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
