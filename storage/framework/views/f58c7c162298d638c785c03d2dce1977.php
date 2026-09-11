<?php $__env->startSection('title', 'Vikao vya Kuingia'); ?>
<?php $__env->startSection('page-title', 'Vikao vya Kuingia'); ?>

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

<div class="row">
    <div class="col-lg-8 mx-auto">
        
        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-laptop fs-4 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Vikao Vilivyoingia</h5>
                            <p class="text-muted small mb-0">Simamia vifaa vilivyoingia kwenye akaunti yako</p>
                        </div>
                    </div>
                    <?php if(count($sessions) > 1): ?>
                        <form method="POST" action="<?php echo e(route('admin.sessions.destroy-all')); ?>" 
                              onsubmit="return confirm('Una uhakika unataka kufunga vikao vyote vingine? Kikao cha sasa kitabaki pekee.')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-shield-x me-1"></i> Funga Vikao Vyote Vingine
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0">
                <?php $__empty_1 = true; $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="border-bottom p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-2">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                    <?php if($session['is_current']): ?>
                                        <i class="bi bi-laptop-fill text-primary fs-5"></i>
                                    <?php elseif(strpos($session['user_agent'], 'Android') !== false || strpos($session['user_agent'], 'iPhone') !== false): ?>
                                        <i class="bi bi-phone-fill text-success fs-5"></i>
                                    <?php else: ?>
                                        <i class="bi bi-laptop text-secondary fs-5"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-0">
                                        <?php echo e($session['user_agent'] ?? 'Haijulikani'); ?>

                                        <?php if($session['is_current']): ?>
                                            <span class="badge bg-success ms-2">Kikao cha Sasa</span>
                                        <?php endif; ?>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="bi bi-globe me-1"></i> IP: <?php echo e($session['ip_address']); ?>

                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i> 
                                        <?php if($session['is_current']): ?>
                                            Active sasa
                                        <?php else: ?>
                                            Mwisho wa shughuli: <?php echo e($session['last_activity_human']); ?>

                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-2 mt-md-0">
                            <?php if(!$session['is_current']): ?>
                                <form method="POST" action="<?php echo e(route('admin.sessions.destroy', $session['id'])); ?>"
                                      onsubmit="return confirm('Una uhakika unataka kufunga kikao hiki?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-box-arrow-right me-1"></i> Funga Kikao
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-info px-3 py-2">Unatumia Sasa</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-5">
                    <i class="bi bi-laptop fs-1 text-muted"></i>
                    <h5 class="mt-3">Hakuna vikao vilivyopatikana</h5>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-shield-check me-2 text-success"></i>Vidokezo vya Usalama</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="bg-light rounded-3 p-3">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Angalia vikao mara kwa mara</strong>
                            <p class="text-muted small mb-0 mt-1">Hakikisha vifaa vinavyoingia kwenye akaunti yako ni vyako.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded-3 p-3">
                            <i class="bi bi-shield-lock text-primary me-2"></i>
                            <strong>Funga vikao visivyojulikana</strong>
                            <p class="text-muted small mb-0 mt-1">Ukiona kifaa kisichojulikana, fungia kikao mara moja.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded-3 p-3">
                            <i class="bi bi-key text-warning me-2"></i>
                            <strong>Badilisha nywila mara kwa mara</strong>
                            <p class="text-muted small mb-0 mt-1">Badilisha nywila yako mara kwa mara ili kuongeza usalama.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light rounded-3 p-3">
                            <i class="bi bi-box-arrow-right text-danger me-2"></i>
                            <strong>Toka baada ya kumaliza</strong>
                            <p class="text-muted small mb-0 mt-1">Hakikisha unatoka kwenye akaunti unapomaliza kutumia kifaa cha umma.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="mt-4">
            <a href="<?php echo e(route('admin.settings.profile')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Rudi kwenye Mipangilio
            </a>
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
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\admin\settings\sessions.blade.php ENDPATH**/ ?>