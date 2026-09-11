<?php $__env->startSection('title', 'Mipangilio ya Profaili'); ?>
<?php $__env->startSection('page-title', 'Mipangilio ya Akaunti ya Admin'); ?>

<?php $__env->startSection('content'); ?>


<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(session('info')): ?>
    <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-info-circle-fill me-2"></i> <?php echo e(session('info')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Tafadhali sahihisha makosa yafuatayo:</strong>
        <ul class="mb-0 mt-2">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        
        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-person-circle fs-4 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Maelezo ya Akaunti</h5>
                        <p class="text-muted small mb-0">Badilisha jina lako, barua pepe, na namba ya simu</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('admin.settings.profile.update')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    
                    <div class="text-center mb-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <span class="fw-bold text-primary" style="font-size: 2rem;">
                                <?php echo e(strtoupper(substr(Auth::user()->full_name, 0, 1))); ?>

                            </span>
                        </div>
                        <h6 class="fw-bold"><?php echo e(Auth::user()->full_name); ?></h6>
                        <span class="badge bg-danger">Msimamizi Mkuu</span>
                    </div>

                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label fw-semibold">
                            <i class="bi bi-person me-1"></i> Jina Kamili
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?php echo e(old('full_name', Auth::user()->full_name)); ?>" 
                               placeholder="Mf: Msimamizi Mkuu" required>
                        <?php $__errorArgs = ['full_name'];
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
                        <label for="email" class="form-label fw-semibold">
                            <i class="bi bi-envelope me-1"></i> Barua Pepe
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo e(old('email', Auth::user()->email)); ?>" 
                                   placeholder="Mf: admin@example.com" required>
                        </div>
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

                    
                    <div class="mb-4">
                        <label for="phone_number" class="form-label fw-semibold">
                            <i class="bi bi-telephone me-1"></i> Namba ya Simu
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">+255</span>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                                   value="<?php echo e(old('phone_number', ltrim(Auth::user()->phone_number, '+255'))); ?>" 
                                   placeholder="700000000">
                        </div>
                        <small class="text-muted">Namba mpya itahitaji kuthibitishwa kwa OTP.</small>
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
                    </div>

                    
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 mb-3">
                        <div>
                            <h6 class="fw-semibold mb-1">
                                <i class="bi bi-phone-check me-2 text-primary"></i>Namba ya Simu Imethibitishwa?
                            </h6>
                            <p class="text-muted small mb-0">
                                <?php if(Auth::user()->is_phone_verified): ?>
                                    Namba yako imethibitishwa.
                                <?php else: ?>
                                    Thibitisha namba yako ili kuongeza usalama.
                                <?php endif; ?>
                            </p>
                        </div>
                        <div>
                            <?php if(Auth::user()->is_phone_verified): ?>
                                <span class="badge bg-success rounded-pill px-3 py-2">
                                    <i class="bi bi-check-circle-fill"></i> Imethibitishwa
                                </span>
                            <?php else: ?>
                                <a href="<?php echo e(route('admin.phone.verify')); ?>" class="btn btn-outline-primary btn-sm">
                                    Thibitisha Sasa
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Hifadhi Mabadiliko
                        </button>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-lock-fill fs-4 text-warning"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Badilisha Nywila</h5>
                        <p class="text-muted small mb-0">Hakikisha unatumia nywila imara na ya kipekee</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('admin.password.update')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold">Nywila ya Sasa</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" class="form-control" id="current_password" name="current_password" 
                                   placeholder="Ingiza nywila ya sasa" required>
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
                        <label for="password" class="form-label fw-semibold">Nywila Mpya</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Angalau vibambo 8" required>
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
                        <label for="password_confirmation" class="form-label fw-semibold">Thibitisha Nywila Mpya</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" 
                                   placeholder="Rudia nywila mpya" required>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-shield-lock me-1"></i> Badilisha Nywila
                        </button>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-shield-check fs-4 text-success"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Usalama wa Akaunti</h5>
                        <p class="text-muted small mb-0">Simamia usalama wa akaunti yako</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-semibold">Aina ya Akaunti</h6>
                        <p class="mb-0 text-muted small">Una haki za msimamizi mkuu.</p>
                    </div>
                    <div>
                        <span class="badge bg-danger px-3 py-2">Super Admin</span>
                    </div>
                </div>

                <hr>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-semibold">Tarehe ya Usajili</h6>
                        <p class="mb-0 text-muted small">Uliyejiunga tarehe hii.</p>
                    </div>
                    <div>
                        <span class="text-muted"><?php echo e(Auth::user()->created_at->format('d M Y')); ?></span>
                    </div>
                </div>

                <hr>

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-semibold">Mwisho wa Kubadilisha Nywila</h6>
                        <p class="mb-0 text-muted small">Mara ya mwisho kubadilisha nywila.</p>
                    </div>
                    <div>
                        <span class="text-muted"><?php echo e(Auth::user()->updated_at ? Auth::user()->updated_at->format('d M Y, H:i') : 'Haijabadilishwa'); ?></span>
                    </div>
                </div>

                <hr>

                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-semibold">Vikao vya Kuingia</h6>
                        <p class="mb-0 text-muted small">Simamia vifaa vilivyoingia kwenye akaunti.</p>
                    </div>
                    <div>
                      <a href="<?php echo e(route('admin.settings.sessions')); ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-laptop me-1"></i> Tazama Vikao
                      </a>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-link-45deg me-2"></i>Viungo vya Haraka</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="<?php echo e(route('admin.users.index')); ?>" class="text-decoration-none">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <i class="bi bi-people fs-3 text-primary"></i>
                                <p class="mb-0 mt-2 fw-semibold">Watumiaji Wote</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?php echo e(route('admin.pricing.index')); ?>" class="text-decoration-none">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <i class="bi bi-currency-dollar fs-3 text-success"></i>
                                <p class="mb-0 mt-2 fw-semibold">Bei & Uchumi</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?php echo e(route('admin.reports.finance')); ?>" class="text-decoration-none">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <i class="bi bi-bar-chart fs-3 text-warning"></i>
                                <p class="mb-0 mt-2 fw-semibold">Ripoti za Fedha</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-decoration-none">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <i class="bi bi-speedometer2 fs-3 text-info"></i>
                                <p class="mb-0 mt-2 fw-semibold">Dashboard</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
    }
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }
    .form-control:focus, .form-select:focus {
        border-color: #FF6B35;
        box-shadow: 0 0 0 3px rgba(255,107,53,0.1);
    }
    .badge.bg-success {
        background-color: #d1e7dd !important;
        color: #0f5132 !important;
    }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views/admin/settings/profile.blade.php ENDPATH**/ ?>