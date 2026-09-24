<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Plan | PayrollTask</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --dark: #0f172a;
            --dark-2: #1e293b;
            --dark-3: #334155;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark);
            color: #e2e8f0;
            min-height: 100vh;
        }
        .topbar {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo img { height: 36px; }
        .logout-btn {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            color: #94a3b8;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .logout-btn:hover { background: rgba(255,255,255,0.12); color: white; }
        .hero {
            text-align: center;
            padding: 4rem 1rem 2rem;
        }
        .hero .badge-trial {
            display: inline-block;
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 0.35rem 1rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }
        .hero h1 {
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            font-weight: 800;
            color: white;
            letter-spacing: -0.03em;
            margin-bottom: 0.75rem;
        }
        .hero h1 span {
            background: linear-gradient(135deg, #6366f1, #a855f7, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero p {
            color: #94a3b8;
            font-size: 1.05rem;
            max-width: 560px;
            margin: 0 auto 0.5rem;
        }
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            margin: 2.5rem auto 4rem;
            padding: 0 1.5rem;
        }
        .plan-card {
            background: var(--dark-2);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 2rem;
            position: relative;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            display: flex;
            flex-direction: column;
        }
        .plan-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }
        .plan-card.popular {
            border-color: rgba(245, 158, 11, 0.5);
            box-shadow: 0 0 0 1px rgba(245,158,11,0.3), 0 10px 40px rgba(245,158,11,0.1);
        }
        .popular-badge {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.3rem 1rem;
            border-radius: 50px;
            white-space: nowrap;
        }
        .plan-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 1.25rem;
            color: white;
        }
        .plan-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.25rem;
        }
        .plan-limit {
            font-size: 0.78rem;
            color: #64748b;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        .plan-price {
            font-size: 2rem;
            font-weight: 800;
            color: white;
            line-height: 1;
            margin-bottom: 0.25rem;
        }
        .plan-price span {
            font-size: 0.85rem;
            font-weight: 400;
            color: #64748b;
        }
        .divider { height: 1px; background: rgba(255,255,255,0.06); margin: 1.25rem 0; }
        .features-list {
            list-style: none;
            padding: 0;
            margin: 0 0 1.5rem;
            flex: 1;
        }
        .features-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            font-size: 0.84rem;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }
        .features-list li i.check { color: #10b981; flex-shrink: 0; }
        .features-list li i.cross { color: #475569; flex-shrink: 0; }
        .features-list li.not-included { color: #475569; }
        .select-btn {
            display: block;
            width: 100%;
            padding: 0.85rem;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            color: white;
        }
        .select-btn:hover { opacity: 0.88; transform: translateY(-1px); }
        /* Non-super user locked message */
        .locked-container {
            max-width: 540px;
            margin: 5rem auto;
            text-align: center;
            padding: 0 1.5rem;
        }
        .locked-icon {
            width: 80px;
            height: 80px;
            background: rgba(239,68,68,0.12);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            color: #f87171;
            margin: 0 auto 2rem;
        }
        .locked-container h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
        }
        .locked-container p {
            color: #94a3b8;
            font-size: 1rem;
            line-height: 1.7;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="logo">
            <img src="https://payrolltask-s3-cloud-852128327213-us-east-2-an.s3.us-east-2.amazonaws.com/logo.png" alt="PayrollTask">
        </div>
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
            </button>
        </form>
    </div>

    <?php if(auth()->user()->isSuper()): ?>
        
        <div class="hero">
            <div class="badge-trial">⚠️ Período de prueba expirado</div>
            <h1>Elige tu <span>Plan de Suscripción</span></h1>
            <p><?php echo e($company->name); ?> — Selecciona el plan que mejor se adapte a tu empresa para continuar usando la plataforma.</p>
        </div>

        <?php if(session('success')): ?>
            <div class="container" style="max-width:800px;">
                <div class="alert alert-success" style="background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.3);border-radius:12px;padding:1rem 1.25rem;margin-bottom:1rem;">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

                </div>
            </div>
        <?php endif; ?>

        <div class="plans-grid">
            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="plan-card <?php echo e(isset($plan['popular']) && $plan['popular'] ? 'popular' : ''); ?>">
                    <?php if(isset($plan['popular']) && $plan['popular']): ?>
                        <div class="popular-badge">⭐ Más Popular</div>
                    <?php endif; ?>

                    <div class="plan-icon" style="background: linear-gradient(135deg, <?php echo e($plan['color']); ?>, <?php echo e($plan['color']); ?>aa);">
                        <i class="bi <?php echo e($plan['icon']); ?>"></i>
                    </div>

                    <div class="plan-name"><?php echo e($plan['name']); ?></div>
                    <div class="plan-limit"><i class="bi bi-people-fill me-1"></i><?php echo e($plan['limit']); ?></div>
                    <div class="plan-price"><?php echo e($plan['price']); ?> <span>/ mes</span></div>

                    <div class="divider"></div>

                    <ul class="features-list">
                        <?php $__currentLoopData = $plan['features']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <i class="bi bi-check-circle-fill check"></i>
                                <?php echo e($feature); ?>

                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>

                    <form method="POST" action="<?php echo e(route('subscription.store')); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="plan" value="<?php echo e($key); ?>">
                        <button type="submit" class="select-btn" style="background: linear-gradient(135deg, <?php echo e($plan['color']); ?>, <?php echo e($plan['color']); ?>cc);">
                            Seleccionar <?php echo e($plan['name']); ?>

                        </button>
                    </form>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

    <?php else: ?>
        
        <div class="locked-container">
            <div class="locked-icon">
                <i class="bi bi-lock-fill"></i>
            </div>
            <h2>Acceso Suspendido</h2>
            <p>
                El período de prueba gratuita de <strong><?php echo e($company->name); ?></strong> ha expirado.<br><br>
                Por favor, comuníquese con su superior para que seleccione un plan de suscripción y restaure el acceso a la plataforma.
            </p>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\Users\Freddy\Desktop\proyectos\anti\resources\views/subscription/index.blade.php ENDPATH**/ ?>