<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Compañía | PayrollTask</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            position: relative;
            overflow: hidden;
            padding: 2rem 0;
        }

        body::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            top: -200px;
            right: -100px;
            border-radius: 50%;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.1) 0%, transparent 70%);
            bottom: -100px;
            left: -100px;
            border-radius: 50%;
        }

        .login-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 1;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }

        .login-brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-brand .brand-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }

        .login-brand h3 {
            color: white;
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.02em;
        }

        .login-brand p {
            color: #64748b;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .form-control {
            background: #334155;
            border: 1px solid rgba(255,255,255,0.08);
            color: white;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }

        .form-control:focus {
            background: #334155;
            border-color: #6366f1;
            color: white;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .form-control::placeholder { color: #64748b; }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
            border: none;
            color: white;
            font-weight: 700;
            padding: 0.75rem;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-login:hover {
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
            transform: translateY(-2px);
            color: white;
        }

        .alert {
            border-radius: 12px;
            font-size: 0.85rem;
            border: none;
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
        }

        .input-group-text {
            background: #334155;
            border: 1px solid rgba(255,255,255,0.08);
            color: #64748b;
            border-radius: 12px 0 0 12px;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: #94a3b8;
        }

        .login-footer a {
            color: #6366f1;
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .section-title {
            color: #818cf8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding-bottom: 0.5rem;
            margin-top: 1rem;
        }

        /* Terms link */
        .terms-link {
            color: #818cf8;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .terms-link:hover { color: #a5b4fc; text-decoration: underline; }

        .terms-notice {
            background: rgba(99, 102, 241, 0.08);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 10px;
            padding: 0.65rem 1rem;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.82rem;
            color: #94a3b8;
        }

        /* Full-screen modal overlay */
        .terms-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(10, 15, 30, 0.97);
            backdrop-filter: blur(12px);
            flex-direction: column;
            overflow: hidden;
        }
        .terms-overlay.active { display: flex; }

        .terms-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(30, 41, 59, 0.9);
            flex-shrink: 0;
        }
        .terms-header-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .terms-header-title .icon-badge {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: white;
        }
        .terms-close-btn {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #94a3b8;
            border-radius: 8px;
            padding: 0.4rem 0.75rem;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .terms-close-btn:hover { background: rgba(255,255,255,0.1); color: white; }

        .terms-body {
            flex: 1;
            overflow-y: auto;
            padding: 2.5rem 3rem;
            max-width: 860px;
            margin: 0 auto;
            width: 100%;
            color: #cbd5e1;
            line-height: 1.85;
            font-size: 0.92rem;
        }
        .terms-body::-webkit-scrollbar { width: 6px; }
        .terms-body::-webkit-scrollbar-track { background: rgba(255,255,255,0.03); }
        .terms-body::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.4); border-radius: 3px; }
        .terms-body h1 {
            color: white;
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }
        .terms-body .terms-date {
            color: #64748b;
            font-size: 0.8rem;
            margin-bottom: 2rem;
            display: block;
        }
        .terms-body h2 {
            color: #a5b4fc;
            font-size: 1rem;
            font-weight: 700;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.8rem;
        }
        .terms-body p { margin-bottom: 1rem; }

        .terms-footer {
            padding: 1.5rem 2rem;
            border-top: 1px solid rgba(255,255,255,0.07);
            background: rgba(30, 41, 59, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            flex-shrink: 0;
        }
        .btn-accept-terms {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
            border: none;
            color: white;
            font-weight: 700;
            padding: 0.8rem 2.5rem;
            border-radius: 12px;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 20px rgba(99,102,241,0.3);
        }
        .btn-accept-terms:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(99,102,241,0.5);
        }

        /* Disabled submit button */
        .btn-login:disabled {
            background: #334155 !important;
            box-shadow: none !important;
            transform: none !important;
            cursor: not-allowed;
            opacity: 0.55;
        }
        .btn-login-wrapper { position: relative; }
        .lock-badge {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 1rem;
            pointer-events: none;
            transition: opacity 0.3s;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-brand">
            <div class="brand-icon"><i class="bi bi-building-add"></i></div>
            <h3>Crear Compañía</h3>
            <p>Registra tu empresa y comienza a gestionar</p>
        </div>

        <?php if($errors->any()): ?>
            <div class="alert mb-3">
                <i class="bi bi-exclamation-circle me-1"></i>
                <?php echo e($errors->first()); ?>

            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('register')); ?>">
            <?php echo csrf_field(); ?>

            <div class="section-title">Datos de la Empresa</div>

            <div class="mb-3">
                <label class="form-label">Nombre de la Compañía</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-building"></i></span>
                    <input type="text" class="form-control" name="company_name" value="<?php echo e(old('company_name')); ?>"
                           placeholder="Mi Empresa S.R.L" required autofocus style="border-radius: 0 12px 12px 0">
                </div>
            </div>

            <div class="section-title">Datos del Administrador</div>

            <div class="mb-3">
                <label class="form-label">Nombre Completo</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" name="name" value="<?php echo e(old('name')); ?>"
                           placeholder="Juan Pérez" required style="border-radius: 0 12px 12px 0">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>"
                           placeholder="admin@empresa.com" required style="border-radius: 0 12px 12px 0">
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" name="password"
                                   placeholder="••••••••" required style="border-radius: 0 12px 12px 0">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Confirmar Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                            <input type="password" class="form-control" name="password_confirmation"
                                   placeholder="••••••••" required style="border-radius: 0 12px 12px 0">
                        </div>
                    </div>
                </div>
            </div>
            <!-- Terms notice -->
            <div class="terms-notice">
                <i class="bi bi-shield-check" style="color:#818cf8;font-size:1.1rem;flex-shrink:0"></i>
                <span>Al registrarte aceptas nuestros
                    <a href="#" class="terms-link" id="openTermsBtn">
                        <i class="bi bi-file-text"></i>Términos y Condiciones
                    </a>
                </span>
            </div>

            <div class="btn-login-wrapper mt-1">
                <button type="submit" class="btn btn-login" id="submitBtn" disabled>
                    <i class="bi bi-check-circle me-2"></i>Registrar Compañía
                </button>
                <span class="lock-badge" id="lockIcon"><i class="bi bi-lock-fill"></i></span>
            </div>
        </form>


        <div class="login-footer">
            ¿Ya tienes una cuenta? <a href="<?php echo e(route('login')); ?>">Inicia Sesión</a>
        </div>

    </div>

    <!-- ===================== TERMS & CONDITIONS MODAL ===================== -->
    <div class="terms-overlay" id="termsOverlay">

        <div class="terms-header">
            <div class="terms-header-title">
                <div class="icon-badge"><i class="bi bi-file-text"></i></div>
                Términos y Condiciones de Uso
            </div>
            <button class="terms-close-btn" id="closeTermsBtn">
                <i class="bi bi-x-lg me-1"></i>Cerrar
            </button>
        </div>

        <div class="terms-body" id="termsBody">
            <h1>Términos y Condiciones de Uso — PayrollTask</h1>
            <span class="terms-date">Última actualización: 30 de mayo de 2026 &nbsp;|&nbsp; Codevar &copy; 2026. Todos los derechos reservados.</span>

            <h2>1. Aceptación de los Términos</h2>
            <p>Al registrarse, acceder o utilizar la plataforma <strong>PayrollTask</strong>, desarrollada y operada por <strong>Codevar</strong>, usted declara haber leído, comprendido y aceptado en su totalidad los presentes Términos y Condiciones de Uso. Si actúa en nombre de una organización o empresa, declara tener la autoridad legal necesaria para obligar a dicha entidad a cumplir estos términos.</p>
            <p>El uso continuo de la plataforma después de cualquier modificación a estos términos constituirá su aceptación de los cambios introducidos. Si no está de acuerdo con alguna de las condiciones aquí establecidas, deberá abstenerse de utilizar los servicios de PayrollTask.</p>

            <h2>2. Descripción del Servicio</h2>
            <p><strong>PayrollTask</strong> es una plataforma de gestión empresarial integral desarrollada por Codevar, diseñada para facilitar la administración de nóminas, control de empleados, gestión de solicitudes, evaluaciones de desempeño, reclutamiento, y demás procesos de recursos humanos dentro de organizaciones de cualquier tamaño.</p>
            <p>Los servicios incluyen, entre otros: gestión de empleados y contratos, procesamiento automatizado de nóminas, control de asistencia y tiempo, flujos de aprobación de solicitudes, módulos de evaluación, reportes y estadísticas, y herramientas de comunicación interna. Codevar se reserva el derecho de ampliar, modificar o descontinuar funcionalidades sin previo aviso, procurando siempre notificar a los usuarios afectados con la mayor antelación posible.</p>

            <h2>3. Registro de Cuenta y Responsabilidades del Administrador</h2>
            <p>Para acceder a PayrollTask, la empresa contratante deberá registrar una cuenta corporativa designando un usuario administrador principal. Este administrador será el responsable de gestionar los accesos, configuraciones y datos de su organización dentro de la plataforma. La información proporcionada durante el registro debe ser veraz, completa y actualizada.</p>
            <p>El administrador asume la responsabilidad exclusiva de mantener la confidencialidad de las credenciales de acceso de todos los usuarios creados bajo su cuenta. Cualquier actividad realizada desde cuentas registradas bajo su organización será considerada responsabilidad del administrador. En caso de sospecha de uso no autorizado, deberá notificarlo inmediatamente al equipo de soporte de Codevar.</p>
            <p>Queda estrictamente prohibido compartir credenciales de acceso entre múltiples personas o utilizarlas con fines distintos a los autorizados. Codevar no será responsable por pérdidas o daños derivados del incumplimiento de esta obligación por parte del administrador o cualquiera de sus usuarios.</p>

            <h2>4. Uso Aceptable de la Plataforma</h2>
            <p>El usuario se compromete a utilizar PayrollTask única y exclusivamente para los fines legítimos relacionados con la gestión de recursos humanos y nóminas de su organización. Queda prohibido el uso de la plataforma para actividades ilegales, fraudulentas, difamatorias, o que vulneren derechos de terceros, así como cualquier intento de acceder sin autorización a sistemas, datos o cuentas ajenas.</p>
            <p>No está permitido realizar ingeniería inversa, descompilar, modificar, distribuir o crear trabajos derivados del software de PayrollTask sin la autorización expresa y escrita de Codevar. El incumplimiento de estas restricciones podrá resultar en la suspensión inmediata de la cuenta y el inicio de acciones legales correspondientes.</p>

            <h2>5. Privacidad y Protección de Datos Personales</h2>
            <p>Codevar reconoce la importancia de proteger la información personal y sensible de los empleados y usuarios registrados en PayrollTask. Toda la información recopilada a través de la plataforma será tratada conforme a la legislación de protección de datos vigente en la República Dominicana y demás normativas aplicables, incluyendo la Ley No. 172-13 sobre Protección de Datos de Carácter Personal.</p>
            <p>Los datos almacenados en PayrollTask, tales como información personal de empleados, salarios, contratos y documentos confidenciales, serán utilizados exclusivamente para prestar el servicio contratado. Codevar no venderá, cederá ni compartirá esta información con terceros sin el consentimiento explícito del cliente, salvo requerimiento legal o judicial debidamente sustentado.</p>
            <p>La empresa contratante actúa como responsable del tratamiento de los datos de sus empleados y, al utilizar PayrollTask, garantiza haber obtenido las autorizaciones necesarias para el procesamiento de dicha información dentro de la plataforma. Codevar actuará como encargado del tratamiento en nombre del cliente.</p>

            <h2>6. Propiedad Intelectual</h2>
            <p>Todo el contenido, diseño, código fuente, interfaces, logotipos, marcas registradas, bases de datos, documentación y demás elementos que componen PayrollTask son propiedad exclusiva de <strong>Codevar</strong> y están protegidos por las leyes de propiedad intelectual y derechos de autor aplicables. El acceso a la plataforma no transfiere al usuario ningún derecho de propiedad sobre dichos elementos.</p>
            <p>Se concede al cliente una licencia de uso limitada, no exclusiva, intransferible y revocable para acceder y utilizar PayrollTask durante la vigencia del contrato de servicio. Esta licencia no autoriza la reproducción, distribución, modificación o explotación comercial de ningún elemento de la plataforma sin autorización escrita de Codevar.</p>

            <h2>7. Planes, Pagos y Facturación</h2>
            <p>El acceso a PayrollTask está sujeto a la contratación de un plan de servicio ofrecido por Codevar. Los precios, condiciones y alcance de cada plan se especificarán en la propuesta comercial o contrato suscrito entre las partes. Los pagos deberán realizarse en los plazos y mediante los métodos acordados, y el incumplimiento de pago podrá resultar en la suspensión temporal o definitiva del acceso al servicio.</p>
            <p>Codevar se reserva el derecho de modificar los precios de sus planes con un aviso previo mínimo de treinta (30) días calendario. Las modificaciones de precio no afectarán los contratos vigentes hasta su período de renovación, salvo acuerdo en contrario entre las partes.</p>

            <h2>8. Limitación de Responsabilidad</h2>
            <p>PayrollTask se proporciona "tal como está" y Codevar no garantiza que el servicio esté libre de interrupciones, errores o fallos técnicos. Aunque se realizan todos los esfuerzos razonables para mantener la disponibilidad y seguridad de la plataforma, Codevar no será responsable por pérdidas económicas, daños directos, indirectos, incidentales o consecuentes derivados del uso o imposibilidad de uso del servicio.</p>
            <p>En ningún caso la responsabilidad total acumulada de Codevar frente al cliente, por cualquier causa, excederá el monto total pagado por el cliente a Codevar durante los tres (3) meses anteriores al evento que originó el daño. Esta limitación aplica en la máxima medida permitida por la ley aplicable.</p>

            <h2>9. Modificaciones al Servicio y a los Términos</h2>
            <p>Codevar podrá actualizar, modificar o ampliar estos Términos y Condiciones en cualquier momento. Las actualizaciones serán notificadas a los administradores de cuenta registrados mediante correo electrónico o a través de un aviso en la propia plataforma, con un mínimo de quince (15) días de anticipación antes de su entrada en vigor.</p>
            <p>Asimismo, Codevar podrá introducir mejoras, nuevas funciones o cambios en las características existentes de PayrollTask sin previo aviso, siempre que dichos cambios no impliquen una reducción significativa de las funcionalidades contratadas. En caso de cambios sustanciales, el cliente será notificado oportunamente.</p>

            <h2>10. Suspensión y Terminación del Servicio</h2>
            <p>Codevar podrá suspender o terminar el acceso a PayrollTask de manera inmediata en los siguientes supuestos: (a) incumplimiento de los presentes Términos y Condiciones; (b) falta de pago por dos o más períodos consecutivos; (c) uso fraudulento, abusivo o ilegal de la plataforma; (d) solicitud expresa del cliente.</p>
            <p>Ante una terminación por cualquier causa, el cliente dispondrá de un período de treinta (30) días para solicitar la exportación de sus datos. Transcurrido dicho plazo, Codevar procederá a eliminar de manera segura toda la información asociada a la cuenta, salvo obligación legal de conservación.</p>

            <h2>11. Soporte Técnico y Disponibilidad</h2>
            <p>Codevar proveerá soporte técnico a los administradores de cuenta registrados a través de los canales oficiales establecidos en el contrato de servicio. El horario de atención estándar es de lunes a viernes de 8:00 a.m. a 6:00 p.m. (hora local de República Dominicana), excluyendo días feriados nacionales. La disponibilidad objetivo de la plataforma es del 99% mensual, sujeta a ventanas de mantenimiento programado previamente notificadas.</p>

            <h2>12. Ley Aplicable y Jurisdicción</h2>
            <p>Los presentes Términos y Condiciones se rigen por las leyes de la <strong>República Dominicana</strong>. Cualquier controversia o reclamación derivada del uso de PayrollTask o de la interpretación de estos términos será sometida a la jurisdicción de los tribunales competentes de Santo Domingo, República Dominicana, renunciando las partes expresamente a cualquier otro fuero que pudiera corresponderles.</p>
            <p>Para consultas, reclamaciones o ejercicio de derechos sobre sus datos personales, puede contactar a Codevar a través de los canales de soporte oficiales indicados en la plataforma o en el contrato de servicio suscrito. Al hacer clic en "Aceptar Términos y Condiciones", usted confirma que ha leído y comprende íntegramente el presente documento y manifiesta su conformidad con todas las disposiciones aquí establecidas.</p>
        </div>

        <div class="terms-footer">
            <button class="btn-accept-terms" id="acceptTermsBtn">
                <i class="bi bi-check2-circle"></i>
                Aceptar Términos y Condiciones
            </button>
        </div>

    </div>
    <!-- ================================================================== -->

    <script>
        const openTermsBtn  = document.getElementById('openTermsBtn');
        const closeTermsBtn = document.getElementById('closeTermsBtn');
        const acceptTermsBtn = document.getElementById('acceptTermsBtn');
        const termsOverlay  = document.getElementById('termsOverlay');
        const submitBtn     = document.getElementById('submitBtn');
        const lockIcon      = document.getElementById('lockIcon');

        // Open modal
        openTermsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            termsOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        // Close modal (without accepting)
        closeTermsBtn.addEventListener('click', function() {
            termsOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });

        // Accept terms → unlock submit button
        acceptTermsBtn.addEventListener('click', function() {
            termsOverlay.classList.remove('active');
            document.body.style.overflow = '';

            submitBtn.disabled = false;
            lockIcon.style.opacity = '0';

            // Update visual feedback
            openTermsBtn.innerHTML = '<i class="bi bi-check2-circle"></i> Términos aceptados';
            openTermsBtn.style.color = '#34d399';
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && termsOverlay.classList.contains('active')) {
                termsOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/auth/register.blade.php ENDPATH**/ ?>