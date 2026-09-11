<?php $__env->startSection('title', 'Ingia'); ?>
<?php $__env->startSection('page-heading', 'Ingia kwenye Akaunti Yako'); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('login')); ?>">
    <?php echo csrf_field(); ?>

    
    <div class="mb-3">
        <label for="login" class="form-label">Barua Pepe au Namba ya Simu</label>
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
                   placeholder=" Mf: jinalako@example.com | +255615004300">
        </div>
        <?php $__errorArgs = ['login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="invalid-feedback d-block" role="alert"><?php echo e($message); ?></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <small class="text-muted">Unaweza kutumia namba ya simu au barua pepe uliyojisajili nayo.</small>
    </div>

    
    <div class="mb-3">
        <label for="password" class="form-label">Nywila</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input id="password" type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                   name="password" required placeholder="••••••••">
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="invalid-feedback d-block" role="alert"><?php echo e($message); ?></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
        <label class="form-check-label" for="remember">Nikumbuke</label>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-box-arrow-in-right"></i> Ingia
        </button>
    </div>
</form>


<div class="mt-3 text-center auth-footer">
    <a href="<?php echo e(route('password.request')); ?>" class="text-decoration-none">
        <i class="bi bi-question-circle"></i> Umesahau Nywila?
    </a>
    <br>
    <span class="text-muted">Huna akaunti?</span>
    <a href="<?php echo e(route('register')); ?>" class="text-decoration-none fw-bold">
        Jisajili Bure
    </a>
</div>


<div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top border-light">
    <a href="<?php echo e(route('home')); ?>" class="text-decoration-none small fw-semibold" style="color: #FF6B35;">
        <i class="bi bi-arrow-left"></i> Rudi Nyumbani
    </a>
    <span class="text-muted small">
        <i class="bi bi-phone"></i> *150*99#
    </span>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer-links'); ?>
    
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.guest', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\auth\login.blade.php ENDPATH**/ ?>