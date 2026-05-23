<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nueva Evaluación Asignada</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { background-color: #2c3e50; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 600; }
        .content { padding: 40px 30px; color: #333333; line-height: 1.6; }
        .content p { margin-top: 0; font-size: 16px; }
        .button-container { text-align: center; margin-top: 30px; margin-bottom: 20px; }
        .button { display: inline-block; padding: 14px 28px; background-color: #3498db; color: #ffffff !important; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 16px; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 13px; color: #777777; border-top: 1px solid #eeeeee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nueva Evaluación Pendiente</h1>
        </div>
        <div class="content">
            <p>Hola <strong>{{ $user->name }}</strong>,</p>
            <p>Se te ha asignado una nueva evaluación de personal: <strong>{{ $evaluation->title }}</strong>.</p>
            @if($evaluation->description)
                <p><em>{{ $evaluation->description }}</em></p>
            @endif
            <p>Por favor, tómate unos minutos para completarla. Tus respuestas son importantes para el proceso de mejora continua.</p>
            
            <div class="button-container">
                <a href="{{ route('evaluations.fill', $evaluation->id) }}" class="button">Completar Evaluación</a>
            </div>
            
            <p style="font-size: 14px; color: #555;">Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:<br>
            <a href="{{ route('evaluations.fill', $evaluation->id) }}">{{ route('evaluations.fill', $evaluation->id) }}</a></p>
        </div>
        <div class="footer">
            <p>Este es un correo automático. Por favor no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>
