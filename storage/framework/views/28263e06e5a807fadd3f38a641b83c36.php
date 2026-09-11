<?php $__env->startSection('title', 'Weka Nywila Mpya'); ?>
<?php $__env->startSection('page-heading', 'Weka Nywila Mpya'); ?>

<?php $__env->startSection('content'); ?>
<p class="text-muted mb-4">OTP yako imethibitishwa. Weka nywila mpya yenye usalama.</p>
<form method="POST" action="<?php echo e(route('password.store')); ?>">
    <?php echo csrf_field(); ?>

    <div class="mb-3">
        <label for="password" class="form-label">Nywila Mpya</label>
        <input id="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" type="password" name="password" required autofocus>
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="invalid-feedback d-block"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="mb-4">
        <label for="password_confirmation" class="form-label">Thibitisha Nywila</label>
        <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required>
    </div>
    <div class="d-grid"><button class="btn btn-primary btn-lg" type="submit">Weka Nywila Mpya</button></div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\auth\reset-password.blade.php ENDPATH**/ ?>