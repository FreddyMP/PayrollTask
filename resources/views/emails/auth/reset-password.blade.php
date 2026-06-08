<x-mail::message>
# Hola,

Si recibiste este correo es porque solicitaste un cambio de contraseña para tu cuenta en **PayrollTask**.

<x-mail::button :url="url(route('password.reset', ['token' => $token, 'email' => $email], false))">
Restablecer Contraseña
</x-mail::button>

Este enlace de restablecimiento de contraseña expirará en 60 minutos.

Si no realizaste esta solicitud, no es necesario realizar ninguna otra acción.

Gracias,<br>
{{ $companyName }}
</x-mail::message>
