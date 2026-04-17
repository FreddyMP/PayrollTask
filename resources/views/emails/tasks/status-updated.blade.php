<x-mail::message>
# Hola, {{ $task->assignedUser->name }}

El estado de la tarea **"{{ $task->title }}"** ha sido actualizado a: **{{ $task->status }}**.

<x-mail::button :url="config('app.url') . '/tasks/' . $task->id . '/edit'">
Ver Tarea
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
