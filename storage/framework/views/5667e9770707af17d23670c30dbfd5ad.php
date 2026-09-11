<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['order']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['order']); ?>
<?php foreach (array_filter((['order']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h6 class="fw-bold mb-1"><?php echo e($order['order_number'] ?? 'GPOA-XXXX'); ?></h6>
                <p class="text-muted small mb-1">
                    <i class="bi bi-person"></i> <?php echo e($order['customer_name'] ?? 'Mteja'); ?>

                </p>
                <p class="text-muted small mb-1">
                    <i class="bi bi-box"></i> <?php echo e($order['service'] ?? 'Huduma'); ?>

                </p>
                <p class="text-muted small mb-0">
                    <i class="bi bi-geo-alt"></i> <?php echo e(\Illuminate\Support\Str::limit($order['address'] ?? 'Anwani', 30)); ?>

                </p>
            </div>
            <div class="text-end">
                <span class="badge 
                    <?php if(($order['status'] ?? '') == 'delivered'): ?> bg-success 
                    <?php elseif(($order['status'] ?? '') == 'cancelled'): ?> bg-danger 
                    <?php elseif(($order['status'] ?? '') == 'out_for_delivery'): ?> bg-warning text-dark 
                    <?php else: ?> bg-info <?php endif; ?>">
                    <?php echo e(ucfirst(str_replace('_', ' ', $order['status'] ?? 'pending'))); ?>

                </span>
                <h6 class="mt-2 mb-0">TZS <?php echo e(number_format($order['total'] ?? 0)); ?></h6>
                <small class="text-muted"><?php echo e($order['created_at'] ?? 'Hivi karibuni'); ?></small>
            </div>
        </div>
        <?php echo e($slot ?? ''); ?>

    </div>
</div><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\components\order-card.blade.php ENDPATH**/ ?>