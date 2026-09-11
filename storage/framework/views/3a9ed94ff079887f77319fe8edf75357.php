<?php $__env->startSection('title', 'Umesahau Nywila'); ?>
<?php $__env->startSection('page-heading', 'Rudisha Nywila Yako'); ?>

<?php $__env->startSection('content'); ?>
<p class="text-muted mb-4">
    Usijali! Tuambie namba ya simu au barua pepe yako. Tutatuma OTP ya tarakimu 6 kwenye barua pepe iliyosajiliwa.
</p>

<?php if(session('status')): ?>
    <div class="alert alert-success mb-4">
        <i class="bi bi-check-circle-fill"></i> <?php echo e(session('status')); ?>

    </div>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('password.email')); ?>">
    <?php echo csrf_field(); ?>

    <div class="mb-4">
        <label for="login" class="form-label">Namba ya Simu au Barua Pepe</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input id="login" type="text" class="form-control <?php $__errorArgs = ['login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                   name="login" value="<?php echo e(old('login')); ?>" required autofocus 
                   placeholder="Mf: 0712345678 au jina@example.com">
        </div>
        <?php $__errorArgs = ['login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="invalid-feedback d-block"><?php echo e($message); ?></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-send"></i> Tuma OTP ya Kubadilisha Nywila
        </button>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer-links'); ?>
    <a href="<?php echo e(route('login')); ?>" class="text-decoration-none">
        <i class="bi bi-arrow-left"></i> Rudi kwenye Ingia
    </a>
    <br>
    <span class="text-muted">Huna akaunti?</span>
    <a href="<?php echo e(route('register')); ?>" class="text-decoration-none fw-bold">
        Jisajili
    </a>
    <hr>
    <p class="small text-muted">
        <i class="bi bi-phone"></i> Unaweza pia kupiga <strong>*150*99#</strong> kwa msaada.
    </p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\auth\forgot-password.blade.php ENDPATH**/ ?>