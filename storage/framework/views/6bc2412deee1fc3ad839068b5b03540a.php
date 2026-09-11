<?php $__env->startSection('title', 'Maagizo'); ?>
<?php $__env->startSection('page-title', 'Dhibiti Maagizo ya Wateja'); ?>

<?php $__env->startSection('content'); ?>
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold"><i class="bi bi-inbox me-2 text-primary"></i>Maagizo</h5>
            <a href="<?php echo e(route('retailer.orders.history')); ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-clock-history"></i> Historia
            </a>
        </div>
        
        
        <ul class="nav nav-tabs mt-3" id="orderTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                    <i class="bi bi-bell-fill text-warning me-1"></i> 
                    Maagizo Mapya 
                    <span class="badge bg-warning text-dark ms-1"><?php echo e($totalPending ?? 0); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab">
                    <i class="bi bi-truck text-primary me-1"></i> 
                    Yanayoendelea 
                    <span class="badge bg-primary ms-1"><?php echo e(isset($activeOrders) ? count($activeOrders) : 0); ?></span>
                </button>
            </li>
        </ul>
    </div>
    
    <div class="card-body p-0">
        <div class="tab-content" id="orderTabsContent">
            
            
            
            
            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                <?php if(isset($orders) && count($orders) > 0): ?>
                    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border-bottom p-4">
                        <div class="row align-items-start">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-2 flex-wrap">
                                    <span class="fw-bold fs-5 me-2"><?php echo e($order['order_number']); ?></span>
                                    <span class="badge bg-info"><?php echo e($order['created_at_human']); ?></span>
                                    <?php if(isset($order['is_urgent']) && $order['is_urgent']): ?>
                                        <span class="badge bg-danger ms-2"><i class="bi bi-lightning"></i> Haraka</span>
                                    <?php endif; ?>
                                    <?php if(isset($order['stock_available']) && !$order['stock_available']): ?>
                                        <span class="badge bg-warning text-dark ms-2"><i class="bi bi-exclamation-triangle"></i> Stock Chache</span>
                                    <?php endif; ?>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <p class="mb-1"><i class="bi bi-person me-2"></i><?php echo e($order['customer']); ?> (<?php echo e($order['customer_phone']); ?>)</p>
                                        <p class="mb-1"><i class="bi bi-geo-alt me-2"></i><?php echo e($order['address']); ?></p>
                                        <p class="mb-1"><i class="bi bi-truck me-2"></i>Umbali: <?php echo e($order['distance']); ?> km</p>
                                        <p class="mb-1">
                                            <i class="bi bi-credit-card me-2"></i>Malipo: 
                                            <?php if($order['payment_method'] == 'cash'): ?>
                                                <span class="badge bg-success">Pesa Taslimu</span>
                                            <?php elseif($order['payment_method'] == 'mobile_money'): ?>
                                                <span class="badge bg-primary">M-Pesa/TigoPesa</span>
                                            <?php else: ?>
                                                <span class="badge bg-info">Kadi</span>
                                            <?php endif; ?>
                                        </p>
                                        <p class="mb-1">
                                            <i class="bi bi-receipt me-2"></i>Hali ya Malipo:
                                            <?php if(($order['payment_status'] ?? 'pending') === 'paid'): ?>
                                                <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Imelipwa</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Inasubiri malipo</span>
                                            <?php endif; ?>
                                        </p>
                                        <p class="mb-1">
                                            <i class="bi bi-calendar me-2"></i>Iliagizwa: <?php echo e($order['created_at_formatted']); ?>

                                        </p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p class="mb-1">
                                            <strong><?php echo e($order['service']); ?></strong> 
                                            <?php if($order['service_type'] == 'new_cylinder'): ?>
                                                <span class="badge bg-warning text-dark">Mtungi Mpya</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Kubadilisha</span>
                                            <?php endif; ?>
                                            x<?php echo e($order['quantity']); ?>

                                        </p>
                                        <p class="mb-1">Bei ya Bidhaa: TZS <?php echo e(number_format($order['product_subtotal'])); ?></p>
                                        <p class="mb-1">
                                            Usafirishaji: 
                                            <?php if($order['delivery_fee'] > 0): ?>
                                                TZS <?php echo e(number_format($order['delivery_fee'])); ?>

                                            <?php else: ?>
                                                <span class="text-success">TZS 0 (Bure)</span>
                                            <?php endif; ?>
                                        </p>
                                        <?php if(isset($order['is_urgent']) && $order['is_urgent']): ?>
                                            <p class="mb-1">
                                                Ada ya Haraka: 
                                                <span class="text-danger">TZS <?php echo e(number_format($order['urgency_fee'])); ?></span>
                                            </p>
                                        <?php endif; ?>
                                        <p class="fw-bold text-primary mt-2">Jumla: TZS <?php echo e(number_format($order['total'])); ?></p>
                                        <?php if(($order['payment_method_provider'] ?? $order['payment_method'] ?? '') !== 'cash'): ?>
                                            <?php if(!empty($order['transaction_reference'])): ?>
                                                <div class="alert alert-info py-2 mt-2 mb-0 transaction-reference-display">
                                                    <strong>Transaction ID:</strong>
                                                    <span class="transaction-reference-value"><?php echo e($order['transaction_reference']); ?></span>
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-warning py-2 mt-2 mb-0 small"><i class="bi bi-exclamation-triangle me-1"></i>Subiri Mteja Atume Transaction ID</div>
                                            <?php endif; ?>
                                            <?php if(($order['payment_status'] ?? 'pending') === 'pending'): ?>
                                                
                                                <div class="payment-review-actions d-flex flex-nowrap align-items-center gap-2 mt-2">
                                                    <?php if(!empty($order['transaction_reference'])): ?>
                                                        <form method="POST" action="<?php echo e(route('retailer.orders.payment.reject', $order['id'])); ?>" class="m-0" onsubmit="return confirm('Una uhakika Transaction ID hii si sahihi? Mteja ataweza kutuma nyingine.');"><?php echo csrf_field(); ?>
                                                            <button class="btn btn-outline-danger btn-sm text-nowrap"><i class="bi bi-x-circle me-1"></i>Kataa ID</button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <form method="POST" action="<?php echo e(route('retailer.orders.payment.confirm', $order['id'])); ?>" class="m-0"><?php echo csrf_field(); ?>
                                                        <button class="btn btn-success btn-sm text-nowrap" <?php echo e(empty($order['transaction_reference']) ? 'disabled' : ''); ?>><i class="bi bi-check-circle me-1"></i>Thibitisha ID</button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="d-grid gap-2 d-md-block">
                                    <form method="POST" action="<?php echo e(route('retailer.orders.accept', $order['id'])); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-success me-2" 
                                                <?php if((isset($order['stock_available']) && !$order['stock_available']) || (($order['payment_method_provider'] ?? $order['payment_method'] ?? '') !== 'cash' && (empty($order['transaction_reference']) || ($order['payment_status'] ?? 'pending') !== 'paid'))): ?> disabled <?php endif; ?>>
                                            <i class="bi bi-check-lg"></i> Kubali Agizo
                                        </button>
                                    </form>
                                </div>
                                <?php if(isset($order['stock_available']) && !$order['stock_available']): ?>
                                    <div class="mt-2">
                                        <small class="text-danger">
                                            <i class="bi bi-exclamation-circle"></i> Huna stock ya kutosha
                                        </small>
                                    </div>
                                <?php endif; ?>
                                <?php if(($order['payment_method_provider'] ?? $order['payment_method'] ?? '') !== 'cash' && (empty($order['transaction_reference']) || ($order['payment_status'] ?? 'pending') !== 'paid')): ?>
                                    <div class="mt-2"><small class="text-warning"><i class="bi bi-lock-fill"></i> Thibitisha kwanza Transaction ID </small></div>
                                <?php endif; ?>
                                <div class="mt-3">
                                    <a href="tel:<?php echo e($order['customer_phone']); ?>" class="btn btn-sm btn-outline-secondary me-2">
                                        <i class="bi bi-telephone"></i> Piga Simu
                                    </a>
                                    <a href="<?php echo e(route('retailer.chat.show', $order['id'])); ?>" class="btn btn-sm btn-outline-primary">
                                      <i class="bi bi-chat"></i> Chat
                                     </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h5 class="mt-3">Hakuna Maagizo Mapya</h5>
                        <p class="text-muted">Maagizo yote mapya yameshughulikiwa.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            
            
            
            <div class="tab-pane fade" id="active" role="tabpanel">
                <div class="p-3 border-bottom bg-light">
                    <p class="text-muted mb-0 small">
                        <i class="bi bi-info-circle me-1"></i> 
                        Maagizo uliyokubali na yanayosubiri kufikishwa kwa wateja.
                    </p>
                </div>
                
                <?php if(isset($activeOrders) && count($activeOrders) > 0): ?>
                    <?php $__currentLoopData = $activeOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border-bottom p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <span class="fw-bold fs-5 me-3"><?php echo e($order['order_number']); ?></span>
                                <?php if($order['status'] == 'accepted'): ?>
                                    <span class="badge bg-info px-3 py-2">Imekubaliwa</span>
                                <?php elseif($order['status'] == 'picked_up'): ?>
                                    <span class="badge bg-primary px-3 py-2">Imeshachukuliwa</span>
                                <?php elseif($order['status'] == 'out_for_delivery'): ?>
                                    <span class="badge bg-warning text-dark px-3 py-2">Njiani</span>
                                <?php endif; ?>
                                <?php if(isset($order['is_urgent']) && $order['is_urgent']): ?>
                                    <span class="badge bg-danger ms-2 px-3 py-2">
                                        <i class="bi bi-lightning"></i> Haraka
                                    </span>
                                <?php endif; ?>
                            </div>
                            <span class="text-muted small">
                                <i class="bi bi-clock"></i> <?php echo e($order['created_at'] ?? 'Hivi karibuni'); ?>

                            </span>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><i class="bi bi-person-circle me-2"></i><strong><?php echo e($order['customer'] ?? 'Mteja'); ?></strong></p>
                                <p class="mb-1"><i class="bi bi-telephone me-2"></i><?php echo e($order['customer_phone'] ?? 'Haipo'); ?></p>
                                <p class="mb-1"><i class="bi bi-geo-alt me-2"></i><?php echo e($order['address'] ?? 'Haipo'); ?></p>
                                <p class="mb-1"><i class="bi bi-signpost me-2"></i>Umbali: <?php echo e($order['distance'] ?? 0); ?> km</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><i class="bi bi-box me-2"></i><strong><?php echo e($order['service'] ?? 'Huduma'); ?></strong></p>
                                <p class="mt-2 mb-0 fw-bold text-primary fs-5">Jumla: TZS <?php echo e(number_format($order['total'] ?? 0)); ?></p>
                            </div>
                        </div>
                        
                        <?php if(isset($order['estimated_delivery'])): ?>
                        <div class="alert alert-success py-2 mb-3 mt-3">
                            <i class="bi bi-clock-fill me-2"></i>
                            <strong>Muda wa Kufika:</strong> 
                            <?php if(is_object($order['estimated_delivery'])): ?>
                                <?php echo e($order['estimated_delivery']->format('H:i')); ?> (<?php echo e($order['estimated_delivery']->diffForHumans()); ?>)
                            <?php else: ?>
                                <?php echo e($order['estimated_delivery']); ?>

                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <?php if($order['status'] == 'accepted'): ?>
                                <form method="POST" action="<?php echo e(route('retailer.orders.pickup', $order['id'])); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-box-arrow-up me-1"></i> Agizo Limetumwa
                                    </button>
                                </form>
                            <?php elseif(in_array($order['status'], ['picked_up', 'out_for_delivery'])): ?>
                                <form method="POST" action="<?php echo e(route('retailer.orders.deliver', $order['id'])); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check2-circle me-1"></i> Agizo Limefika
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <a href="<?php echo e(route('retailer.chat.show', $order['id'])); ?>" class="btn btn-outline-primary">
                                <i class="bi bi-chat-dots me-1"></i> Fungua Chat
                            </a>
                            <a href="tel:<?php echo e($order['customer_phone'] ?? '#'); ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-telephone me-1"></i> Mpigie
                            </a>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle display-1 text-muted"></i>
                        <h5 class="mt-3">Hakuna Maagizo Yanayoendelea</h5>
                        <p class="text-muted">Huna maagizo yanayoshughulikiwa kwa sasa.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .payment-review-actions {
        overflow-x: auto;
        padding-bottom: .15rem;
    }

    .payment-review-actions form {
        flex: 0 0 auto;
    }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('styles'); ?>
<style>
    /* Basic Badge Styles */
    .badge.bg-info { background-color: #e6f3ff !important; color: #0056b3 !important; }
    .badge.bg-primary { background-color: #cfe2ff !important; color: #084298 !important; }
    .badge.bg-warning { background-color: #fff3cd !important; color: #856404 !important; }
    .badge.bg-danger { background-color: #f8d7da !important; color: #842029 !important; }
    .badge.bg-success { background-color: #d1e7dd !important; color: #0f5132 !important; }
    .btn-success:disabled { opacity: 0.5; cursor: not-allowed; }
    
    /* Tabs */
    .nav-tabs .nav-link { color: #6c757d; font-weight: 500; border: none; padding: 0.75rem 1.5rem; }
    .nav-tabs .nav-link:hover { color: #0d6efd; border: none; }
    .nav-tabs .nav-link.active { color: #0d6efd; background-color: transparent; border-bottom: 3px solid #0d6efd; }
    
    /* Alerts */
    .bg-opacity-10 { --bs-bg-opacity: 0.1; }
    .alert-success { background-color: #d1e7dd; border-color: #badbcc; color: #0f5132; }
    .alert-info { background-color: #cff4fc; border-color: #b6effb; color: #055160; }
    .transaction-reference-display { font-size: 1rem; }
    .transaction-reference-value { font-family: var(--bs-font-monospace); font-size: 1rem; font-weight: 600; letter-spacing: 0.02em; overflow-wrap: anywhere; }
    
    /* Button Hovers */
    .btn-outline-primary:hover, .btn-outline-secondary:hover { color: white; }
    
    /* Order Row Hover - ONLY on order rows */
    .order-row:hover { background-color: #fafbfc; }

    /* ==================================================== */
    /* CUSTOM MODAL - PURE CSS, NO JAVASCRIPT ANIMATIONS */
    /* ==================================================== */
    
    /* Overlay - fixed, covers entire screen */
    .custom-modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        /* NO TRANSITIONS */
    }
    
    /* Modal Box - centered, fixed */
    .custom-modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90%;
        max-width: 500px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        z-index: 10000;
        /* NO TRANSITIONS */
    }
    
    /* Modal Header */
    .custom-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem 1.5rem 1rem;
        border-bottom: 1px solid #eee;
    }
    
    .custom-modal-title {
        font-weight: 700;
        font-size: 1.25rem;
        margin: 0;
        color: #1a1a2e;
    }
    
    .custom-modal-close {
        background: none;
        border: none;
        font-size: 1.25rem;
        cursor: pointer;
        opacity: 0.6;
        padding: 0;
        color: #6c757d;
    }
    
    .custom-modal-close:hover {
        opacity: 1;
        background: none;
    }
    
    /* Modal Body */
    .custom-modal-body {
        padding: 1.5rem;
    }
    
    .custom-modal-body p {
        margin-bottom: 1rem;
    }
    
    .custom-modal-body .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #334155;
    }
    
    .custom-modal-body .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #ddd;
        border-radius: 12px;
        background-color: white;
        cursor: pointer;
    }
    
    .custom-modal-body .form-select:focus {
        border-color: #0d6efd;
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    }
    
    /* Modal Footer */
    .custom-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1rem 1.5rem 1.5rem;
        border-top: 1px solid #eee;
    }
    
    .custom-modal-footer .btn {
        padding: 0.7rem 1.5rem;
        border-radius: 10px;
        font-weight: 500;
        cursor: pointer;
    }
    
    .custom-modal-footer .btn-secondary {
        background-color: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #475569;
    }
    
    .custom-modal-footer .btn-secondary:hover {
        background-color: #e2e8f0;
    }
    
    .custom-modal-footer .btn-danger {
        background: #dc3545;
        border: none;
        color: white;
    }
    
    .custom-modal-footer .btn-danger:hover {
        background: #c82333;
    }
    
    /* Prevent body scroll when modal is open */
    body.modal-open {
        overflow: hidden;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Open custom modal
    function openCustomModal(orderId) {
        const modal = document.getElementById('customModal' + orderId);
        if (modal) {
            modal.style.display = 'block';
            document.body.classList.add('modal-open');
            document.body.style.overflow = 'hidden';
        }
    }
    
    // Close custom modal
    function closeCustomModal(orderId) {
        const modal = document.getElementById('customModal' + orderId);
        if (modal) {
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
        }
    }
    
    // Close modal when clicking outside
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.custom-modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                }
            });
        });
        
        // Form submission loading state
        document.querySelectorAll('form[id^="rejectForm"]').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Inashughulikia...';
                }
            });
        });
        
        // Tabs handling
        const hash = window.location.hash;
        if (hash) {
            const tab = document.querySelector(`button[data-bs-target="${hash}"]`);
            if (tab) try { new bootstrap.Tab(tab).show(); } catch(e) {}
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\retailer\orders\incoming.blade.php ENDPATH**/ ?>