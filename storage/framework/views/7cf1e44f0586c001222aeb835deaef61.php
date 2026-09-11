<?php $__env->startSection('title', 'Mipangilio ya Akaunti'); ?>
<?php $__env->startSection('page-title', 'Mipangilio ya Akaunti Yangu'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8 mx-auto">
        
        
        <?php if($errors->any()): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Tafadhali sahihisha makosa yafuatayo:</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        
        
        
        <div class="card border-0 shadow-sm rounded-4 mt-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-shield-check me-2 text-success"></i>Usalama wa Akaunti</h5>
                <p class="text-muted">Badilisha nywila yako na uthibitishe namba yako.</p>
            </div>
            <div class="card-body">
                
                <div class="mb-4">
                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="collapse" data-bs-target="#changePasswordCollapse">
                        <i class="bi bi-lock me-2"></i> Badilisha Nywila
                        <i class="bi bi-chevron-down ms-2"></i>
                    </button>
                    
                    <div class="collapse mt-3" id="changePasswordCollapse">
                        <form method="POST" action="<?php echo e(route('wholesaler.password.update')); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <div class="mb-3">
                                <label class="form-label">Nywila ya Sasa</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-danger small"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nywila Mpya</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="text-danger small"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Thibitisha Nywila</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning">Sasisha Nywila</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div> <br>

        
        
        
        <form method="POST" action="<?php echo e(route('wholesaler.profile.update')); ?>" id="mainProfileForm">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-person-circle me-2 text-primary"></i>Maelezo ya Akaunti</h5>
                    <p class="text-muted">Sasisha taarifa zako za msingi za akaunti.</p>
                </div>
                <div class="card-body">

                    
                    <div class="mb-4">
                        <label for="phone_number" class="form-label fw-semibold">
                            <i class="bi bi-telephone me-1"></i> Namba ya Simu
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">+255</span>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                                  
                                   value="<?php echo e(old('phone_number', ltrim($businessProfile->phone_number ?? Auth::user()->phone_number, '+255'))); ?>"
                                   placeholder="615004300" required>
                        </div>
                        <small class="text-muted">Namba itatumika kwa uthibitishaji na arifa.</small>
                        <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger small"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        
                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                    <div>
                        <h6 class="fw-semibold mb-1">
                            <i class="bi bi-phone-check me-2 text-primary"></i>Namba ya Simu Imethibitishwa?
                        </h6>
                        <p class="text-muted small mb-0">
                            <?php if(Auth::user()->is_phone_verified): ?>
                                Namba yako imethibitishwa kwa OTP.
                            <?php else: ?>
                                Thibitisha namba yako ili kuongeza usalama.
                            <?php endif; ?>
                        </p>
                    </div>
                    <div>
                        <?php if(Auth::user()->is_phone_verified): ?>
                            <span class="badge bg-success rounded-pill px-4 py-2">
                                <i class="bi bi-check-circle-fill"></i> Imethibitishwa
                            </span>
                        <?php else: ?>
                            <a href="<?php echo e(route('verification.phone.notice')); ?>" class="btn btn-outline-primary btn-sm">
                                Thibitisha Sasa
                            </a>
                        <?php endif; ?>
                    </div>
                    </div>
                    </div>

                    
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold">
                            <i class="bi bi-envelope me-1"></i> Barua Pepe
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo e(old('email', Auth::user()->email)); ?>" 
                               placeholder="Mf: jina@example.com">
                        <small class="text-muted">Barua pepe itatumika kwa arifa na kurejesha nywila.</small>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger small"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-bell me-2 text-warning"></i>Mapendeleo ya Arifa</h5>
                    <p class="text-muted">Chagua arifa unazotaka kupokea.</p>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="notify_new_order" name="notify_new_order" value="1" 
                                   <?php echo e(old('notify_new_order', $businessProfile->notify_new_order ?? true) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold" for="notify_new_order">
                                <i class="bi bi-bell-fill text-primary me-1"></i> Arifa kwa agizo jipya
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="notify_chat" name="notify_chat" value="1" 
                                   <?php echo e(old('notify_chat', $businessProfile->notify_chat ?? true) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold" for="notify_chat">
                                <i class="bi bi-chat-dots text-info me-1"></i> Arifa kwa ujumbe mpya
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo e(route('wholesaler.dashboard')); ?>" class="btn btn-outline-secondary px-4">Ghairi</a>
                <button type="submit" class="btn btn-primary px-5">
                    <i class="bi bi-check-lg"></i> Hifadhi Mabadiliko
                </button>
            </div>

            <div class="alert alert-info border-0 shadow-sm rounded-3 mt-4">
                <i class="bi bi-lightbulb-fill me-2"></i>
                <strong>Kidokezo:</strong> Hakikisha namba yako ya simu na barua pepe ni sahihi.
            </div>
        </form>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle mobile money details
        const mobileMoneyCheckbox = document.getElementById('accept_mobile_money');
        const mobileMoneyDetails = document.getElementById('mobileMoneyDetails');
        
        if (mobileMoneyCheckbox && mobileMoneyDetails) {
            mobileMoneyDetails.style.display = mobileMoneyCheckbox.checked ? 'block' : 'none';
            mobileMoneyCheckbox.addEventListener('change', function() {
                mobileMoneyDetails.style.display = this.checked ? 'block' : 'none';
            });
        }
        
        // Toggle bank details
        const bankCheckbox = document.getElementById('accept_bank');
        const bankDetails = document.getElementById('bankDetails');
        
        if (bankCheckbox && bankDetails) {
            bankDetails.style.display = bankCheckbox.checked ? 'block' : 'none';
            bankCheckbox.addEventListener('change', function() {
                bankDetails.style.display = this.checked ? 'block' : 'none';
            });
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .form-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; }
    .form-switch .form-check-input:checked { background-color: #198754; border-color: #198754; }
    .card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .card:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
    .form-control:focus, .form-select:focus { border-color: #FF6B35; box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
    .badge.bg-success { background-color: #d1e7dd !important; color: #0f5132 !important; }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\wholesaler\account\edit.blade.php ENDPATH**/ ?>