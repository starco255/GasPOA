
<div class="mobile-menu d-md-none fixed-bottom bg-white border-top py-2 shadow-lg">
    <div class="container">
        <div class="row text-center g-0">
            <?php $userType = Auth::user()->user_type ?? 'consumer'; ?>

            
            <div class="col">
                <a href="<?php echo e(route('dashboard')); ?>" class="text-decoration-none <?php echo e(request()->routeIs('dashboard') ? 'text-primary' : 'text-secondary'); ?>">
                    <i class="bi bi-house-door fs-5"></i>
                    <div class="small">Nyumbani</div>
                </a>
            </div>

            <?php if($userType === 'admin'): ?>
                <div class="col">
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="text-decoration-none text-secondary">
                        <i class="bi bi-people fs-5"></i>
                        <div class="small">Watumiaji</div>
                    </a>
                </div>
                <div class="col">
                    <a href="<?php echo e(route('admin.users.verify')); ?>" class="text-decoration-none text-secondary">
                        <i class="bi bi-check-circle fs-5"></i>
                        <div class="small">Idhinisha</div>
                    </a>
                </div>
                <div class="col">
                    <a href="<?php echo e(route('admin.reports.finance')); ?>" class="text-decoration-none text-secondary">
                        <i class="bi bi-bar-chart fs-5"></i>
                        <div class="small">Ripoti</div>
                    </a>
                </div>
            <?php elseif($userType === 'retailer'): ?>
                <div class="col">
                    <a href="<?php echo e(route('retailer.orders.incoming')); ?>" class="text-decoration-none text-secondary">
                        <i class="bi bi-bell fs-5"></i>
                        <div class="small">Maagizo</div>
                    </a>
                </div>
         <!--  <div class="col">
                    <a href="<?php echo e(route('retailer.inventory.index')); ?>" class="text-decoration-none text-secondary">
                        <i class="bi bi-box-seam fs-5"></i>
                        <div class="small">Hisa</div>
                    </a>
                </div> -->
                <div class="col">
                    <a href="<?php echo e(route('retailer.procurement.browse')); ?>" class="text-decoration-none text-secondary">
                        <i class="bi bi-cart fs-5"></i>
                        <div class="small">Jumla</div>
                    </a>
                </div>
            <?php elseif($userType === 'wholesaler'): ?>
                <div class="col">
                    <a href="<?php echo e(route('wholesaler.orders.incoming')); ?>" class="text-decoration-none text-secondary">
                        <i class="bi bi-inbox fs-5"></i>
                        <div class="small">Maagizo</div>
                    </a>
                </div>
                <div class="col">
                    <a href="<?php echo e(route('wholesaler.products.index')); ?>" class="text-decoration-none text-secondary">
                        <i class="bi bi-tags fs-5"></i>
                        <div class="small">Bidhaa</div>
                    </a>
                </div>
                <div class="col">
                    <a href="<?php echo e(route('wholesaler.orders.history')); ?>" class="text-decoration-none text-secondary">
                        <i class="bi bi-clock-history fs-5"></i>
                        <div class="small">Historia</div>
                    </a>
                </div>
            <?php else: ?>
                
                <div class="col">
                    <a href="<?php echo e(route('consumer.order.create')); ?>" class="text-decoration-none text-secondary">
                        <i class="bi bi-plus-circle fs-5"></i>
                        <div class="small">Agiza</div>
                    </a>
                </div>
                <div class="col">
                    <a href="<?php echo e(route('consumer.order.tracking')); ?>" class="text-decoration-none text-secondary">
                        <i class="bi bi-geo-alt fs-5"></i>
                        <div class="small">Fuatilia</div>
                    </a>
                </div>
                <div class="col">
                    <a href="<?php echo e(route('consumer.history')); ?>" class="text-decoration-none text-secondary">
                        <i class="bi bi-clock-history fs-5"></i>
                        <div class="small">Historia</div>
                    </a>
                </div>
            <?php endif; ?>

            
            <div class="col">
                <a href="<?php echo e(route('profile.edit')); ?>" class="text-decoration-none text-secondary">
                    <i class="bi bi-person fs-5"></i>
                    <div class="small">Profaili</div>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .mobile-menu {
        padding-bottom: env(safe-area-inset-bottom);
    }
</style><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views/partials/mobile-menu.blade.php ENDPATH**/ ?>