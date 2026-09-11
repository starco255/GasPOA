<?php $__env->startSection('title', 'Manunuzi ya Jumla'); ?>

<?php $__env->startSection('content'); ?>


<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-clock-history me-2 text-primary"></i>Takwimu Fupi Manunuzi ya jumla
    </h4>
    <a href="<?php echo e(route('retailer.procurement.browse')); ?>" class="btn btn-success rounded-pill px-4">
        <i class="bi bi-cart-plus me-2"></i>Fanya Agizo Jipya
    </a>
</div>


<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10 h-100">
            <div class="card-body py-3">
                <h6 class="text-muted mb-1">Jumla ya Maagizo</h6>
                <h3 class="mb-0"><?php echo e($orders->total() ?? 0); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10 h-100">
            <div class="card-body py-3">
                <h6 class="text-muted mb-1">Yanayosubiri</h6>
                <h3 class="mb-0"><?php echo e($orders->where('status', 'pending')->count() ?? 0); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-info bg-opacity-10 h-100">
            <div class="card-body py-3">
                <h6 class="text-muted mb-1">Yanashughulikiwa</h6>
                <h3 class="mb-0"><?php echo e($orders->whereIn('status', ['confirmed', 'processing', 'dispatched'])->count() ?? 0); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-success bg-opacity-10 h-100">
            <div class="card-body py-3">
                <h6 class="text-muted mb-1">Yaliyokamilika</h6>
                <h3 class="mb-0"><?php echo e($orders->where('status', 'delivered')->count() ?? 0); ?></h3>
            </div>
        </div>
    </div>
</div>


<?php if(isset($orders) && count($orders) > 0): ?>
    <?php
        $groupedOrders = $orders->groupBy('status');
        $statusConfig = [
            'pending' => ['label' => 'Yanayosubiri', 'icon' => 'clock-history', 'color' => 'warning', 'bg' => 'warning'],
            'confirmed' => ['label' => 'Yamethibitishwa', 'icon' => 'check-circle', 'color' => 'info', 'bg' => 'info'],
            'processing' => ['label' => 'Yanashughulikiwa', 'icon' => 'gear', 'color' => 'primary', 'bg' => 'primary'],
            'dispatched' => ['label' => 'Yamesafirishwa', 'icon' => 'truck', 'color' => 'secondary', 'bg' => 'secondary'],
            'delivered' => ['label' => 'Yamekamilika', 'icon' => 'check2-circle', 'color' => 'success', 'bg' => 'success'],
            'cancelled' => ['label' => 'Yamefutwa', 'icon' => 'x-circle', 'color' => 'danger', 'bg' => 'danger'],
        ];
    ?>

    <?php $__currentLoopData = $statusConfig; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(isset($groupedOrders[$status]) && count($groupedOrders[$status]) > 0): ?>
        <div class="status-section mb-4">
            
            <div class="d-flex align-items-center mb-3">
                <div class="status-icon bg-<?php echo e($config['bg']); ?> bg-opacity-10 p-2 rounded-3 me-3">
                    <i class="bi bi-<?php echo e($config['icon']); ?> fs-4 text-<?php echo e($config['color']); ?>"></i>
                </div>
                <h5 class="fw-bold mb-0">
                    <?php echo e($config['label']); ?>

                    <span class="badge bg-<?php echo e($config['bg']); ?> bg-opacity-25 text-<?php echo e($config['color']); ?> ms-2 rounded-pill px-3">
                        <?php echo e(count($groupedOrders[$status])); ?>

                    </span>
                </h5>
            </div>

            
            <div class="row g-3">
                <?php $__currentLoopData = $groupedOrders[$status]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-12">
                    <div class="order-card card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-body p-0">
                            <div class="row g-0 align-items-stretch">
                                
                                <div class="col-auto bg-<?php echo e($config['bg']); ?> bg-opacity-25" style="width: 6px;"></div>
                                
                                
                                <div class="col p-3 p-md-4">
                                    <div class="row align-items-center">
                                        
                                        <div class="col-md-2 mb-3 mb-md-0">
                                            <div class="d-flex align-items-center">
                                                <div class="order-icon bg-<?php echo e($config['bg']); ?> bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="bi bi-box-seam text-<?php echo e($config['color']); ?>"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-0"><?php echo e($order->order_number); ?></h6>
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar3 me-1"></i>
                                                        <?php echo e($order->created_at->format('d M Y')); ?>

                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <div class="col-md-3 mb-3 mb-md-0">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-building text-muted me-2"></i>
                                                <div>
                                                    <span class="fw-medium"><?php echo e($order->wholesaler->business_name ?? 'Wholesaler'); ?></span>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo e(\Illuminate\Support\Str::limit($order->wholesaler->physical_address ?? '', 25)); ?>

                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        
                                        <div class="col-md-3 mb-3 mb-md-0">
                                            <?php
                                                $totalItems = $order->items->sum('quantity');
                                                $productsList = $order->items->take(2)->map(function($item) {
                                                    return $item->product->name . ' x' . $item->quantity;
                                                })->implode(' • ');
                                                $remainingCount = $order->items->count() - 2;
                                            ?>
                                            <div>
                                                <span class="text-muted small">Bidhaa (<?php echo e($totalItems); ?>)</span>
                                                <p class="mb-0 fw-medium">
                                                    <?php echo e(\Illuminate\Support\Str::limit($productsList, 35)); ?>

                                                    <?php if($remainingCount > 0): ?>
                                                        <span class="text-muted">+<?php echo e($remainingCount); ?> zaidi</span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>

                                        
                                        <div class="col-md-3 mb-3 mb-md-0">
                                            <?php
                                                $paymentMethodLabel = match($order->payment_method) {
                                                    'cash' => 'Pesa Taslimu', 'mpesa' => 'M-Pesa', 'tigopesa' => 'TigoPesa / Mixx by Yas',
                                                    'airtelmoney' => 'Airtel Money', 'halopesa' => 'HaloPesa', 'bank' => 'Benki', default => 'Haijachaguliwa',
                                                };
                                                $paymentAccount = match($order->payment_method) {
                                                    'mpesa' => $order->wholesaler->mpesa_number ?? null,
                                                    'tigopesa' => $order->wholesaler->mixx_number ?? null,
                                                    'airtelmoney' => $order->wholesaler->airtel_number ?? null,
                                                    'halopesa' => $order->wholesaler->halopesa_number ?? null,
                                                    'bank' => $order->wholesaler->bank_account_number ?? null,
                                                    default => null,
                                                };
                                            ?>
                                            <div class="payment-summary small">
                                                <div class="d-flex justify-content-between gap-2"><span class="text-muted">Kiasi</span><strong class="text-primary text-nowrap">TZS <?php echo e(number_format($order->total_amount)); ?></strong></div>
                                                <div class="d-flex justify-content-between gap-2 mt-1"><span class="text-muted">Hali</span>
                                                    <?php if($order->payment_status == 'paid'): ?>
                                                        <span class="badge bg-success">Imelipwa</span>
                                                    <?php elseif($order->payment_status == 'partial'): ?>
                                                        <span class="badge bg-warning text-dark">Sehemu</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark">Inasubiri</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="d-flex justify-content-between gap-2 mt-1"><span class="text-muted">Njia</span><span class="text-end"><?php echo e($paymentMethodLabel); ?></span></div>
                                                <?php if($order->payment_method !== 'cash'): ?>
                                                    <div class="d-flex justify-content-between gap-2 mt-1"><span class="text-muted">Namba</span><strong class="text-end"><?php echo e($paymentAccount ?: 'Haijawekwa'); ?></strong></div>
                                                <?php endif; ?>
                                            </div>
                                            <?php if($order->payment_status === 'pending' && $order->payment_method !== 'cash'): ?>
                                                <?php if($order->transaction_reference): ?>
                                                    <div class="text-info small mt-2"><i class="bi bi-send-check-fill me-1"></i>Transaction ID imetumwa; unaweza kuhariri na kutuma upya.</div>
                                                <?php endif; ?>
                                                <div class="d-flex flex-wrap gap-2 mt-2">
                                                    <form method="POST" action="<?php echo e(route('retailer.procurement.order.payment-submitted', $order)); ?>" class="payment-reference-form flex-grow-1"><?php echo csrf_field(); ?>
                                                        <div class="input-group input-group-sm">
                                                            <input class="form-control transaction-reference-input" name="transaction_reference" type="text" inputmode="numeric" pattern="[0-9]{10,}" minlength="10" maxlength="100" value="<?php echo e($order->transaction_reference); ?>" placeholder="Transaction ID" autocomplete="off" required>
                                                            <button class="btn btn-outline-success reference-submit-button"><?php echo e($order->transaction_reference ? 'Tuma upya' : 'Tuma'); ?></button>
                                                        </div>
                                                    </form>
                                                    <?php if(empty($order->transaction_reference)): ?>
                                                        <form method="POST" action="<?php echo e(route('retailer.procurement.order.pay', $order)); ?>"><?php echo csrf_field(); ?>
                                                            <button class="btn btn-primary btn-sm text-nowrap"><i class="bi bi-shield-lock me-1"></i>ClickPesa</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            <?php elseif($order->payment_status === 'paid' && $order->transaction_reference): ?>
                                                <div class="text-success small mt-2"><i class="bi bi-lock-fill me-1"></i>Transaction ID imethibitishwa.</div>
                                            <?php endif; ?>
                                        </div>

                                        
                                        <div class="col-md-1 text-md-end">
                                            <button class="btn btn-outline-primary btn-sm rounded-circle"
                                                    style="width: 40px; height: 40px; padding: 0;"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#orderModal<?php echo e($order->id); ?>">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($orders->links()); ?>

    </div>
<?php else: ?>
    <div class="text-center py-5">
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-inbox"></i>
            </div>
            <h5 class="fw-bold mt-4">Hakuna Maagizo ya Jumla</h5>
            <p class="text-muted mb-4">Bado hujaweka agizo lolote la jumla.</p>
            <a href="<?php echo e(route('retailer.procurement.browse')); ?>" class="btn btn-primary btn-lg rounded-pill px-5">
                <i class="bi bi-cart-plus me-2"></i> Nenda Soko la Jumla
            </a>
        </div>
    </div>
<?php endif; ?>


<?php if(isset($orders)): ?>
    <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="modal fade" id="orderModal<?php echo e($order->id); ?>" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-box-seam me-2 text-primary"></i>
                        Agizo #<?php echo e($order->order_number); ?>

                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    
                    <div class="order-timeline mb-4">
                        <?php
                            $steps = [
                                'pending' => ['label' => 'Imepokelewa', 'icon' => 'check-circle'],
                                'confirmed' => ['label' => 'Imethibitishwa', 'icon' => 'check2-circle'],
                                'processing' => ['label' => 'Inashughulikiwa', 'icon' => 'gear'],
                                'dispatched' => ['label' => 'Imesafirishwa', 'icon' => 'truck'],
                                'delivered' => ['label' => 'Imekamilika', 'icon' => 'flag'],
                            ];
                            $currentStepIndex = array_search($order->status, array_keys($steps));
                            if ($order->status == 'cancelled') $currentStepIndex = -1;
                        ?>
                        
                        <?php if($order->status != 'cancelled'): ?>
                        <div class="timeline-steps d-flex justify-content-between">
                            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepStatus => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $stepIndex = array_search($stepStatus, array_keys($steps));
                                    $isCompleted = $stepIndex <= $currentStepIndex;
                                    $isActive = $stepIndex == $currentStepIndex;
                                ?>
                                <div class="timeline-step <?php echo e($isCompleted ? 'completed' : ''); ?> <?php echo e($isActive ? 'active' : ''); ?>">
                                    <div class="step-icon">
                                        <i class="bi bi-<?php echo e($step['icon']); ?>"></i>
                                    </div>
                                    <div class="step-label"><?php echo e($step['label']); ?></div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle-fill me-2"></i>
                            <strong>Agizo limefutwa</strong>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-card bg-light rounded-3 p-3 h-100">
                                <h6 class="fw-bold mb-2">
                                    <i class="bi bi-building me-2"></i>Wholesaler
                                </h6>
                                <p class="mb-1 fw-semibold"><?php echo e($order->wholesaler->business_name ?? 'Wholesaler'); ?></p>
                                <p class="mb-1 text-muted small">
                                    <i class="bi bi-geo-alt me-1"></i><?php echo e($order->wholesaler->physical_address ?? 'Haipo'); ?>

                                </p>
                                <p class="mb-0 text-muted small">
                                    <i class="bi bi-telephone me-1"></i><?php echo e($order->wholesaler->user->phone_number ?? 'Haipo'); ?>

                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-card bg-light rounded-3 p-3 h-100">
                                <h6 class="fw-bold mb-2">
                                    <i class="bi bi-info-circle me-2"></i>Maelezo
                                </h6>
                                <p class="mb-1">
                                    <i class="bi bi-calendar me-2"></i><?php echo e($order->created_at->format('d M Y, H:i')); ?>

                                </p>
                                <p class="mb-1">
                                    <i class="bi bi-credit-card me-2"></i>
                                    <?php if($order->payment_status == 'paid'): ?>
                                        <span class="badge bg-success">Imelipwa</span>
                                    <?php elseif($order->payment_status == 'partial'): ?>
                                        <span class="badge bg-warning">Sehemu</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Haijalipwa</span>
                                    <?php endif; ?>
                                </p>
                                <?php if($order->delivered_at): ?>
                                <p class="mb-0 text-success">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Imepokelewa: <?php echo e($order->delivered_at->format('d M Y, H:i')); ?>

                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php
                        $paymentMethodLabel = match($order->payment_method) {
                            'cash' => 'Pesa Taslimu', 'mpesa' => 'M-Pesa', 'tigopesa' => 'TigoPesa / Mixx by Yas',
                            'airtelmoney' => 'Airtel Money', 'halopesa' => 'HaloPesa', 'bank' => 'Benki', default => 'Haijachaguliwa'
                        };
                        $bankName = match(strtolower((string) ($order->wholesaler->bank_name ?? ''))) {
                            'nmb' => 'NMB', 'crdb' => 'CRDB', 'nbc' => 'NBC', 'other' => 'Benki Nyingine',
                            default => $order->wholesaler->bank_name ?? ''
                        };
                    ?>
                    <div class="alert <?php echo e($order->payment_status === 'paid' ? 'alert-success' : 'alert-warning'); ?> border-0 mb-4">
                        <h6 class="fw-bold mb-2"><i class="bi bi-credit-card me-2"></i>Maelekezo ya Malipo</h6>
                        <p class="mb-1">Njia uliyochagua: <strong><?php echo e($paymentMethodLabel); ?></strong></p>
                        <?php if($order->payment_method === 'mpesa'): ?>
                            <p class="mb-1">Lipa Namba: <strong><?php echo e($order->wholesaler->mpesa_number ?: 'Haijawekwa'); ?></strong></p>
                        <?php elseif($order->payment_method === 'tigopesa'): ?>
                            <p class="mb-1">Lipa Namba: <strong><?php echo e($order->wholesaler->mixx_number ?: 'Haijawekwa'); ?></strong></p>
                        <?php elseif($order->payment_method === 'airtelmoney'): ?>
                            <p class="mb-1">Lipa Namba: <strong><?php echo e($order->wholesaler->airtel_number ?: 'Haijawekwa'); ?></strong></p>
                        <?php elseif($order->payment_method === 'halopesa'): ?>
                            <p class="mb-1">Lipa Namba: <strong><?php echo e($order->wholesaler->halopesa_number ?: 'Haijawekwa'); ?></strong></p>
                        <?php elseif($order->payment_method === 'bank'): ?>
                            <p class="mb-1"><?php echo e($bankName); ?> — <strong><?php echo e($order->wholesaler->bank_account_number ?: 'Haijawekwa'); ?></strong></p>
                            <p class="mb-1">Jina la Akaunti: <strong><?php echo e($order->wholesaler->bank_account_name ?: 'Haijawekwa'); ?></strong></p>
                        <?php elseif($order->payment_method === 'cash'): ?>
                            <p class="mb-1">Lipa pesa taslimu kulingana na maelekezo ya wholesaler wakati wa kupokea mzigo.</p>
                        <?php endif; ?>
                        <?php if($order->payment_status === 'paid'): ?>
                            <p class="mb-0"><i class="bi bi-check-circle-fill me-1"></i>Malipo yamekamilika<?php echo e($order->transaction_reference ? ' — Rejea: ' . $order->transaction_reference : ''); ?>.</p>
                        <?php else: ?>
                            <p class="mb-0"><i class="bi bi-clock-history me-1"></i>Lipa kwa taarifa hizi; status itakuwa <strong>Imelipwa</strong> baada ya ClickPesa au wholesaler kuthibitisha malipo yako.</p>
                        <?php endif; ?>
                    </div>
                    
                    <h6 class="fw-bold mb-2">
                        <i class="bi bi-cart me-2"></i>Bidhaa Zilizoagizwa
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Bidhaa</th>
                                    <th class="text-center">Idadi</th>
                                    <th class="text-end">Bei/Unit</th>
                                    <th class="text-end">Jumla</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="fw-medium"><?php echo e($item->product->name ?? 'Bidhaa'); ?></td>
                                    <td class="text-center"><?php echo e($item->quantity); ?></td>
                                    <td class="text-end">TZS <?php echo e(number_format($item->price_per_item)); ?></td>
                                    <td class="text-end fw-semibold">TZS <?php echo e(number_format($item->quantity * $item->price_per_item)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">Jumla Kuu:</th>
                                    <th class="text-end text-primary fs-5">TZS <?php echo e(number_format($order->total_amount)); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Funga
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Badge Styles */
    .badge.bg-info { background-color: #e6f3ff !important; color: #0056b3 !important; }
    .badge.bg-primary { background-color: #cfe2ff !important; color: #084298 !important; }
    .badge.bg-warning { background-color: #fff3cd !important; color: #856404 !important; }
    .badge.bg-success { background-color: #d1e7dd !important; color: #0f5132 !important; }
    .badge.bg-danger { background-color: #f8d7da !important; color: #842029 !important; }
    .badge.bg-secondary { background-color: #e9ecef !important; color: #495057 !important; }
    
    .bg-opacity-10 { --bs-bg-opacity: 0.1; }
    .bg-opacity-25 { --bs-bg-opacity: 0.25; }
    
    /* Order Card Styles */
    .order-card {
        transition: all 0.3s ease;
        border-left: none !important;
    }
    
    .order-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
    }
    
    .order-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Status Section */
    .status-section {
        animation: fadeInUp 0.4s ease forwards;
    }
    
    .status-icon {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
    
    /* Empty State */
    .empty-state {
        padding: 3rem;
    }
    
    .empty-state-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(145deg, #f8f9fa, #e9ecef);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 3rem;
        color: #adb5bd;
    }
    
    /* Timeline Steps */
    .timeline-steps {
        position: relative;
        padding: 0 10px;
    }
    
    .timeline-steps::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 15%;
        right: 15%;
        height: 3px;
        background: #e9ecef;
        z-index: 0;
    }
    
    .timeline-step {
        text-align: center;
        position: relative;
        z-index: 1;
        flex: 1;
    }
    
    .step-icon {
        width: 45px;
        height: 45px;
        background: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        color: #6c757d;
        transition: all 0.3s ease;
    }
    
    .timeline-step.completed .step-icon {
        background: #198754;
        color: white;
    }
    
    .timeline-step.active .step-icon {
        background: #0d6efd;
        color: white;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.2);
    }
    
    .step-label {
        font-size: 0.75rem;
        font-weight: 500;
        color: #6c757d;
    }
    
    .timeline-step.completed .step-label,
    .timeline-step.active .step-label {
        color: #212529;
        font-weight: 600;
    }
    
    /* Info Card */
    .info-card {
        transition: all 0.2s ease;
    }
    
    .info-card:hover {
        background-color: #e9ecef !important;
    }
    
    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Pagination */
    .pagination {
        gap: 5px;
    }
    
    .page-link {
        border-radius: 10px !important;
        border: none;
        padding: 0.5rem 1rem;
        color: #6c757d;
    }
    
    .page-item.active .page-link {
        background: linear-gradient(145deg, #FF6B35, #E85D2C);
        color: white;
    }
    
    /* Mobile Responsive */
    @media (max-width: 767.98px) {
        .timeline-step .step-label {
            font-size: 0.6rem;
        }
        
        .step-icon {
            width: 35px;
            height: 35px;
            font-size: 0.9rem;
        }
        
        .timeline-steps::before {
            top: 17px;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.transaction-reference-input').forEach(function(input) {
            const form = input.closest('.payment-reference-form');
            const submitButton = form ? form.querySelector('.reference-submit-button') : null;
            const validateReference = function() {
                input.value = input.value.replace(/\D/g, '').slice(0, 100);
                const isValid = input.value.length >= 10;
                input.setCustomValidity(isValid ? '' : 'Reference ID lazima iwe na tarakimu 10 au zaidi.');
                if (submitButton) submitButton.disabled = !isValid;
            };
            input.addEventListener('input', validateReference);
            validateReference();
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views/retailer/procurement/wholesale_orders.blade.php ENDPATH**/ ?>