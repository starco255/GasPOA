<!DOCTYPE html>
<html lang="<?php echo e(auth()->user()?->interface_language === 'en' ? 'en' : 'sw'); ?>" data-theme="<?php echo e(auth()->user()?->interface_theme === 'dark' ? 'dark' : 'light'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="GasPOA Market - Pata gesi papo kwa hapo kutoka kwa wauzaji wa karibu yako.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php echo $__env->make('components.interface-preferences-initializer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <title>Dashboard - <?php echo $__env->yieldContent('title', 'GasPOA Market'); ?></title>
    
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo e(asset('css/custom.css')); ?>">
    
    <style>
     body {
       background: linear-gradient(145deg, #a3a3e4 0%, #b3b896 100%);
       font-family: 'Inter', sans-serif;
    }
        /* Top Navbar - Imeboreshwa kufanana na Admin */
        .navbar-top {
            background: linear-gradient(180deg, #1A1A2E 0%, #16213E 100%);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 0.6rem 1.5rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            color: white !important;
        }
        
        .navbar-brand i {
            color: #FF6B35;
            margin-right: 8px;
        }
        
        .navbar-top .btn-outline-light {
            border-color: rgba(255,255,255,0.3);
            color: white;
        }
        
        .navbar-top .btn-outline-light:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }


        /* Hakikisha kitufe cha Akaunti hakibadiliki rangi kuwa nyeupe kinapoelezwa */
.navbar-top .btn-outline-light:hover,
.navbar-top .btn-outline-light:focus,
.navbar-top .btn-outline-light:active {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: white !important;
    border-color: rgba(255, 255, 255, 0.5) !important;
}

/* Pia hakikisha dropdown menu yenyewe ina muonekano mzuri */
.dropdown-menu {
    background-color: #1A1A2E;
    border: 1px solid rgba(255,255,255,0.1);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.dropdown-menu .dropdown-item {
    color: rgba(255,255,255,0.8);
}

.dropdown-menu .dropdown-item:hover {
    background: linear-gradient(145deg, #7575b9 0%, #b3b896 100%);
    color: white;
}

.dropdown-divider {
    border-top: 1px solid rgba(255,255,255,0.1);
}
        
        /* Sidebar Styling - Sasa inaanza chini ya navbar */
        .sidebar {
            position: fixed;
            top: 60px; /* Urefu wa navbar */
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 20px 12px;
            box-shadow: 5px 0 20px rgba(0, 0, 0, 0.05);
            background: linear-gradient(180deg, #1A1A2E 0%, #16213E 100%);
            color: white;
            overflow-y: auto;
            overflow-x: hidden;
            width: 260px;
        }
        
        /* Skroli ndani ya sidebar */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar::-webkit-scrollbar-thumb {
        background: linear-gradient(145deg, #7575b9 0%, #b3b896 100%);
        border-radius: 10px;
        }
        
        /* Profaili ya Mtumiaji kwenye Sidebar */
        .sidebar-profile {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
        }
        
        .sidebar-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(145deg, #7575b9 0%, #b3b896 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.3rem;
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
        }
        
        .sidebar-user-name {
            font-weight: 700;
            color: white;
            font-size: 1rem;
            line-height: 1.2;
        }
        
        .sidebar-user-role {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            padding: 0.2rem 0.8rem;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 5px;
        }
        
        .sidebar .nav-link {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.7);
            padding: 0.8rem 1rem;
            border-radius: 12px;
            margin-bottom: 6px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link i {
            width: 24px;
            margin-right: 8px;
        }
        
        .sidebar .nav-link:hover {
            color: #ffffff;
            background: linear-gradient(145deg, #7575b9 0%, #b3b896 100%);
        }
        
        .sidebar .nav-link.active {
            color: #ffffff;
            background: linear-gradient(145deg, #7575b9, #E85D2C);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin: 1rem 0;
        }
        
        /* Main Content - inaanza baada ya sidebar na navbar */
        .main-content {
            margin-left: 260px; /* Upana wa sidebar */
            margin-top: 60px; /* Urefu wa navbar */
            padding: 24px 30px;
            min-height: calc(100vh - 60px);
        }
        
        /* Kwa vifaa vidogo */
        @media (max-width: 767.98px) {
            .sidebar {
                display: none;
            }
            .sidebar.collapse.show {
                display: block;
                width: min(86vw, 320px);
                z-index: 1025;
                border-right: 1px solid rgba(255, 255, 255, 0.12);
            }
            .main-content {
                margin-left: 0;
                padding: 16px 12px calc(6.5rem + env(safe-area-inset-bottom));
            }
            .navbar-top .navbar-toggler {
                display: inline-block;
            }
        }
        
        /* Vitufe na Vipengele vya ziada */
        .btn-outline-light {
            border-color: rgba(255,255,255,0.3);
            color: white;
        }
        
        .btn-outline-light:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .alert {
            border-radius: 16px;
            border: none;
        }
        
        /* Hakikisha kichwa cha ukurasa kinaonekana vizuri */
        .page-title {
            color: #1A1A2E;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    
    <nav class="navbar-top d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-light d-md-none me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand" href="<?php echo e(route('home')); ?>">
                <i class="bi bi-fire"></i> GasPOA
            </a>
        </div>
        
        <div class="d-flex align-items-center">
            <?php echo $__env->make('components.interface-preferences', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <span class="text-white d-none d-md-block me-3">
                <i class="bi bi-person-circle"></i> <?php echo e(Auth::user()->full_name); ?>

            </span>
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-gear"></i> <span class="d-none d-sm-inline">Akaunti</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    
                    <?php if(Auth::user()->user_type === 'retailer'): ?>
                        <li><a class="dropdown-item" href="<?php echo e(route('retailer.profile.edit')); ?>"><i class="bi bi-person"></i> Profaili</a></li>
                    <?php elseif(Auth::user()->user_type === 'wholesaler'): ?>
                        <li><a class="dropdown-item" href="<?php echo e(route('wholesaler.account.edit')); ?>"><i class="bi bi-person"></i> Profaili</a></li>
                    <?php else: ?>
                        <li><a class="dropdown-item" href="<?php echo e(route('profile.edit')); ?>"><i class="bi bi-person"></i> Profaili</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Toka</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="d-flex">
        
        <nav id="sidebarMenu" class="sidebar collapse d-md-block">
            
            <div class="sidebar-profile d-flex align-items-center gap-3">
                <div class="sidebar-avatar">
                    <?php echo e(strtoupper(substr(Auth::user()->full_name ?? 'U', 0, 1))); ?>

                </div>
                <div class="flex-grow-1">
                    <div class="sidebar-user-name"><?php echo e(Auth::user()->full_name ?? 'Mtumiaji'); ?></div>
                    <span class="sidebar-user-role"><?php echo e(ucfirst(Auth::user()->user_type)); ?></span>
                </div>
            </div>
            
            <ul class="nav flex-column">
                
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>" 
                       href="<?php echo e(route('dashboard')); ?>">
                        <i class="bi bi-house-door"></i> Nyumbani
                    </a>
                </li>
                
                         
<?php if(Auth::user()->user_type === 'consumer'): ?>
    <li class="nav-item">
        <a class="nav-link <?php echo e(request()->routeIs('consumer.order.create') || request()->routeIs('consumer.order.select-retailer') ? 'active' : ''); ?>" 
           href="<?php echo e(route('consumer.order.create')); ?>">
            <i class="bi bi-plus-circle"></i> Agiza Sasa
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo e(request()->routeIs('consumer.order.tracking*') || request()->routeIs('consumer.order.details*') ? 'active' : ''); ?>" 
           href="<?php echo e(route('consumer.order.tracking')); ?>">
            <i class="bi bi-geo-alt"></i> Fuatilia Agizo
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo e(request()->routeIs('consumer.history*') || request()->routeIs('consumer.order.receipt*') ? 'active' : ''); ?>" 
           href="<?php echo e(route('consumer.history')); ?>">
            <i class="bi bi-clock-history"></i> Historia
        </a>
    </li>
<?php endif; ?>
                
                
                <?php if(Auth::user()->user_type === 'retailer'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('retailer.orders.incoming') ? 'active' : ''); ?>" 
                           href="<?php echo e(route('retailer.orders.incoming')); ?>">
                            <i class="bi bi-bell"></i> Maagizo Wateja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('retailer.procurement.browse') ? 'active' : ''); ?>" 
                           href="<?php echo e(route('retailer.procurement.browse')); ?>">
                            <i class="bi bi-cart-plus"></i> Agiza Gesi Jumla
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('retailer.settings.shop') ? 'active' : ''); ?>" 
                           href="<?php echo e(route('retailer.settings.shop')); ?>">
                            <i class="bi bi-shop"></i> Duka Langu
                        </a>
                    </li>
                <?php endif; ?>
                
                
                <?php if(Auth::user()->user_type === 'wholesaler'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('wholesaler.orders.incoming') ? 'active' : ''); ?>" 
                           href="<?php echo e(route('wholesaler.orders.incoming')); ?>">
                            <i class="bi bi-truck"></i> Maagizo ya Jumla
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('wholesaler.products.index') ? 'active' : ''); ?>" 
                           href="<?php echo e(route('wholesaler.products.index')); ?>">
                            <i class="bi bi-tags"></i> Bidhaa Zangu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('wholesaler.settings.shop') ? 'active' : ''); ?>" 
                           href="<?php echo e(route('wholesaler.settings.shop')); ?>">
                            <i class="bi bi-building"></i> Ghala Langu
                        </a>
                    </li>
                <?php endif; ?>
                
                <div class="sidebar-divider"></div>
                
                
                <?php if(Auth::user()->user_type === 'retailer'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('retailer.profile.edit') ? 'active' : ''); ?>" 
                           href="<?php echo e(route('retailer.profile.edit')); ?>">
                            <i class="bi bi-person-gear"></i> Mipangilio ya Akaunti
                        </a>
                    </li>
                <?php elseif(Auth::user()->user_type === 'wholesaler'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('wholesaler.account.edit') ? 'active' : ''); ?>" 
                           href="<?php echo e(route('wholesaler.account.edit')); ?>">
                            <i class="bi bi-person-gear"></i> Mipangilio ya Akaunti
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('profile.edit') ? 'active' : ''); ?>" 
                           href="<?php echo e(route('profile.edit')); ?>">
                            <i class="bi bi-person-gear"></i> Mipangilio ya Akaunti
                        </a>
                    </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="nav-link text-danger w-100 text-start border-0 bg-transparent">
                            <i class="bi bi-box-arrow-right"></i> Toka
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        
        <div class="main-content flex-grow-1">
            
            <?php echo $__env->make('partials.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            
            
            <?php if (! empty(trim($__env->yieldContent('page-title')))): ?>
                <h4 class="page-title"><?php echo $__env->yieldContent('page-title'); ?></h4>
            <?php endif; ?>
            
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>

    
    <?php echo $__env->make('partials.mobile-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo e(asset('js/interface-preferences.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\GasPOA\resources\views/layouts/dashboard.blade.php ENDPATH**/ ?>