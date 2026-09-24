<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Incidencia Reportada</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: #ffffff;
            padding: 30px 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .content {
            padding: 30px 24px;
            line-height: 1.6;
        }
        .field {
            margin-bottom: 20px;
        }
        .label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 4px;
        }
        .value {
            font-size: 16px;
            color: #1e293b;
        }
        .priority-low {
            background-color: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }
        .priority-medium {
            background-color: #fef3c7;
            color: #d97706;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }
        .priority-high {
            background-color: #fee2e2;
            color: #dc2626;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 24px 0;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 24px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nueva Incidencia</h1>
        </div>
        <div class="content">
            <p>Se ha registrado una nueva incidencia en la plataforma. A continuación los detalles:</p>
            
            <div class="divider"></div>

            <div class="field">
                <div class="label">Título</div>
                <div class="value" style="font-weight: 600; font-size: 18px;"><?php echo e($incident->title); ?></div>
            </div>

            <div class="field">
                <div class="label">Reportado por</div>
                <div class="value"><?php echo e($incident->user->name ?? 'Usuario Desconocido'); ?> (<?php echo e($incident->user->email ?? 'Sin Correo'); ?>)</div>
            </div>

            <div class="field">
                <div class="label">Prioridad</div>
                <div class="value">
                    <?php if($incident->priority === 'high'): ?>
                        <span class="priority-high">Alta</span>
                    <?php elseif($incident->priority === 'medium'): ?>
                        <span class="priority-medium">Media</span>
                    <?php else: ?>
                        <span class="priority-low">Baja</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="field">
                <div class="label">Descripción</div>
                <div class="value" style="background-color: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; white-space: pre-line;"><?php echo e($incident->description); ?></div>
            </div>

            <?php if($incident->attachments && $incident->attachments->isNotEmpty()): ?>
                <div class="field" style="margin-top: 20px;">
                    <div class="label">Archivos Adjuntos</div>
                    <div class="value">
                        <ul style="padding-left: 20px; margin: 5px 0; list-style-type: none;">
                            <?php $__currentLoopData = $incident->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li style="margin-bottom: 8px;">
                                    <a href="<?php echo e(\Illuminate\Support\Facades\Storage::disk('s3')->url($attachment->file_path)); ?>" target="_blank" style="color: #6366f1; text-decoration: none; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; gap: 6px;">
                                        <?php if($attachment->file_type === 'video'): ?>
                                            🎥 Ver Video Adjunto
                                        <?php else: ?>
                                            🖼️ Ver Imagen Adjunta
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <div class="divider"></div>
            
            <p style="font-size: 14px; color: #64748b;">Puedes gestionar esta incidencia ingresando al panel de administración de la plataforma.</p>
        </div>
        <div class="footer">
            Este es un correo automático generado por la plataforma de Nómina/Recursos Humanos.
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/emails/incident_reported.blade.php ENDPATH**/ ?>