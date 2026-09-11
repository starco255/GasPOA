<?php $__env->startSection('title', 'Maagizo ya Jumla'); ?>
<?php $__env->startSection('page-title', 'Dhibiti Maagizo ya Jumla'); ?>

<?php $__env->startSection('content'); ?>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold"><i class="bi bi-inbox me-2 text-primary"></i>Maagizo ya Jumla</h5>
            <a href="<?php echo e(route('wholesaler.orders.history')); ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-clock-history"></i> Historia
            </a>
        </div>
        
        <ul class="nav nav-tabs mt-3" id="orderTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                    <i class="bi bi-bell-fill text-warning me-1"></i> 
                    Maagizo Mapya 
                    <span class="badge bg-warning text-dark ms-1"><?php echo e($pendingCount ?? 0); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                    <i class="bi bi-gear-fill text-primary me-1"></i> 
                    Yanayoshughulikiwa 
                    <span class="badge bg-primary ms-1"><?php echo e($processingCount ?? 0); ?></span>
                </button>
            </li>
        </ul>
    </div>
    
    <div class="card-body p-0">
        <div class="tab-content" id="orderTabsContent">
            
            
            
            
            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                <?php if(isset($pendingOrders) && count($pendingOrders) > 0): ?>
                    <?php $__currentLoopData = $pendingOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $paymentMethodLabel = match($order['payment_method'] ?? null) {
                            'cash' => 'Pesa Taslimu', 'mpesa' => 'M-Pesa', 'tigopesa' => 'TigoPesa / Mixx by Yas',
                            'airtelmoney' => 'Airtel Money', 'halopesa' => 'HaloPesa', 'bank' => 'Benki', default => 'Haijachaguliwa',
                        };
                    ?>
                    <div class="order-card border-bottom p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap">
                            <div class="d-flex align-items-center">
                                <span class="fw-bold fs-5 me-3"><?php echo e($order['number']); ?></span>
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                    <i class="bi bi-clock me-1"></i><?php echo e($order['created']); ?>

                                </span>
                                <span class="badge bg-secondary rounded-pill px-3 py-2 ms-2">
                                    <i class="bi bi-box me-1"></i><?php echo e($order['total_items']); ?> bidhaa
                                </span>
                                <?php if(($order['payment_status'] ?? 'pending') === 'paid'): ?>
                                    <span class="badge bg-success rounded-pill px-3 py-2 ms-2"><i class="bi bi-check-circle-fill me-1"></i>Malipo yamekamilika</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2 ms-2"><i class="bi bi-clock-history me-1"></i>Malipo yanasubiri</span>
                                <?php endif; ?>
                            </div>
                            <div class="mt-2 mt-sm-0">
                                <span class="badge bg-info rounded-pill px-3 py-2">
                                    <i class="bi bi-shop me-1"></i> <?php echo e($order['retailer']); ?>

                                </span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-8">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="order-icon bg-light rounded-circle p-2 me-3">
                                        <i class="bi bi-telephone text-primary"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-medium"><?php echo e($order['retailer_phone']); ?></p>
                                        <p class="text-muted small mb-0">
                                            <i class="bi bi-geo-alt me-1"></i><?php echo e($order['address']); ?>

                                        </p>
                                    </div>
                                </div>
                                <div class="alert <?php echo e(($order['payment_status'] ?? 'pending') === 'paid' ? 'alert-success' : 'alert-warning'); ?> py-2 small">
                                    <strong>Njia ya malipo:</strong> <?php echo e($paymentMethodLabel); ?>.
                                    <?php if(($order['payment_status'] ?? 'pending') === 'paid'): ?>
                                        Malipo yamethibitishwa<?php echo e(!empty($order['transaction_reference']) ? ' (Rejea: ' . $order['transaction_reference'] . ')' : ''); ?>.
                                    <?php else: ?>
                                        Subiri Transaction Reference ID Itumwe
                                    <?php endif; ?>
                                </div>

                                <div class="bg-light rounded-3 px-3 py-2">
                                    <?php $__currentLoopData = $order['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="d-flex justify-content-between gap-2 small <?php echo e(!$loop->last ? 'border-bottom pb-2 mb-2' : ''); ?>">
                                            <span class="fw-medium"><?php echo e($item['name']); ?> <span class="text-muted">× <?php echo e($item['qty']); ?></span></span>
                                            <span class="text-nowrap">TZS <?php echo e(number_format($item['qty'] * $item['price'])); ?></span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <div class="d-flex justify-content-between border-top pt-2 mt-2 fw-bold">
                                        <span>Jumla ya Agizo</span>
                                        <span class="text-primary">TZS <?php echo e(number_format($order['total'])); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 mt-3 mt-lg-0">
                                <div class="action-buttons">
                                    <?php if(($order['payment_method'] ?? '') !== 'cash'): ?>
                                        <?php if(!empty($order['transaction_reference'])): ?>
                                            <div class="alert alert-info py-2 mb-2 text-start transaction-reference-display">
                                                <strong>Transaction ID:</strong>
                                                <span class="transaction-reference-value"><?php echo e($order['transaction_reference']); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning py-2 mb-2 small text-start"><i class="bi bi-exclamation-triangle me-1"></i>Subiri Retailer Atume Transaction ID</div>
                                        <?php endif; ?>
                                        <?php if(($order['payment_status'] ?? 'pending') === 'pending'): ?>
                                            <div class="d-flex flex-wrap gap-2 mb-2">
                                                <?php if(!empty($order['transaction_reference'])): ?>
                                                    <form method="POST" action="<?php echo e(route('wholesaler.orders.payment.reject', $order['id'])); ?>" onsubmit="return confirm('Una uhakika Transaction ID hii si sahihi? Retailer ataweza kutuma nyingine.');"><?php echo csrf_field(); ?>
                                                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle me-1"></i>Kataa ID</button>
                                                    </form>
                                                <?php endif; ?>
                                                <form method="POST" action="<?php echo e(route('wholesaler.orders.payment.confirm', $order['id'])); ?>"><?php echo csrf_field(); ?>
                                                    <button class="btn btn-success btn-sm" <?php echo e(empty($order['transaction_reference']) ? 'disabled' : ''); ?>><i class="bi bi-check-circle me-1"></i>Thibitisha ID</button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                       <a href="tel:<?php echo e($order['retailer_phone']); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                        <i class="bi bi-telephone"></i> Piga Simu
                                       </a>
                                        <form method="POST" action="<?php echo e(route('wholesaler.orders.accept', $order['id'])); ?>" class="flex-grow-1">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-success w-100 rounded-pill" <?php echo e((($order['payment_method'] ?? '') !== 'cash' && (empty($order['transaction_reference']) || ($order['payment_status'] ?? 'pending') !== 'paid')) ? 'disabled' : ''); ?>>
                                                <i class="bi bi-check-lg me-1"></i> Kubali Agizo
                                            </button>
                                        </form>
                                    </div>
                                    <?php if(($order['payment_method'] ?? '') !== 'cash' && (empty($order['transaction_reference']) || ($order['payment_status'] ?? 'pending') !== 'paid')): ?>
                                        <small class="text-warning d-block mt-2"><i class="bi bi-lock-fill"></i> Thibitisha kwanza Transaction ID </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
                        <h5 class="mt-3 fw-bold">Hakuna Maagizo Mapya</h5>
                        <p class="text-muted">Maagizo yote mapya yameshughulikiwa kwa sasa.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            
            
            
            <div class="tab-pane fade" id="active" role="tabpanel">
                <div class="p-3 border-bottom bg-light">
                    <p class="text-muted mb-0 small">
                        <i class="bi bi-info-circle me-1"></i> 
                        Maagizo yaliyokubaliwa na yanayoshughulikiwa kwa sasa.
                    </p>
                </div>
                
                <?php if(isset($processingOrders) && count($processingOrders) > 0): ?>
                    <?php $__currentLoopData = $processingOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $paymentMethodLabel = match($order['payment_method'] ?? null) {
                            'cash' => 'Pesa Taslimu', 'mpesa' => 'M-Pesa', 'tigopesa' => 'TigoPesa / Mixx by Yas',
                            'airtelmoney' => 'Airtel Money', 'halopesa' => 'HaloPesa', 'bank' => 'Benki', default => 'Haijachaguliwa',
                        };
                    ?>
                    <div class="order-card border-bottom p-4">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <div class="d-flex align-items-center mb-2 flex-wrap">
                                    <span class="fw-bold fs-5 me-3"><?php echo e($order['number']); ?></span>
                                    <?php if($order['status'] == 'confirmed'): ?>
                                        <span class="badge bg-info rounded-pill px-3 py-2">Imethibitishwa</span>
                                    <?php elseif($order['status'] == 'processing'): ?>
                                        <span class="badge bg-primary rounded-pill px-3 py-2">Inashughulikiwa</span>
                                    <?php elseif($order['status'] == 'dispatched'): ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Imesafirishwa</span>
                                    <?php endif; ?>
                                    <span class="badge bg-secondary rounded-pill px-3 py-2 ms-2">
                                        <i class="bi bi-box me-1"></i><?php echo e($order['total_items']); ?> bidhaa
                                    </span>
                                    <?php if(($order['payment_status'] ?? 'pending') === 'paid'): ?>
                                        <span class="badge bg-success rounded-pill px-3 py-2 ms-2">Imelipwa</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2 ms-2">Malipo yanasubiri</span>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-1">
                                    <i class="bi bi-shop me-2"></i><?php echo e($order['retailer']); ?> 
                                    (<?php echo e($order['retailer_phone']); ?>)
                                </p>
                                <p class="mb-1">
                                    <i class="bi bi-geo-alt me-2"></i><?php echo e($order['address']); ?>

                                </p>
                                <p class="mb-1">
                                    <i class="bi bi-box me-2"></i><?php echo e(Str::limit($order['items'], 50)); ?>

                                </p>
                                <p class="fw-bold text-primary">
                                    Jumla: TZS <?php echo e(number_format($order['total'])); ?> 
                                    <span class="text-muted ms-2">(Bidhaa: <?php echo e($order['total_items']); ?>)</span>
                                </p>
                                <p class="mb-1 small <?php echo e(($order['payment_status'] ?? 'pending') === 'paid' ? 'text-success' : 'text-warning'); ?>">
                                    <i class="bi bi-credit-card me-2"></i><?php echo e($paymentMethodLabel); ?> — <?php echo e(($order['payment_status'] ?? 'pending') === 'paid' ? 'Malipo yamekamilika' : 'Malipo yanasubiri uthibitisho'); ?>

                                </p>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i> <?php echo e($order['created_at']); ?>

                                </small>
                            </div>
                            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                <?php if(in_array($order['status'], ['confirmed', 'processing'])): ?>
                                    <button class="btn btn-outline-primary rounded-pill px-4" 
                                            onclick="openCustomModal('dispatchModal<?php echo e($order['id']); ?>')">
                                        <i class="bi bi-truck me-2"></i> Thibitisha Usafirishaji
                                    </button>
                                <?php elseif($order['status'] == 'dispatched'): ?>
                                    <form method="POST" action="<?php echo e(route('wholesaler.orders.deliver', $order['id'])); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-success rounded-pill px-4">
                                            <i class="bi bi-check-circle me-2"></i> Kamilisha
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="custom-modal-overlay" id="dispatchModal<?php echo e($order['id']); ?>">
                        <div class="custom-modal">
                            <form method="POST" action="<?php echo e(route('wholesaler.orders.dispatch', $order['id'])); ?>">
                                <?php echo csrf_field(); ?>
                                <div class="custom-modal-header">
                                    <h5 class="custom-modal-title">Maelezo ya Usafirishaji</h5>
                                    <button type="button" class="custom-modal-close" 
                                            onclick="closeCustomModal('dispatchModal<?php echo e($order['id']); ?>')">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                                <div class="custom-modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Jina la Dereva</label>
                                        <input type="text" class="form-control form-control-lg" name="driver_name" 
                                               placeholder="Mf: Juma" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Namba ya Gari</label>
                                        <input type="text" class="form-control form-control-lg" name="vehicle_number" 
                                               placeholder="Mf: T123 ABC" required>
                                    </div>
                                    <p class="text-muted small mb-0">
                                        Agizo #<?php echo e($order['number']); ?> - <?php echo e($order['retailer']); ?> (Bidhaa: <?php echo e($order['total_items']); ?>)
                                    </p>
                                </div>
                                <div class="custom-modal-footer">
                                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" 
                                            onclick="closeCustomModal('dispatchModal<?php echo e($order['id']); ?>')">Ghairi</button>
                                    <button type="submit" class="btn btn-success rounded-pill px-4">
                                        <i class="bi bi-check-lg me-2"></i> Thibitisha
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="empty-state-icon"><i class="bi bi-gear"></i></div>
                        <h5 class="mt-3 fw-bold">Hakuna Maagizo Yanayoshughulikiwa</h5>
                        <p class="text-muted">Maagizo yote yameshughulikiwa au hayapo kwa sasa.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>




<?php if(isset($recentDeliveredOrders) && count($recentDeliveredOrders) > 0): ?>
<div class="card border-0 shadow-sm rounded-4 mt-4">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <h5 class="fw-bold"><i class="bi bi-check-circle me-2 text-success"></i>Imekamilika Hivi Karibuni</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Agizo #</th>
                        <th>Muuzaji</th>
                        <th>Bidhaa</th>
                        <th>Jumla (TZS)</th>
                        <th>Tarehe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $recentDeliveredOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($order['number']); ?></td>
                        <td><?php echo e($order['retailer']); ?></td>
                        <td><?php echo e($order['items']); ?></td>
                        <td><?php echo e(number_format($order['total'])); ?></td>
                        <td><?php echo e($order['delivered_at']); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .bg-opacity-10 { --bs-bg-opacity: 0.1; }
    .order-card { transition: background-color 0.2s ease; background: linear-gradient(135deg, #7a948f 0%, #c5c3c3 100%); }
    .order-icon { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; }
    .action-buttons .btn { transition: all 0.2s ease; }
    .action-buttons .btn:hover { transform: translateY(-2px); }
    .empty-state-icon { width: 100px; height: 100px; background: linear-gradient(145deg, #d7d9db, #e9ecef); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-size: 3rem; color: #adb5bd; }
    .badge.bg-warning { background-color: #fff3cd !important; color: #856404 !important; }
    .badge.bg-info { background-color: #e6f3ff !important; color: #0056b3 !important; }
    .badge.bg-success { background-color: #d1e7dd !important; color: #0f5132 !important; }
    .transaction-reference-display { font-size: 1rem; }
    .transaction-reference-value { font-family: var(--bs-font-monospace); font-size: 1rem; font-weight: 600; letter-spacing: 0.02em; overflow-wrap: anywhere; }
    .nav-tabs .nav-link { color: #6c757d; font-weight: 500; border: none; padding: 0.75rem 1.5rem; }
    .nav-tabs .nav-link:hover { color: #0d6efd; border: none; }
    .nav-tabs .nav-link.active { color: #0d6efd; background-color: transparent; border-bottom: 3px solid #0d6efd; }
    .table tbody tr { background-color: #9e9e9e; }
    .table tbody tr:hover { background-color: transparent; }

/* ===== Custom Modal System (Imeboreshwa – inasogezwa na haikati) ===== */
.custom-modal-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    overflow-y: auto;               /* inaruhusu overlay yenyewe kusogea ikiwa modal ni ndefu kuliko viewport */
}

.custom-modal {
    position: relative;             /* sio fixed tena, ili iweze kusogea pamoja na overlay */
    width: 90%;
    max-width: 500px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.25);
    margin: 50px auto;              /* nafasi juu na chini, inajiweka katikati ya overlay */
    display: flex;
    flex-direction: column;
    max-height: 80vh;               /* hairuhusiwi kuzidi 80% ya urefu wa skrini */
}

.custom-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #eee;
}

.custom-modal-title {
    font-weight: 700;
    font-size: 1.25rem;
    margin: 0;
}

.custom-modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #666;
}

.custom-modal-body {
    padding: 1.25rem 1.5rem;
    overflow-y: auto;               /* sehemu ya katikati inasogezwa ikiwa yaliyomo ni mengi */
    flex: 1;                        /* inachukua nafasi iliyobaki */
}

.custom-modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    padding: 1.25rem 1.5rem;
    border-top: 1px solid #eee;
    background-color: white;        /* ili isifichwe nyuma ya maandishi */
}

.custom-modal, .custom-modal * {
    transition: none !important;
    animation: none !important;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Custom Modal Functions
    function openCustomModal(id) {
        document.getElementById(id).style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    function closeCustomModal(id) {
        document.getElementById(id).style.display = 'none';
        document.body.style.overflow = '';
    }
    // Funga unapobofya nje ya modal
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('custom-modal-overlay')) {
            e.target.style.display = 'none';
            document.body.style.overflow = '';
        }
    });

    // Tabs handling (inaleta tab iliyofunguliwa awali)
    document.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash;
        if (hash) {
            const tab = document.querySelector(`button[data-bs-target="${hash}"]`);
            if (tab) new bootstrap.Tab(tab).show();
        }
        document.querySelectorAll('#orderTabs button').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(e) {
                localStorage.setItem('activeWholesalerOrderTab', e.target.getAttribute('data-bs-target'));
            });
        });
        const activeTab = localStorage.getItem('activeWholesalerOrderTab');
        if (activeTab) {
            const tab = document.querySelector(`button[data-bs-target="${activeTab}"]`);
            if (tab) new bootstrap.Tab(tab).show();
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\wholesaler\orders\incoming.blade.php ENDPATH**/ ?>