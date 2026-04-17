<x-mail::message>
# Hola, {{ $task->assignedUser->name }}

Se te ha asignado una nueva tarea: **"{{ $task->title }}"**.

**Prioridad:** {{ $task->priority }}
**Fecha de vencimiento:** {{ $task->due_date ? $task->due_date->format('d/m/Y') : 'N/A' }}

<x-mail::button :url="config('app.url') . '/tasks/' . $task->id . '/edit'">
Ver Tarea
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
