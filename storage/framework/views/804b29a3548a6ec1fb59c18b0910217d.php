<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['name', 'show' => false, 'maxWidth' => 'modal-lg', 'focusable' => false]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['name', 'show' => false, 'maxWidth' => 'modal-lg', 'focusable' => false]); ?>
<?php foreach (array_filter((['name', 'show' => false, 'maxWidth' => 'modal-lg', 'focusable' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div x-data="{ shown: <?php echo \Illuminate\Support\Js::from($show)->toHtml() ?> }" x-show="shown" x-on:close.stop="shown = false" x-on:keydown.escape.window="shown = false" class="modal d-block" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog <?php echo e($maxWidth); ?>" role="document">
        <div class="modal-content p-3">
            <?php echo e($slot); ?>

        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\components\modal.blade.php ENDPATH**/ ?>