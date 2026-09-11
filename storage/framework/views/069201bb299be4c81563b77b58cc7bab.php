<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['messages' => [], 'orderId' => null, 'height' => '300px']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['messages' => [], 'orderId' => null, 'height' => '300px']); ?>
<?php foreach (array_filter((['messages' => [], 'orderId' => null, 'height' => '300px']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="chat-container border rounded-3 p-3 bg-light">
    <div class="chat-messages mb-3" style="height: <?php echo e($height); ?>; overflow-y: auto;" id="chatMessages">
        <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="d-flex <?php echo e($msg['sender_id'] == Auth::id() ? 'justify-content-end' : 'justify-content-start'); ?> mb-2">
                <div class="p-2 rounded-3 <?php echo e($msg['sender_id'] == Auth::id() ? 'bg-primary text-white' : 'bg-white border'); ?>" style="max-width: 75%;">
                    <div class="small"><?php echo e($msg['message']); ?></div>
                    <div class="small text-muted mt-1" style="font-size: 0.7rem;">
                        <?php echo e($msg['sender_name'] ?? 'Mimi'); ?> • <?php echo e(\Carbon\Carbon::parse($msg['created_at'])->format('H:i')); ?>

                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-chat-dots fs-1"></i>
                <p>Bado hakuna mazungumzo. Anza kuandika hapa chini.</p>
            </div>
        <?php endif; ?>
    </div>

    <form method="POST" action="<?php echo e(route('chat.send', $orderId)); ?>" class="d-flex gap-2">
        <?php echo csrf_field(); ?>
        <input type="text" name="message" class="form-control" placeholder="Andika ujumbe..." required>
        <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i></button>
    </form>
</div><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\components\chat-box.blade.php ENDPATH**/ ?>