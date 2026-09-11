<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['align' => 'right', 'width' => '48']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['align' => 'right', 'width' => '48']); ?>
<?php foreach (array_filter((['align' => 'right', 'width' => '48']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div <?php echo e($attributes->merge(['class' => 'dropdown'])); ?>>
    <div class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <?php echo e($trigger); ?>

    </div>
    <div class="dropdown-menu dropdown-menu-end">
        <?php echo e($content); ?>

    </div>
</div>
<?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\components\dropdown.blade.php ENDPATH**/ ?>