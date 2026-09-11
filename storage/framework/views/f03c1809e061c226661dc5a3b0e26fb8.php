<?php $__env->startSection('title', 'Maagizo ya Jumla'); ?>
<?php $__env->startSection('page-title', 'Maagizo Yangu ya Jumla'); ?>

<?php $__env->startSection('content'); ?>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <h5 class="fw-bold"><i class="bi bi-truck me-2"></i>Orodha ya Maagizo ya Jumla</h5>
    </div>
    <div class="card-body p-0">
        <?php if(isset($orders) && count($orders) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Agizo #</th>
                            <th>Muuzaji Jumla</th>
                            <th>Bidhaa</th>
                            <th>Jumla (TZS)</th>
                            <th>Hali</th>
                            <th>Tarehe</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($order['number']); ?></td>
                            <td><?php echo e($order['wholesaler']); ?></td>
                            <td><?php echo e($order['items']); ?></td>
                            <td><?php echo e(number_format($order['total'])); ?></td>
                            <td>
                                <?php if($order['status'] == 'dispatched'): ?>
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">Imesafirishwa</span>
                                <?php elseif($order['status'] == 'delivered'): ?>
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">Imekamilika</span>
                                <?php elseif($order['status'] == 'cancelled'): ?>
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">Imefutwa</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill px-3 py-2"><?php echo e(ucfirst($order['status'])); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($order['created_at']); ?></td>
                            <td>
                                <?php if(in_array($order['status'], ['dispatched', 'confirmed', 'processing'])): ?>
                                    <a href="<?php echo e(route('retailer.orders.wholesale.tracking', $order['id'])); ?>" 
                                       class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="bi bi-geo-alt"></i> Fuatilia
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h5 class="mt-3">Hakuna Maagizo ya Jumla</h5>
                <p class="text-muted">Bado hujaweka maagizo yoyote ya jumla.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="text-center mt-4">
    <a href="<?php echo e(route('retailer.procurement.browse')); ?>" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
        <i class="bi bi-cart-plus me-2"></i> Nunua Bidhaa za Jumla
    </a>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\retailer\orders\wholesale_orders.blade.php ENDPATH**/ ?>