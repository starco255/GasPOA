
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        
        <div class="text-center mb-4 d-md-block d-none">
            <a href="<?php echo e(route('home')); ?>" class="text-decoration-none">
                <h4 class="text-white"><i class="bi bi-fire"></i> GasPOA</h4>
            </a>
            <hr class="bg-secondary">
        </div>

        
        <ul class="nav flex-column">
            <?php $userType = Auth::user()->user_type ?? 'consumer'; ?>

            
            <li class="nav-item">
                <a class="nav-link text-white <?php echo e(request()->routeIs('dashboard') ? 'active bg-primary' : ''); ?>" href="<?php echo e(route('dashboard')); ?>">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>

            
            <?php if($userType === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo e(request()->routeIs('admin.users.*') ? 'active bg-primary' : ''); ?>" href="<?php echo e(route('admin.users.index')); ?>">
                        <i class="bi bi-people me-2"></i> Watumiaji
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo e(request()->routeIs('admin.verify.*') ? 'active bg-primary' : ''); ?>" href="<?php echo e(route('admin.users.verify')); ?>">
                        <i class="bi bi-check-circle me-2"></i> Uidhinishaji
                        <span class="badge bg-danger ms-auto">4</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo e(request()->routeIs('admin.pricing.*') ? 'active bg-primary' : ''); ?>" href="<?php echo e(route('admin.pricing.index')); ?>">
                        <i class="bi bi-currency-dollar me-2"></i> Bei
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo e(request()->routeIs('admin.reports.*') ? 'active bg-primary' : ''); ?>" href="<?php echo e(route('admin.reports.finance')); ?>">
                        <i class="bi bi-bar-chart me-2"></i> Ripoti
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo e(request()->routeIs('admin.settings.*') ? 'active bg-primary' : ''); ?>" href="<?php echo e(route('admin.settings.profile')); ?>">
                        <i class="bi bi-gear me-2"></i> Mipangilio
                    </a>
                </li>
            <?php endif; ?>

            
            <?php if($userType === 'retailer'): ?>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo e(request()->routeIs('retailer.orders.incoming') ? 'active bg-primary' : ''); ?>" href="<?php echo e(route('retailer.orders.incoming')); ?>">
                        <i class="bi bi-bell me-2"></i> Maagizo Mapya
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo e(route('retailer.inventory.index')); ?>">
                        <i class="bi bi-box-seam me-2"></i> Hisa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo e(route('retailer.procurement.browse')); ?>">
                        <i class="bi bi-cart-plus me-2"></i> Nunua Jumla
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo e(route('retailer.settings.shop')); ?>">
                        <i class="bi bi-shop me-2"></i> Duka Langu
                    </a>
                </li>
            <?php endif; ?>

            
            <?php if($userType === 'wholesaler'): ?>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo e(request()->routeIs('wholesaler.orders.incoming') ? 'active bg-primary' : ''); ?>" href="<?php echo e(route('wholesaler.orders.incoming')); ?>">
                        <i class="bi bi-inbox me-2"></i> Maagizo Mapya
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo e(route('wholesaler.orders.processing')); ?>">
                        <i class="bi bi-gear me-2"></i> Yanayoshughulikiwa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo e(route('wholesaler.products.index')); ?>">
                        <i class="bi bi-tags me-2"></i> Bidhaa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo e(route('wholesaler.settings.profile')); ?>">
                        <i class="bi bi-building me-2"></i> Ghala
                    </a>
                </li>
            <?php endif; ?>

            
            <?php if($userType === 'consumer'): ?>
                <li class="nav-item">
                    <a class="nav-link text-white <?php echo e(request()->routeIs('consumer.order.create') ? 'active bg-primary' : ''); ?>" href="<?php echo e(route('consumer.order.create')); ?>">
                        <i class="bi bi-plus-circle me-2"></i> Agiza Sasa
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo e(route('consumer.order.tracking')); ?>">
                        <i class="bi bi-geo-alt me-2"></i> Fuatilia
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?php echo e(route('consumer.history')); ?>">
                        <i class="bi bi-clock-history me-2"></i> Historia
                    </a>
                </li>
            <?php endif; ?>

            <hr class="bg-secondary">

            <li class="nav-item">
                <a class="nav-link text-white" href="<?php echo e(route('profile.edit')); ?>">
                    <i class="bi bi-person-gear me-2"></i> Profaili
                </a>
            </li>
            <li class="nav-item">
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="nav-link text-white text-start w-100">
                        <i class="bi bi-box-arrow-right me-2"></i> Toka
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\partials\sidebar.blade.php ENDPATH**/ ?>