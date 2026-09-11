<?php $__env->startSection('title', 'Dashboard ya Admin'); ?>
<?php $__env->startSection('page-title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-people-fill fs-3 text-primary"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">Jumla ya Watumiaji</h6>
                        <h3 class="mb-0"><?php echo e(number_format($totalUsers)); ?></h3>
                        <?php if($userGrowth > 0): ?>
                            <small class="text-success"><i class="bi bi-arrow-up"></i> +<?php echo e($userGrowth); ?>% mwezi huu</small>
                        <?php elseif($userGrowth < 0): ?>
                            <small class="text-danger"><i class="bi bi-arrow-down"></i> <?php echo e(abs($userGrowth)); ?>% mwezi huu</small>
                        <?php else: ?>
                            <small class="text-muted">Hakuna mabadiliko</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-cash-stack fs-3 text-success"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">Mauzo Yaliyolipwa</h6>
                        <h3 class="mb-0">TZS <?php echo e(number_format($totalPaidSales)); ?></h3>
                        <?php if($revenueGrowth > 0): ?>
                            <small class="text-success"><i class="bi bi-arrow-up"></i> +<?php echo e($revenueGrowth); ?>% wiki hii</small>
                        <?php elseif($revenueGrowth < 0): ?>
                            <small class="text-danger"><i class="bi bi-arrow-down"></i> <?php echo e(abs($revenueGrowth)); ?>% wiki hii</small>
                        <?php else: ?>
                        
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-hourglass-split fs-3 text-warning"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">Zinazosubiri Uidhinishaji</h6>
                        <h3 class="mb-0"><?php echo e($pendingVerifications); ?></h3>
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-truck fs-3 text-info"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="text-muted mb-1">Maagizo Leo</h6>
                        <h3 class="mb-0"><?php echo e($ordersToday); ?></h3>
                        <small class="text-muted"><?php echo e($ordersActive); ?> yanashughulikiwa</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-light">
            <div class="card-body text-center py-3">
                <i class="bi bi-person fs-2 text-primary"></i>
                <h5 class="mt-2 mb-0"><?php echo e(number_format($consumersCount)); ?></h5>
                <small class="text-muted">Wateja</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-light">
            <div class="card-body text-center py-3">
                <i class="bi bi-shop fs-2 text-success"></i>
                <h5 class="mt-2 mb-0"><?php echo e(number_format($retailersCount)); ?></h5>
                <small class="text-muted">Wauzaji Rejareja</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-light">
            <div class="card-body text-center py-3">
                <i class="bi bi-building fs-2 text-warning"></i>
                <h5 class="mt-2 mb-0"><?php echo e(number_format($wholesalersCount)); ?></h5>
                <small class="text-muted">Wauzaji Jumla</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-light">
            <div class="card-body text-center py-3">
                <i class="bi bi-wallet2 fs-2 text-info"></i>
                <h5 class="mt-2 mb-0">TZS <?php echo e(number_format($totalPlatformRevenue)); ?></h5>
            
                <div><small class="text-muted"><?php echo e($pendingPayouts); ?> payout pending</small></div>
            </div>
        </div>
    </div>
</div>


<div class="row g-4">
    
    
    
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-person-plus me-2"></i>Watumiaji Wapya</h5>
            </div>
            <div class="card-body flex-grow-1">
                <?php $__empty_1 = true; $__currentLoopData = $newUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-light rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <span class="fw-bold text-primary"><?php echo e($user['avatar']); ?></span>
                    </div>
                    <div class="flex-grow-1">
                        <strong><?php echo e($user['name']); ?></strong><br>
                        <small class="text-muted"><?php echo e($user['type_label']); ?> • <?php echo e($user['time']); ?></small>
                    </div>
                    <span class="badge bg-secondary"><?php echo e($user['type_label']); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people fs-1"></i>
                    <p class="mt-2">Hakuna watumiaji wapya.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="card-footer bg-white border-0 pt-0 pb-3">
                <a href="<?php echo e(route('admin.users.index')); ?>" class="btn btn-outline-primary w-100">
                    <i class="bi bi-people me-1"></i> Watumiaji Wote
                </a>
            </div>
        </div>
    </div>
    
    
    
    
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100 d-flex flex-column">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-bar-chart me-2"></i>Muhtasari wa Mfumo</h5>
            </div>
            <div class="card-body flex-grow-1">
                <div class="row g-3">
                    
                    <div class="col-6 col-md-4">
                        <div class="stats-card bg-primary bg-opacity-10 rounded-4 p-3 text-center h-100">
                            <div class="stats-icon bg-primary text-white rounded-circle mx-auto mb-2">
                                <i class="bi bi-buildings"></i>
                            </div>
                            <h3 class="fw-bold text-primary mb-1"><?php echo e(number_format($totalBusinesses ?? 0)); ?></h3>
                            <p class="text-muted small mb-0">Biashara Zilizosajiliwa</p>
                            <div class="d-flex justify-content-center gap-2 mt-1">
                                <small class="text-muted">Rejareja: <?php echo e(number_format($totalRetailers ?? 0)); ?></small>
                                <small class="text-muted">|</small>
                                <small class="text-muted">Jumla: <?php echo e(number_format($totalWholesalers ?? 0)); ?></small>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-6 col-md-4">
                        <div class="stats-card bg-success bg-opacity-10 rounded-4 p-3 text-center h-100">
                            <div class="stats-icon bg-success text-white rounded-circle mx-auto mb-2">
                                <i class="bi bi-cart-check"></i>
                            </div>
                            <h3 class="fw-bold text-success mb-1"><?php echo e(number_format($totalOrders)); ?></h3>
                            <p class="text-muted small mb-0">Jumla ya Maagizo</p>
                        </div>
                    </div>
                    
                    
                    <div class="col-6 col-md-4">
                        <div class="stats-card bg-info bg-opacity-10 rounded-4 p-3 text-center h-100">
                            <div class="stats-icon bg-info text-white rounded-circle mx-auto mb-2">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <h3 class="fw-bold text-info mb-1"><?php echo e(number_format($deliveredOrders)); ?></h3>
                            <p class="text-muted small mb-0">Imekamilika</p>
                        </div>
                    </div>
                    
                    
                    <div class="col-6 col-md-4">
                        <div class="stats-card bg-danger bg-opacity-10 rounded-4 p-3 text-center h-100">
                            <div class="stats-icon bg-danger text-white rounded-circle mx-auto mb-2">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <h3 class="fw-bold text-danger mb-1"><?php echo e(number_format($cancelledOrders)); ?></h3>
                            <p class="text-muted small mb-0">Imefutwa</p>
                        </div>
                    </div>
                    
                    
                    <div class="col-6 col-md-4">
                        <div class="stats-card bg-warning bg-opacity-10 rounded-4 p-3 text-center h-100">
                            <div class="stats-icon bg-warning text-white rounded-circle mx-auto mb-2">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <h3 class="fw-bold text-warning mb-1"><?php echo e(number_format($pendingVerifications)); ?></h3>
                            <p class="text-muted small mb-0">Zinasubiri Uidhinishaji</p>
                        </div>
                    </div>
                    
                    
                    <div class="col-6 col-md-4">
                        <div class="stats-card bg-secondary bg-opacity-10 rounded-4 p-3 text-center h-100">
                            <div class="stats-icon bg-secondary text-white rounded-circle mx-auto mb-2">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <h3 class="fw-bold text-secondary mb-1"><?php echo e($deliveryRate); ?>%</h3>
                            <p class="text-muted small mb-0">Ufanisi wa Utekelezaji</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<div class="card border-0 shadow-sm rounded-4 mt-4">
    <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold"><i class="bi bi-list-ul me-2"></i>Maagizo ya Hivi Karibuni</h5>
        <a href="<?php echo e(route('admin.reports.finance')); ?>" class="text-decoration-none">Ripoti Kamili <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mteja</th>
                        <th>Muuzaji</th>
                        <th>Huduma</th>
                        <th>Jumla (TZS)</th>
                        <th>Hali</th>
                        <th>Tarehe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="fw-medium"><?php echo e($order['id']); ?></td>
                        <td><?php echo e($order['consumer']); ?></td>
                        <td><?php echo e($order['retailer']); ?></td>
                        <td><?php echo e($order['service']); ?></td>
                        <td>TZS <?php echo e(number_format($order['total'])); ?></td>
                        <td>
                            <?php if($order['status'] == 'delivered'): ?>
                                <span class="badge bg-success">Imekamilika</span>
                            <?php elseif($order['status'] == 'cancelled'): ?>
                                <span class="badge bg-danger">Imefutwa</span>
                            <?php elseif($order['status'] == 'out_for_delivery'): ?>
                                <span class="badge bg-warning text-dark">Njiani</span>
                            <?php elseif($order['status'] == 'accepted'): ?>
                                <span class="badge bg-info">Imekubaliwa</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?php echo e(ucfirst($order['status'])); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span data-bs-toggle="tooltip" title="<?php echo e($order['date_full']); ?>">
                                <?php echo e($order['date']); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                            Hakuna maagizo bado.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
<?php $__env->stopPush(); ?>
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
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        color: #d8dddd;
    }
    
    /* ===================================================== */
    /* MAKE CARDS EQUAL HEIGHT */
    /* ===================================================== */
    .h-100 {
        height: 100% !important;
    }
    .flex-grow-1 {
        flex-grow: 1 !important;
    }
    .d-flex.flex-column {
        display: flex !important;
        flex-direction: column !important;
    }
    
    /* ===================================================== */
    /* STATS CARDS STYLING - MPYA */
    /* ===================================================== */
    .stats-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 2px solid transparent;
    }
    
    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-color: rgba(0,0,0,0.05);
    }
    
    /* Stats Icon Circle */
    .stats-icon {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    
    .stats-card h3 {
        font-size: 1.5rem;
    }
    
    .stats-card p {
        font-size: 0.75rem;
        font-weight: 500;
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>