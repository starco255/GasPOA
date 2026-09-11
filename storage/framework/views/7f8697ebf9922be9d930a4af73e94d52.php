<?php $__env->startSection('title', 'Uidhinishaji wa Biashara'); ?>
<?php $__env->startSection('page-title', 'Akaunti Zinazosubiri Uidhinishaji'); ?>

<?php $__env->startSection('content'); ?>


<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10">
            <div class="card-body text-center py-3">
                <h4 class="mb-0 text-warning"><?php echo e($totalPending ?? 0); ?></h4>
                <small class="text-muted">Zinasubiri Uidhinishaji</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-success bg-opacity-10">
            <div class="card-body text-center py-3">
                <h4 class="mb-0 text-success"><?php echo e($pending->where('is_open', true)->count() ?? 0); ?></h4>
                <small class="text-muted">Zimeidhinishwa</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10">
            <div class="card-body text-center py-3">
                <h4 class="mb-0 text-primary"><?php echo e($pending->where('business_type', 'retailer')->count() ?? 0); ?></h4>
                <small class="text-muted">Wauzaji Rejareja</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-info bg-opacity-10">
            <div class="card-body text-center py-3">
                <h4 class="mb-0 text-info"><?php echo e($pending->where('business_type', 'wholesaler')->count() ?? 0); ?></h4>
                <small class="text-muted">Wauzaji Jumla</small>
            </div>
        </div>
    </div>
</div>


<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 pb-3">
        <form method="GET" action="<?php echo e(route('admin.users.verify')); ?>" id="verifyFilterForm">
            <div class="row align-items-center">
                <div class="col-md-4 mb-2 mb-md-0">
                    <select class="form-select" name="type" onchange="document.getElementById('verifyFilterForm').submit()">
                        <option value="">Aina Zote za Biashara</option>
                        <option value="retailer" <?php echo e(request('type') == 'retailer' ? 'selected' : ''); ?>>Wauzaji Rejareja</option>
                        <option value="wholesaler" <?php echo e(request('type') == 'wholesaler' ? 'selected' : ''); ?>>Wauzaji Jumla</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <select class="form-select" name="status" onchange="document.getElementById('verifyFilterForm').submit()">
                        <option value="">Hali Zote</option>
                        <option value="unverified" <?php echo e(request('status') == 'unverified' ? 'selected' : ''); ?>>Zisizoidhinishwa</option>
                        <option value="verified" <?php echo e(request('status') == 'verified' ? 'selected' : ''); ?>>Zimeidhinishwa</option>
                    </select>
                </div>
                <div class="col-md-4 text-md-end">
                    <?php if(request()->filled('type') || request()->filled('status')): ?>
                        <a href="<?php echo e(route('admin.users.verify')); ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i> Futa Vichujio
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <h5 class="fw-bold"><i class="bi bi-check-circle me-2 text-warning"></i>Maombi ya Biashara (<?php echo e($pending->total() ?? 0); ?>)</h5>
    </div>
    <div class="card-body p-0">
        <?php $__empty_1 = true; $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $biz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="border-bottom p-4">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-2">
                        <h6 class="fw-bold mb-0 me-2"><?php echo e($biz->business_name); ?></h6>
                        <?php if($biz->business_type == 'retailer'): ?>
                            <span class="badge bg-success">Muuza Rejareja</span>
                        <?php else: ?>
                            <span class="badge bg-primary">Muuza Jumla</span>
                        <?php endif; ?>
                        
                        <?php if($biz->is_open): ?>
                            <span class="badge bg-success ms-2">Imeidhinishwa</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark ms-2">Hajaidhinishwa</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="mb-1"><i class="bi bi-person me-2"></i> Mmiliki: <?php echo e($biz->user->full_name ?? 'Haijulikani'); ?></p>
                            <p class="mb-1"><i class="bi bi-telephone me-2"></i> <?php echo e($biz->user->phone_number ?? 'Haipo'); ?></p>
                            <p class="mb-1"><i class="bi bi-envelope me-2"></i> <?php echo e($biz->user->email ?? 'Haipo'); ?></p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-1"><i class="bi bi-geo-alt me-2"></i> <?php echo e($biz->physical_address ?? 'Haipo'); ?></p>
                            <p class="mb-1"><i class="bi bi-file-text me-2"></i> TIN: <?php echo e($biz->tinn_number ?? 'Haipo'); ?></p>
                            <p class="mb-1">
                                <i class="bi bi-shop me-2"></i> 
                                <?php if($biz->can_deliver): ?>
                                    <span class="text-success">Inatoa usafirishaji</span>
                                <?php else: ?>
                                    <span class="text-muted">Haijatoa usafirishaji</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i> Ilisajiliwa: <?php echo e($biz->created_at->format('d M Y, H:i')); ?>

                        (<?php echo e($biz->created_at->diffForHumans()); ?>)
                    </small>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="d-grid gap-2">
                        
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailsModal<?php echo e($biz->id); ?>">
                            <i class="bi bi-eye"></i> Tazama Maelezo Kamili
                        </button>
                        
                        
                        <?php if(!$biz->is_open): ?>
                            <form method="POST" action="<?php echo e(route('admin.verify.approve', $biz->id )); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Una uhakika unataka kuidhinisha biashara hii?')">
                                    <i class="bi bi-check-lg"></i> Idhinisha Biashara
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <form method="POST" action="<?php echo e(route('admin.verify.reject', $biz->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal<?php echo e($biz->id); ?>">
                                <i class="bi bi-x-lg"></i> Kataa Biashara
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="modal fade" id="detailsModal<?php echo e($biz->id); ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-building me-2"></i>
                            <?php echo e($biz->business_name); ?>

                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">Maelezo ya Biashara</h6>
                                <p><strong>Jina:</strong> <?php echo e($biz->business_name); ?></p>
                                <p><strong>Aina:</strong> <?php echo e($biz->business_type == 'retailer' ? 'Muuza Rejareja' : 'Muuza Jumla'); ?></p>
                                <p><strong>TIN:</strong> <?php echo e($biz->tinn_number ?? 'Haipo'); ?></p>
                                <p><strong>Anwani:</strong> <?php echo e($biz->physical_address ?? 'Haipo'); ?></p>
                                <p><strong>GPS:</strong> <?php echo e($biz->shop_latitude); ?>, <?php echo e($biz->shop_longitude); ?></p>
                                <p><strong>Eneo la Huduma:</strong> <?php echo e($biz->service_radius_km ?? '0'); ?> km</p>
                                <p><strong>Usafirishaji:</strong> <?php echo e($biz->can_deliver ? 'Ndiyo' : 'Hapana'); ?></p>
                                <?php if($biz->can_deliver): ?>
                                    <p><strong>Ada kwa km:</strong> TZS <?php echo e(number_format($biz->delivery_fee_per_km ?? 0)); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">Maelezo ya Mmiliki</h6>
                                <p><strong>Jina:</strong> <?php echo e($biz->user->full_name ?? 'Haijulikani'); ?></p>
                                <p><strong>Simu:</strong> <?php echo e($biz->user->phone_number ?? 'Haipo'); ?></p>
                                <p><strong>Barua Pepe:</strong> <?php echo e($biz->user->email ?? 'Haipo'); ?></p>
                                <p><strong>Aina ya Akaunti:</strong> <?php echo e(ucfirst($biz->user->user_type ?? 'N/A')); ?></p>
                                <p><strong>Aliyejiunga:</strong> <?php echo e($biz->user->created_at->format('d M Y') ?? 'N/A'); ?></p>
                                <p>
                                    <strong>Hali:</strong> 
                                    <?php if($biz->is_open): ?>
                                        <span class="badge bg-success">Imeidhinishwa</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Hajaidhinishwa</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        
                        
                        <hr>
                        <h6 class="fw-bold mb-3">Njia za Malipo Zinazokubaliwa</h6>
                        <div class="d-flex gap-3">
                            <?php if($biz->accept_cash): ?>
                                <span class="badge bg-success p-2"><i class="bi bi-cash"></i> Pesa Taslimu</span>
                            <?php else: ?>
                                <span class="badge bg-secondary p-2"><i class="bi bi-cash"></i> Pesa Taslimu</span>
                            <?php endif; ?>
                            
                            <?php if($biz->accept_mobile_money): ?>
                                <span class="badge bg-success p-2"><i class="bi bi-phone"></i> Lipa Namba</span>
                            <?php else: ?>
                                <span class="badge bg-secondary p-2"><i class="bi bi-phone"></i> Lipa Namba</span>
                            <?php endif; ?>
                            
                            <?php if($biz->accept_bank): ?>
                                <span class="badge bg-success p-2"><i class="bi bi-bank"></i> Benki</span>
                            <?php else: ?>
                                <span class="badge bg-secondary p-2"><i class="bi bi-bank"></i> Benki</span>
                            <?php endif; ?>
                        </div>
                        
                        
                        <?php if($biz->accept_mobile_money): ?>
                            <div class="row mt-3">
                                <?php if($biz->mpesa_number): ?>
                                    <div class="col-md-6"><strong>M-Pesa:</strong> <?php echo e($biz->mpesa_number); ?></div>
                                <?php endif; ?>
                                <?php if($biz->halopesa_number): ?>
                                    <div class="col-md-6"><strong>HaloPesa:</strong> <?php echo e($biz->halopesa_number); ?></div>
                                <?php endif; ?>
                                <?php if($biz->airtel_number): ?>
                                    <div class="col-md-6"><strong>Airtel Money:</strong> <?php echo e($biz->airtel_number); ?></div>
                                <?php endif; ?>
                                <?php if($biz->mixx_number): ?>
                                    <div class="col-md-6"><strong>Mixx:</strong> <?php echo e($biz->mixx_number); ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($biz->accept_bank): ?>
                            <div class="row mt-3">
                                <div class="col-md-4"><strong>Benki:</strong> <?php echo e(strtoupper($biz->bank_name ?? 'N/A')); ?></div>
                                <div class="col-md-4"><strong>Akaunti:</strong> <?php echo e($biz->bank_account_number ?? 'N/A'); ?></div>
                                <div class="col-md-4"><strong>Jina:</strong> <?php echo e($biz->bank_account_name ?? 'N/A'); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Funga</button>
                        <?php if(!$biz->is_open): ?>
                            <form method="POST" action="<?php echo e(route('admin.verify.approve', $biz->id)); ?>" class="d-inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-lg"></i> Idhinisha
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="modal fade" id="rejectModal<?php echo e($biz->id); ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="<?php echo e(route('admin.verify.reject', $biz->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="modal-header">
                            <h5 class="modal-title">Thibitisha Kukataa Biashara</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Una uhakika unataka kukataa biashara ya <strong><?php echo e($biz->business_name); ?></strong>?</p>
                            <label for="rejection_reason<?php echo e($biz->id); ?>" class="form-label">Sababu (Hiari):</label>
                            <select class="form-select" name="rejection_reason" id="rejection_reason<?php echo e($biz->id); ?>">
                                <option value="">-- Chagua Sababu --</option>
                                <option value="incomplete">Taarifa hazijakamilika</option>
                                <option value="duplicate">Biashara imeshasajiliwa</option>
                                <option value="invalid">Taarifa si sahihi</option>
                                <option value="other">Nyingine</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ghairi</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-lg"></i> Ndiyo, Kataa Biashara
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center py-5">
            <i class="bi bi-check-circle fs-1 text-muted"></i>
            <h5 class="mt-3">Hakuna Maombi ya Biashara</h5>
            <p class="text-muted">Biashara zote zimeshughulikiwa kwa sasa.</p>
        </div>
        <?php endif; ?>
    </div>
    
    
    <?php if($pending->hasPages()): ?>
    <div class="card-footer bg-white py-3">
        <?php echo e($pending->links()); ?>

    </div>
    <?php endif; ?>
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
    .pagination {
        margin-bottom: 0;
    }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\admin\users\verify.blade.php ENDPATH**/ ?>