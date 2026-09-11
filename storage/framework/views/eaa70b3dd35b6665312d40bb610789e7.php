<!doctype html>
<html lang="sw">
<body style="margin:0;padding:24px;background:#f6f7fb;font-family:Arial,sans-serif;color:#18213a;">
    <div style="max-width:560px;margin:auto;background:#fff;border-radius:14px;padding:32px;">
        <h1 style="margin:0 0 16px;color:#f15a24;font-size:26px;">GasPOA</h1>
        <p>Habari <?php echo e($user->full_name); ?>,</p>
        <p>Umeomba kubadilisha nywila yako. Tumia OTP hii ndani ya dakika 5:</p>
        <p style="font-size:30px;font-weight:700;letter-spacing:8px;color:#18213a;text-align:center;padding:16px;background:#fff3ed;border-radius:10px;"><?php echo e($otp); ?></p>
        <p>Usimpe mtu mwingine namba hii. Kama hukuomba kubadilisha nywila, puuza email hii.</p>
        <p style="color:#667085;font-size:13px;margin-top:24px;">Huu ni ujumbe wa moja kwa moja kutoka GasPOA.</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\emails\password-reset-otp.blade.php ENDPATH**/ ?>