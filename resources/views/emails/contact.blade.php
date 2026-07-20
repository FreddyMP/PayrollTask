<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Mensaje de Contacto</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #0056b3;">Nuevo Mensaje de Contacto</h2>
        <p>Se ha recibido un nuevo mensaje de contacto con los siguientes detalles:</p>
        
        <div style="background-color: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <p style="margin: 5px 0;"><strong>Nombre:</strong> {{ $name }}</p>
            <p style="margin: 5px 0;"><strong>Teléfono:</strong> {{ $phone }}</p>
        </div>

        <h3 style="color: #444; border-bottom: 1px solid #eee; padding-bottom: 5px;">Mensaje:</h3>
        <p style="white-space: pre-line;">{{ $messageContent }}</p>
    </div>
</body>
</html>
