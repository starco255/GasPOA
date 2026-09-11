<?php $__env->startSection('title', 'Thibitisha Namba ya Simu'); ?>
<?php $__env->startSection('page-heading', 'Ingiza Namba ya Uthibitisho (OTP)'); ?>

<?php $__env->startSection('content'); ?>
<p class="text-muted mb-4">
    Tumetuma namba ya siri (OTP) yenye tarakimu 6 kwenye barua pepe yako.
    Ingiza hapa chini ili kuthibitisha namba yako ya simu.
</p>

<form method="POST" action="<?php echo e(route('verification.phone.verify')); ?>" id="otpForm">
    <?php echo csrf_field(); ?>

    <div class="mb-4">
        <label for="otp" class="form-label">Namba ya Uthibitisho (OTP)</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-key"></i></span>
            <input id="otp" type="text" 
                   class="form-control <?php $__errorArgs = ['otp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                   name="otp" required autofocus maxlength="6" 
                   placeholder="Mf: 123456"
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        </div>
        <?php $__errorArgs = ['otp'];
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
        <button type="submit" class="btn btn-primary btn-lg" id="verifyBtn">
            <i class="bi bi-check-lg"></i> Thibitisha
        </button>
    </div>
</form>

<div class="mt-4 text-center">
    <form method="POST" action="<?php echo e(route('verification.phone.resend')); ?>" id="resendForm">
        <?php echo csrf_field(); ?>
        <p class="text-muted small mb-2">Hukupokea OTP?</p>
        <button type="submit" class="btn btn-link text-decoration-none" id="resendBtn">
            <i class="bi bi-arrow-repeat"></i> Tuma OTP Mpya
        </button>
    </form>
</div>

<div class="mt-3 text-center">
    <div class="alert alert-light border d-inline-flex align-items-center gap-2 mb-0 py-2 px-3">
        <i class="bi bi-hourglass-split text-warning"></i>
        <span class="small">OTP inaisha ndani ya <strong id="otpCountdown">--:--</strong></span>
    </div>
</div>

<hr>

<form method="POST" action="<?php echo e(route('logout')); ?>">
    <?php echo csrf_field(); ?>
    <button type="submit" class="btn btn-link text-danger text-decoration-none">
        <i class="bi bi-arrow-left"></i> Tumia Akaunti Nyingine
    </button>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const otpInput = document.getElementById('otp');
        const otpForm = document.getElementById('otpForm');
        const verifyBtn = document.getElementById('verifyBtn');
        const resendForm = document.getElementById('resendForm');
        const resendBtn = document.getElementById('resendBtn');
        
        // Auto-focus and limit input
        otpInput.focus();
        
        // Submit form automatically when 6 digits are entered
        otpInput.addEventListener('input', function() {
            if (this.value.length === 6) {
                verifyBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Inathibitisha...';
                verifyBtn.disabled = true;
                otpForm.submit();
            }
        });
        
        const expiresAt = <?php echo e($expiresAt ?? 'null'); ?>;
        const countdown = document.getElementById('otpCountdown');
        let countdownInterval = null;

        function updateCountdown() {
            const remaining = expiresAt ? expiresAt - Math.floor(Date.now() / 1000) : 0;
            if (remaining <= 0) {
                countdown.textContent = 'imeisha';
                countdown.classList.add('text-danger');
                if (countdownInterval) clearInterval(countdownInterval);
                return;
            }

            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;
            countdown.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        updateCountdown();
        countdownInterval = setInterval(updateCountdown, 1000);
        
        resendForm.addEventListener('submit', function(e) {
            resendBtn.disabled = true;
            resendBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Inatuma...';
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.guest', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\auth\verify-phone.blade.php ENDPATH**/ ?>