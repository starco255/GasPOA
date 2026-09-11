
<!DOCTYPE html>
<html lang="<?php echo e(auth()->user()?->interface_language === 'en' ? 'en' : 'sw'); ?>" data-theme="<?php echo e(auth()->user()?->interface_theme === 'dark' ? 'dark' : 'light'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="GasPOA Market - Pata gesi papo kwa hapo kutoka kwa wauzaji wa karibu yako.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php echo $__env->make('components.interface-preferences-initializer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <title><?php echo $__env->yieldContent('title', 'Ingia'); ?> - GasPOA Market</title>
    
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo e(asset('css/custom.css')); ?>">
    
    
    <style>
        body {
            background: linear-gradient(145deg, #a3a3e4 0%, #b3b896 100%);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            padding-top: 80px; /* nafasi kwa ajili ya navbar fixed */
        }
        
        /* Navbar itakuwa fixed juu */
        .navbar {
            background: linear-gradient(145deg, #af431c, #5d5da7) !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 0.6rem 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            color: white !important;
        }
        
        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            transition: all 0.2s;
        }
        
        .navbar-nav .nav-link:hover {
            background: rgba(255,255,255,0.15);
            color: white !important;
        }
        
        .navbar .btn-outline-light {
            border: 2px solid rgba(255,255,255,0.7);
            color: white;
            border-radius: 40px;
            padding: 0.4rem 1.2rem;
            font-weight: 600;
        }
        
        .navbar .btn-outline-light:hover {
            background: white;
            color: #FF6B35;
        }
        
        /* Kadi ya Login/Register itakuwa katikati */
        .auth-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            padding: 20px;
        }
        
        .guest-card {
            max-width: 460px;
            width: 100%;
            border-radius: 32px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: linear-gradient(145deg, #a3a3e4 0%, #b3b896 100%);
            backdrop-filter: blur(12px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            padding: 2.5rem 2rem;
            margin: 20px 0;
        }
        
        .guest-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 40px 70px rgba(0, 0, 0, 0.18);
        }
        
        .guest-logo {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #FF6B35 0%, #E85D2C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        
        .guest-logo i {
            -webkit-text-fill-color: initial;
            color: #FF6B35;
            margin-right: 12px;
        }
        
        h4 {
            color: #1A1A2E;
            font-weight: 700;
        }
        
        .auth-form .input-group-text {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e2e8f0;
            border-right: none;
            border-radius: 16px 0 0 16px;
            color: #64748b;
        }
        
        .auth-form .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e2e8f0;
            border-left: none;
            border-radius: 0 16px 16px 0;
            padding: 0.8rem 1rem;
            font-size: 1rem;
        }
        
        .auth-form .form-control:focus {
            background: white;
            border-color: #FF6B35;
            box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #FF6B35 0%, #E85D2C 100%);
            border: none;
            border-radius: 40px;
            padding: 14px 20px;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #E85D2C 0%, #FF6B35 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(255, 107, 53, 0.4);
        }
        
        .auth-footer {
            color: #475569;
            font-size: 0.95rem;
        }
        
        .auth-footer a {
            color: #FF6B35;
            font-weight: 700;
            text-decoration: none;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
            color: #E85D2C;
        }
        
        .alert {
            border-radius: 16px;
            border: none;
            padding: 0.9rem 1.2rem;
            font-size: 0.95rem;
            box-shadow: 0 5px 10px rgba(0,0,0,0.03);
        }
        
        .footer-links {
            margin-top: 2rem;
            text-align: center;
            color: #64748b;
        }
        
        .footer-links a {
            color: #FF6B35;
            font-weight: 600;
        }
        
        @media (max-width: 480px) {
            .guest-card {
                padding: 2rem 1.5rem;
            }
            .guest-logo {
                font-size: 2.5rem;
            }
            body {
                padding-top: 70px;
            }
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?php echo e(route('home')); ?>">
                <i class="bi bi-fire"></i> GasPOA
            </a>
            <button class="navbar-toggler border-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-lg-3 mb-2 mb-lg-0">
                        <?php echo $__env->make('components.interface-preferences', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </li>
                    
                    <li class="nav-item me-3 d-none d-lg-block">
                        <span class="badge bg-light text-dark p-2">
                            <i class="bi bi-phone"></i> *150*99#
                        </span>
                    </li>
                    <?php if(auth()->guard()->guest()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('login')); ?>">
                                <i class="bi bi-box-arrow-in-right"></i> Ingia
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-light ms-2" href="<?php echo e(route('register')); ?>">
                                <i class="bi bi-person-plus"></i> Jisajili
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo e(Auth::user()->full_name); ?>

                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo e(route('dashboard')); ?>">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right"></i> Toka
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    
    <div class="auth-wrapper">
        <div class="guest-card">
            
            <div class="guest-logo">
                <i class="bi bi-fire"></i> GasPOA
            </div>
            
            
            <h4 class="text-center mb-4"><?php echo $__env->yieldContent('page-heading', 'Karibu Tena'); ?></h4>
            
            
            <?php if(session('status')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?php echo e(session('status')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if(session('info')): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i> <?php echo e(session('info')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <ul class="mb-0 ps-3">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            
            <div class="auth-form">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
            
            
            <div class="mt-4 text-center auth-footer">
                <?php echo $__env->yieldContent('footer-links'); ?>
            </div>
        </div>
    </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo e(asset('js/interface-preferences.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\GasPOA\resources\views/layouts/guest.blade.php ENDPATH**/ ?>