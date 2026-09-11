<?php $__env->startSection('title', 'Dashboard ya Mteja'); ?>
<?php $__env->startSection('page-title', 'Habari, ' . Auth::user()->full_name . '! 👋'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    
    <div class="col-12 mb-4">
        <div class="card welcome-card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="fw-bold display-6 mb-3">Unahitaji Gesi Sasa?</h4>
                        <p class="lead text-muted mb-4">Agiza sasa na upokee ndani ya muda mfupi kutoka kwa wauzaji wa karibu yako.</p>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="<?php echo e(route('consumer.order.create', ['type' => 'refill'])); ?>" class="btn btn-success btn-lg rounded-pill px-4">
                                <i class="bi bi-arrow-repeat me-2"></i> Kubadilisha (Refill)
                            </a>
                            <a href="<?php echo e(route('consumer.order.create', ['type' => 'new'])); ?>" class="btn btn-warning btn-lg rounded-pill px-4">
                                <i class="bi bi-cart4 me-2"></i> Mtungi Mpya
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-4 mt-md-0">
                        <div class="welcome-illustration">
                            <i class="bi bi-fire text-gradient"></i>
                            <i class="bi bi-truck text-gradient"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="welcome-shape"></div>
        </div>
    </div>

    
    <div class="col-lg-8 mb-4">
        <div class="card stat-card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-2">
                <h5 class="fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>Agizo Linaloendelea</h5>
            </div>
            <div class="card-body">
                <?php if(isset($activeOrder) && $activeOrder): ?>
                    <div class="active-order-details">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="fw-bold mb-1"><?php echo e($activeOrder->order_number); ?></h6>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-calendar me-1"></i> <?php echo e($activeOrder->created_at->format('d M Y, H:i')); ?>

                                </p>
                            </div>
                            <span class="badge 
                                <?php if($activeOrder->status == 'out_for_delivery'): ?> bg-warning bg-opacity-10 text-warning
                                <?php elseif($activeOrder->status == 'accepted'): ?> bg-info bg-opacity-10 text-info
                                <?php elseif($activeOrder->status == 'picked_up'): ?> bg-primary bg-opacity-10 text-primary
                                <?php elseif($activeOrder->status == 'pending'): ?> bg-secondary bg-opacity-10 text-secondary
                                <?php else: ?> bg-info bg-opacity-10 text-info <?php endif; ?> 
                                px-3 py-2 rounded-pill">
                                <?php if($activeOrder->status == 'out_for_delivery'): ?>
                                    Njiani
                                <?php elseif($activeOrder->status == 'accepted'): ?>
                                    Imekubaliwa
                                <?php elseif($activeOrder->status == 'picked_up'): ?>
                                    Imeshachukuliwa
                                <?php elseif($activeOrder->status == 'pending'): ?>
                                    Inasubiri
                                <?php else: ?>
                                    <?php echo e(ucfirst(str_replace('_', ' ', $activeOrder->status))); ?>

                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><i class="bi bi-shop me-2"></i> 
                                    <?php echo e($activeOrder->retailer->business_name ?? 'Inatafuta muuzaji...'); ?>

                                </p>
                                <p class="mb-1"><i class="bi bi-geo-alt me-2"></i> 
                                    <?php echo e(\Illuminate\Support\Str::limit($activeOrder->delivery_address, 40)); ?>

                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><i class="bi bi-box me-2"></i> 
                                    <?php if($activeOrder->items && count($activeOrder->items) > 0): ?>
                                        <?php $__currentLoopData = $activeOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo e($item->quantity); ?>x <?php echo e($item->product->name ?? 'Bidhaa'); ?>

                                            <?php if(!$loop->last): ?>, <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        Bidhaa
                                    <?php endif; ?>
                                </p>
                                <p class="mb-1"><i class="bi bi-cash me-2"></i> 
                                    Jumla: TZS <?php echo e(number_format($activeOrder->total_amount)); ?>

                                </p>
                            </div>
                        </div>

                        <?php if($activeOrder->payment_status === 'paid'): ?>
                            <div class="alert alert-success py-2 small mb-3"><i class="bi bi-check-circle-fill me-1"></i>Malipo yamekamilika.</div>
                        <?php elseif($activePaymentStatus === 'failed' && $activePaymentProvider === 'manual'): ?>
                            <div class="alert alert-danger py-2 small mb-3"><i class="bi bi-x-circle-fill me-1"></i>Reference ID imekataliwa. Tafadhali tuma namba sahihi tena.</div>
                        <?php elseif($activePaymentStatus === 'failed'): ?>
                            <div class="alert alert-danger py-2 small mb-3"><i class="bi bi-x-circle-fill me-1"></i>Malipo ya ClickPesa hayajakamilika. Jaribu tena.</div>
                        <?php elseif($activeOrder->transaction_reference): ?>
                            <div class="alert alert-info py-2 small mb-3"><i class="bi bi-send-check-fill me-1"></i><strong>Namba za muamala zimetumwa.</strong> Zinasubiri uthibitisho wa muuzaji.</div>
                        <?php endif; ?>
                        
                        <a href="<?php echo e(route('consumer.order.tracking', ['id' => $activeOrder->id])); ?>" 
                           class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-geo-alt me-2"></i> Fuatilia Agizo
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="display-1 text-muted mb-3 opacity-50">
                            <i class="bi bi-inbox"></i>
                        </div>
                        <h5 class="fw-semibold">Hakuna Agizo Linaloendelea</h5>
                        <p class="text-muted">Bado hujaweka agizo lolote. Bonyeza hapa chini kuagiza.</p>
                        <a href="<?php echo e(route('consumer.order.create')); ?>" class="btn btn-primary rounded-pill px-4 mt-2">
                            <i class="bi bi-plus-circle me-2"></i> Agiza Sasa
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="col-lg-4 mb-4">
        <div class="card stat-card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-2">
                <h5 class="fw-bold"><i class="bi bi-graph-up text-success me-2"></i>Muhtasari</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Jumla ya Maagizo:</span>
                    <span class="fw-bold fs-5"><?php echo e($totalOrders ?? 0); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Maagizo Mwaka Huu:</span>
                    <span class="fw-bold fs-5"><?php echo e($ordersThisYear ?? 0); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Jumla ya Matumizi:</span>
                    <span class="fw-bold">TZS <?php echo e(number_format($totalSpent ?? 0)); ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Agizo la Mwisho:</span>
                    <span class="fw-bold"><?php echo e($lastOrderNumber ?? 'Hakuna'); ?></span>
                </div>
                <hr class="my-4">
                <a href="<?php echo e(route('consumer.history')); ?>" class="btn btn-outline-primary w-100 rounded-pill">
                    <i class="bi bi-clock-history me-2"></i> Tazama Historia Yote
                </a>
            </div>
        </div>
    </div>

    
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-list-ul text-info me-2"></i>Maagizo ya Hivi Karibuni</h5>
                <a href="<?php echo e(route('consumer.history')); ?>" class="text-decoration-none fw-semibold">
                    Ona Yote <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Namba ya Agizo</th>
                                <th>Tarehe</th>
                                <th>Huduma</th>
                                <th>Jumla (TZS)</th>
                                <th>Hali</th>
                                <th class="text-end pe-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentOrders ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="fw-medium ps-4"><?php echo e($order['number']); ?></td>
                                <td><?php echo e($order['date']); ?></td>
                                <td><?php echo e($order['service']); ?></td>
                                <td>TZS <?php echo e(number_format($order['total'])); ?></td>
                                <td>
                                    <?php if($order['status'] == 'delivered'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Imekamilika</span>
                                    <?php elseif($order['status'] == 'cancelled'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text399-danger px-3 py-2 rounded-pill">Imefutwa</span>
                                    <?php elseif($order['status'] == 'out_for_delivery'): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Njiani</span>
                                    <?php elseif($order['status'] == 'accepted'): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">Imekubaliwa</span>
                                    <?php elseif($order['status'] == 'pending'): ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill">Inasubiri</span>
                                    <?php elseif($order['status'] == 'picked_up'): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Imeshachukuliwa</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Inaendelea</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?php echo e(route('consumer.order.tracking', ['id' => $order['id']])); ?>" 
                                       class="btn btn-sm btn-outline-secondary rounded-circle" 
                                       style="width: 32px; height: 32px; padding: 0;">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-4 opacity-50 mb-3 d-block"></i>
                                    Bado huna maagizo yoyote. 
                                    <a href="<?php echo e(route('consumer.order.create')); ?>" class="text-primary fw-semibold">Agiza sasa</a>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    body{
    background: linear-gradient(145deg, #a3a3e4 0%, #b3b896 100%);
    }
    /* Kadi ya Karibu (Welcome Card) */
    .welcome-card {
        background: linear-gradient(145deg, #a3a3e4 0%, #b3b896 100%);
        position: relative;
        border-left: 5px solid #FF6B35 !important;
    }
    .welcome-shape {
        position: absolute;
        top: -30px;
        right: -30px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255,107,53,0.05) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .welcome-illustration {
        font-size: 4rem;
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        opacity: 0.8;
    }
    .text-gradient {
        background: linear-gradient(145deg, #FF6B35, #E85D2C);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Active Order Details */
    .active-order-details {
        padding: 0.5rem 0;
    }

    /* Kadi za Takwimu (Stat Cards) */
    .stat-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.02);
        background: white;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(255, 107, 53, 0.08) !important;
        border-color: rgba(255, 107, 53, 0.2);
    }

    /* Jedwali */
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #d1d1d3;
        padding-top: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .table tbody tr {
        transition: background-color 0.2s;
    }
    .table tbody tr:hover {
        background-color: #fffaf7;
    }
    .table td {
        padding: 1rem 0.5rem;
        color: #334155;
    }

    /* Vitufe */
    .btn-success {
        background: linear-gradient(145deg, #2A9D8F, #21867A);
        border: none;
        color: white;
    }
    .btn-success:hover {
        background: linear-gradient(145deg, #21867A, #1a6b61);
        color: white;
    }
    .btn-warning {
        background: linear-gradient(145deg, #F9C22E, #F5B700);
        border: none;
        color: #1A1A2E;
    }
    .btn-warning:hover {
        background: linear-gradient(145deg, #F5B700, #e0a800);
    }
    .btn-primary {
        background: linear-gradient(145deg, #FF6B35, #E85D2C);
        border: none;
    }
    .btn-primary:hover {
        background: linear-gradient(145deg, #E85D2C, #d44f20);
    }
    .btn-outline-primary {
        border: 2px solid #FF6B35;
        color: #FF6B35;
        background: transparent;
    }
    .btn-outline-primary:hover {
        background: #FF6B35;
        color: white;
    }

    /* Badges za Hali */
    .badge.bg-success {
        background-color: #e6f7e6 !important;
        color: #0f5132 !important;
        font-weight: 500;
    }
    .badge.bg-danger {
        background-color: #ffe6e6 !important;
        color: #b02a37 !important;
        font-weight: 500;
    }
    .badge.bg-warning {
        background-color: #fff4e5 !important;
        color: #b85e00 !important;
        font-weight: 500;
    }
    .badge.bg-info {
        background-color: #e6f3ff !important;
        color: #0056b3 !important;
        font-weight: 500;
    }
    .badge.bg-secondary {
        background-color: #e9ecef !important;
        color: #495057 !important;
        font-weight: 500;
    }
    .badge.bg-primary {
        background-color: #e6f0ff !important;
        color: #0d6efd !important;
        font-weight: 500;
    }

    /* Mwitikio wa Simu */
    @media (max-width: 768px) {
        .welcome-illustration {
            justify-content: center;
        }
        .table td, .table th {
            font-size: 0.85rem;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views/consumer/dashboard.blade.php ENDPATH**/ ?>