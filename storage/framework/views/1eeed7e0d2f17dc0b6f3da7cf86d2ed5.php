<?php $__env->startSection('title', 'Maagizo Yanayoendelea'); ?>
<?php $__env->startSection('page-title', 'Maagizo Yanayoshughulikiwa Sasa'); ?>

<?php $__env->startSection('content'); ?>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold"><i class="bi bi-truck me-2 text-primary"></i>Maagizo Yanayoendelea (<?php echo e(isset($activeOrders) ? count($activeOrders) : 0); ?>)</h5>
                <p class="text-muted">Maagizo uliyokubali na yanayosubiri kufikishwa kwa wateja.</p>
            </div>
            <a href="<?php echo e(route('retailer.orders.incoming')); ?>" class="btn btn-outline-warning btn-sm">
                <i class="bi bi-bell"></i> Maagizo Mapya
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if(isset($activeOrders) && count($activeOrders) > 0): ?>
            <?php $__currentLoopData = $activeOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border-bottom p-4 order-row">
                <div class="row">
                    <div class="col-md-8">
                        
                        <div class="d-flex align-items-center mb-2 flex-wrap">
                            <span class="fw-bold fs-5 me-3"><?php echo e($order['order_number']); ?></span>
                            <?php if($order['status'] == 'accepted'): ?>
                                <span class="badge bg-info">Imekubaliwa</span>
                            <?php elseif($order['status'] == 'picked_up'): ?>
                                <span class="badge bg-primary">Imeshachukuliwa</span>
                            <?php elseif($order['status'] == 'out_for_delivery'): ?>
                                <span class="badge bg-warning text-dark">Njiani</span>
                            <?php endif; ?>
                            
                            <?php if(isset($order['is_urgent']) && $order['is_urgent']): ?>
                                <span class="badge bg-danger ms-2">
                                    <i class="bi bi-lightning"></i> Haraka
                                </span>
                            <?php endif; ?>
                            
                            <span class="text-muted ms-auto small">
                                <i class="bi bi-clock"></i> <?php echo e($order['created_at'] ?? 'Hivi karibuni'); ?>

                            </span>
                        </div>
                        
                        
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="mb-1"><i class="bi bi-person me-2"></i><?php echo e($order['customer'] ?? 'Mteja'); ?> (<?php echo e($order['customer_phone'] ?? 'Haipo'); ?>)</p>
                                <p class="mb-1"><i class="bi bi-geo-alt me-2"></i><?php echo e($order['address'] ?? 'Haipo'); ?></p>
                                <?php if(isset($order['distance'])): ?>
                                    <p class="mb-1"><i class="bi bi-signpost me-2"></i>Umbali: <?php echo e($order['distance']); ?> km</p>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1"><i class="bi bi-box me-2"></i><?php echo e($order['service'] ?? 'Huduma'); ?></p>
                                <p class="mb-1"><i class="bi bi-cash me-2"></i>Jumla: TZS <?php echo e(number_format($order['total'] ?? 0)); ?></p>
                                
                                
                                <?php if(isset($order['items']) && count($order['items']) > 0): ?>
                                    <div class="mt-2">
                                        <?php $__currentLoopData = $order['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-light text-dark me-2 mb-1">
                                                <?php echo e($item['name'] ?? 'Bidhaa'); ?> x<?php echo e($item['quantity'] ?? 1); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        
                        <div class="alert alert-info py-2 mt-3">
                            <i class="bi bi-clock-fill me-2"></i>
                            <strong>Muda wa Kufika:</strong> 
                            <?php if(isset($order['estimated_delivery'])): ?>
                                <?php if(is_object($order['estimated_delivery'])): ?>
                                    <?php echo e($order['estimated_delivery']->format('H:i')); ?> (<?php echo e($order['estimated_delivery']->diffForHumans()); ?>)
                                <?php else: ?>
                                    <?php echo e($order['estimated_delivery']); ?>

                                <?php endif; ?>
                            <?php else: ?>
                                Dakika 20
                            <?php endif; ?>
                        </div>
                        
                        <?php if(isset($order['accepted_at'])): ?>
                            <p class="text-muted small mb-0">
                                <i class="bi bi-check-circle me-1"></i> Imekubaliwa: <?php echo e($order['accepted_at']); ?>

                            </p>
                        <?php endif; ?>
                    </div>
            
                    
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="d-grid gap-2">
                            <?php if($order['status'] == 'accepted'): ?>
                                <form method="POST" action="<?php echo e(route('retailer.orders.pickup', $order['id'])); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-box-arrow-up"></i> Nimeshachukua Stock
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="<?php echo e(route('retailer.orders.deliver', $order['id'])); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check2-circle"></i> Nimefikisha
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            
                            <a href="<?php echo e(route('retailer.chat.show', $order['id'])); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-chat-dots"></i> Fungua Chat
                            </a>
                            
                            
                            <a href="tel:<?php echo e($order['customer_phone'] ?? '#'); ?>" class="btn btn-outline-primary">
                                <i class="bi bi-telephone"></i> Mpigie
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-check-circle display-1 text-muted"></i>
                <h5 class="mt-3">Hakuna Maagizo Yanayoendelea</h5>
                <p class="text-muted">Huna maagizo yanayoshughulikiwa kwa sasa.</p>
                <a href="<?php echo e(route('retailer.orders.incoming')); ?>" class="btn btn-primary mt-3">
                    <i class="bi bi-bell"></i> Tazama Maagizo Mapya
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>


<?php if(isset($activeOrders) && count($activeOrders) > 0): ?>
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-info bg-opacity-10">
            <div class="card-body text-center py-3">
                <h6 class="text-muted mb-2">Imekubaliwa</h6>
                <h3 class="mb-0"><?php echo e($activeOrders->where('status', 'accepted')->count()); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10">
            <div class="card-body text-center py-3">
                <h6 class="text-muted mb-2">Imeshachukuliwa</h6>
                <h3 class="mb-0"><?php echo e($activeOrders->where('status', 'picked_up')->count()); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10">
            <div class="card-body text-center py-3">
                <h6 class="text-muted mb-2">Njiani</h6>
                <h3 class="mb-0"><?php echo e($activeOrders->where('status', 'out_for_delivery')->count()); ?></h3>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .badge.bg-info { background-color: #e6f3ff !important; color: #0056b3 !important; }
    .badge.bg-primary { background-color: #cfe2ff !important; color: #084298 !important; }
    .badge.bg-warning { background-color: #fff3cd !important; color: #856404 !important; }
    .badge.bg-danger { background-color: #f8d7da !important; color: #842029 !important; }
    .badge.bg-success { background-color: #d1e7dd !important; color: #0f5132 !important; }
    .bg-opacity-10 { --bs-bg-opacity: 0.1; }
    .alert-info { background-color: #cff4fc; border-color: #b6effb; color: #055160; }
    .order-row:hover { background-color: #fafbfc; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirm before pickup
        document.querySelectorAll('form[action*="pickup"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Una uhakika umeshachukua stock kwa agizo hili?')) {
                    e.prevent Default();
                }
            }); 
        });
        
        // Confirm before delivery
        document.querySelectorAll('form[action*="deliver"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Una uhakika umefikisha agizo hili kwa mteja?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\retailer\orders\active.blade.php ENDPATH**/ ?>