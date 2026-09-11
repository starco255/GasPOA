<?php $__env->startSection('title', 'Fuatilia Agizo'); ?>
<?php $__env->startSection('page-title', 'Fuatilia Agizo Lako'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $order = $order ?? null;
    
    if (!$order) {
        echo '<div class="alert alert-danger">Agizo halikupatikana.</div>';
        return;
    }   
    $steps = [
        'pending' => 0,
        'accepted' => 1,
        'picked_up' => 2,
        'out_for_delivery' => 2,
        'delivered' => 3,
    ];
    $currentStep = $steps[$order->status] ?? 0;
    $stepLabels = ['Imepokelewa', 'Imekubaliwa', 'Njiani', 'Imekamilika'];
    $stepIcons = ['bi-check-lg', 'bi-check-lg', 'bi-truck', 'bi-box-seam'];
    
    $statusBadges = [
        'pending' => 'bg-secondary',
        'accepted' => 'bg-info',
        'picked_up' => 'bg-primary',
        'out_for_delivery' => 'bg-warning text-dark',
        'delivered' => 'bg-success',
        'cancelled' => 'bg-danger',
    ];
    $statusBadge = $statusBadges[$order->status] ?? 'bg-secondary';
    
    $statusLabels = [
        'pending' => 'Inasubiri',
        'accepted' => 'Imekubaliwa',
        'picked_up' => 'Imeshachukuliwa',
        'out_for_delivery' => 'Njiani',
        'delivered' => 'Imekamilika',
        'cancelled' => 'Imefutwa',
    ];
    $statusLabel = $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));
    
    $estimatedDelivery = isset($estimatedDelivery) ? $estimatedDelivery : now()->addMinutes(30);
    $estimatedTimestamp = $estimatedDelivery->timestamp * 1000;
    
    $productName = 'Bidhaa';
    $productPrice = 0;
    $quantity = 1;
    if ($order->items && count($order->items) > 0) {
        $firstItem = $order->items->first();
        $productName = $firstItem->product->name ?? 'Bidhaa';
        $productPrice = $firstItem->price_per_item ?? 0;
        $quantity = $firstItem->quantity ?? 1;
    }
    
    $subtotal = $productPrice * $quantity;
    $isUrgent = ($order->urgency_level ?? 'normal') === 'urgent';
    $deliveryFee = 0;
    $urgencyFee = $isUrgent ? 3000 : 0;
    $displayTotal = $order->total_amount ?? ($subtotal + $urgencyFee);
    
    // RETAILER DETAILS
    $retailerName = $order->retailer->business_name ?? 'Inatafuta muuzaji...';
    $retailerPhone = $order->retailer->user->phone_number ?? ($order->retailer->phone_number ?? 'Haijulikani');
    $retailerAddress = $order->retailer->physical_address ?? 'Haijabainishwa';
    $retailerLat = $order->retailer->shop_latitude ?? -6.792354;
    $retailerLng = $order->retailer->shop_longitude ?? 39.208328;
    $retailerOpen = $order->retailer->is_open ?? true;
    $retailerTIN = $order->retailer->tinn_number ?? 'Haijulikani';
    $retailerRadius = $order->retailer->service_radius_km ?? 5;
    $retailerDelivery = $order->retailer->can_deliver ?? false;
    
    $orderDate = $order->created_at ? $order->created_at->format('d M Y, H:i') : 'Hivi karibuni';
    
    $paymentMethod = $order->payment_method_provider ?? $order->payment_method ?? 'cash';
    $paymentMethodLabels = [
        'cash' => 'Pesa Taslimu',
        'mobile_money' => 'M-Pesa/TigoPesa/Airtel Money',
        'mpesa' => 'M-Pesa',
        'tigopesa' => 'TigoPesa / Mixx by Yas',
        'airtelmoney' => 'Airtel Money',
        'halopesa' => 'HaloPesa',
        'bank' => 'Benki',
        'card' => 'Kadi ya Benki',
    ];
    $paymentMethodLabel = $paymentMethodLabels[$paymentMethod] ?? ucfirst($paymentMethod);
    $bankName = match (strtolower((string) ($order->retailer->bank_name ?? ''))) {
        'nmb' => 'NMB', 'crdb' => 'CRDB', 'nbc' => 'NBC', 'other', 'nyingine' => 'Nyingine',
        default => $order->retailer->bank_name ?? '',
    };
    $paymentAccount = match ($paymentMethod) {
        'mpesa' => $order->retailer->mpesa_number ?? null,
        'tigopesa' => $order->retailer->mixx_number ?? null,
        'airtelmoney' => $order->retailer->airtel_number ?? null,
        'halopesa' => $order->retailer->halopesa_number ?? null,
        default => null,
    };
    
    // COORDINATES
    $consumerLat = $order->delivery_latitude ?? -6.792354;
    $consumerLng = $order->delivery_longitude ?? 39.208328;
    
    // Google Maps URL
    $googleMapsUrl = "https://www.google.com/maps/dir/{$retailerLat},{$retailerLng}/{$consumerLat},{$consumerLng}";
?>

<div class="row g-4">
    
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 fade-in-up">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <span class="text-muted">Agizo #</span>
                        <span class="fw-bold fs-5"><?php echo e($order->order_number); ?></span>
                
                    </div>
                    <?php if($order->status != 'delivered' && $order->status != 'cancelled'): ?>
                    <div class="mt-2 mt-sm-0">
                        <span class="text-muted">Muda wa Kukadiriwa Kufika:</span>
                        <span class="fw-bold text-success fs-4" id="countdown" data-estimated="<?php echo e($estimatedTimestamp); ?>">Inapakiwa...</span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="steps mt-4 d-flex justify-content-between">
                    <?php for($i = 0; $i < 4; $i++): ?>
                        <div class="step <?php echo e($i < $currentStep ? 'completed' : ''); ?> <?php echo e($i == $currentStep ? 'active' : ''); ?>">
                            <div class="step-icon"><i class="bi <?php echo e($stepIcons[$i]); ?>"></i></div>
                            <div class="step-label"><?php echo e($stepLabels[$i]); ?></div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h5 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Maelezo ya Agizo</h5>
            </div>
            <div class="card-body pt-3">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between gap-3">
                            <span><?php echo e($quantity); ?>x <?php echo e($productName); ?></span>
                            <span class="text-nowrap">TZS <?php echo e(number_format($subtotal)); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mt-2 text-muted small">
                            <span>Usafirishaji</span><span>TZS 0 (Bure)</span>
                        </div>
                        <?php if($isUrgent): ?>
                            <div class="d-flex justify-content-between mt-1 text-muted small">
                                <span>Ada ya Haraka</span><span>TZS <?php echo e(number_format($urgencyFee)); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between fw-bold text-primary mt-2 pt-2 border-top">
                            <span>Jumla</span><span>TZS <?php echo e(number_format($displayTotal)); ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small text-muted"><i class="bi bi-calendar me-1"></i><?php echo e($orderDate); ?></div>
                        <div class="small mt-2"><i class="bi bi-credit-card me-1"></i><?php echo e($paymentMethodLabel); ?></div>
                        <?php if($order->payment_status): ?>
                            <div class="small mt-2"><i class="bi bi-wallet me-1"></i>
                                <span class="badge <?php echo e($order->payment_status == 'paid' ? 'bg-success' : 'bg-warning text-dark'); ?>"><?php echo e($order->payment_status == 'paid' ? 'Imelipwa' : 'Haijalipwa'); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-5">
                        <?php if($paymentMethod === 'cash'): ?>
                            <div class="alert alert-info py-2 mb-0 small">Pesa taslimu: lipa kwa muuzaji wakati wa kupokea bidhaa.</div>
                        <?php else: ?>
                            <div class="small mb-2">
                                <strong><?php echo e($paymentMethodLabel); ?>:</strong>
                                <?php if($paymentMethod === 'bank' || $paymentMethod === 'card'): ?>
                                    <?php echo e($bankName ?: 'Benki'); ?> — <?php echo e($order->retailer->bank_account_number ?? 'Haijawekwa'); ?>

                                <?php else: ?>
                                    Lipa Namba <?php echo e($paymentAccount ?: 'Haijawekwa'); ?>

                                <?php endif; ?>
                            </div>
                            <?php if($order->payment_status === 'pending'): ?>
                                <?php if(($manualPayment->status ?? null) === 'failed' && ($manualPayment->provider ?? null) === 'manual'): ?>
                                    <div class="text-danger small mb-2"><i class="bi bi-x-circle-fill me-1"></i>Reference ID imekataliwa; tuma namba sahihi.</div>
                                <?php elseif($order->transaction_reference): ?>
                                    <div class="text-info small mb-2"><i class="bi bi-send-check-fill me-1"></i>Reference ID imetumwa; unaweza kuihariri kabla ya uthibitisho.</div>
                                <?php endif; ?>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <form method="POST" action="<?php echo e(route('consumer.order.payment-submitted', $order)); ?>" class="payment-reference-form flex-grow-1">
                                        <?php echo csrf_field(); ?>
                                        <div class="input-group input-group-sm">
                                            <input id="transactionReference" class="form-control transaction-reference-input" name="transaction_reference" type="text" inputmode="numeric" pattern="[0-9]{10,}" minlength="10" maxlength="100" value="<?php echo e(old('transaction_reference', $order->transaction_reference)); ?>" placeholder="Transaction ID (tarakimu 10+)" autocomplete="off" required>
                                            <button class="btn btn-success reference-submit-button"><i class="bi bi-send-check me-1"></i><?php echo e($order->transaction_reference ? 'Tuma upya' : 'Tuma'); ?></button>
                                        </div>
                                    </form>
                                    <?php if(empty($order->transaction_reference)): ?>
                                        <form method="POST" action="<?php echo e(route('consumer.order.pay', $order)); ?>"><?php echo csrf_field(); ?>
                                            <button class="btn btn-primary btn-sm"><i class="bi bi-shield-lock me-1"></i>Lipa kwa ClickPesa</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php elseif($order->transaction_reference): ?>
                                <div class="text-success small"><i class="bi bi-lock-fill me-1"></i>Reference ID <?php echo e($order->transaction_reference); ?> imethibitishwa.</div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Ramani ya Ufuatiliaji</h5>
                <a href="<?php echo e($googleMapsUrl); ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Fungua kwenye Google Maps">
                    <i class="bi bi-box-arrow-up-right"></i> Google Maps
                </a>
            </div>
            <div class="card-body p-3">
                
                <div id="trackingMap" style="height: 350px; border-radius: 12px;"></div>
                
                
                <div class="mt-2 d-flex flex-wrap gap-3 small text-muted">
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-circle-fill text-success"></i> <strong>Duka (<?php echo e($retailerName); ?>)</strong>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="bi bi-circle-fill text-danger"></i> <strong>Ufikishaji (<?php echo e($order->delivery_address ?? 'Wewe'); ?>)</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold"><i class="bi bi-shop me-2"></i>Maelezo ya Muuzaji</h5>
                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#retailerDetailsModal" title="Taarifa Zaidi">
                    <i class="bi bi-info-circle"></i> Zaidi
                </button>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-shop-window text-success fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0"><?php echo e($retailerName); ?></h6>
                        <small class="text-muted">
                            <?php if($retailerOpen): ?>
                                <span class="text-success"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Liko Wazi</span>
                            <?php else: ?>
                                <span class="text-danger"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Limefungwa</span>
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
                
                <p class="mb-2"><i class="bi bi-geo-alt me-2 text-muted"></i> <?php echo e($retailerAddress); ?></p>
                <p class="mb-2">
                    <i class="bi bi-telephone me-2 text-muted"></i> 
                    <a href="tel:<?php echo e($retailerPhone); ?>" class="text-decoration-none"><?php echo e($retailerPhone); ?></a>
                </p>
                <p class="mb-2"><i class="bi bi-truck me-2 text-muted"></i> 
                    <?php if($retailerDelivery): ?> <span class="text-success">Inapatikana</span> (km <?php echo e($retailerRadius); ?>)
                    <?php else: ?> <span class="text-secondary">Haipatikani</span> <?php endif; ?>
                </p>
                
                <hr>
                <h6><i class="bi bi-geo-alt me-2"></i>Anwani ya Kufikishia:</h6>
                <p class="text-muted"><?php echo e($order->delivery_address ?? 'Haijabainishwa'); ?></p>
                
                <hr>
                <div class="d-grid gap-2">
                    <a href="tel:<?php echo e($retailerPhone); ?>" class="btn btn-outline-primary">
                        <i class="bi bi-telephone-fill"></i> Mpigie Simu
                    </a>
                    <button class="btn btn-outline-secondary" onclick="window.location.reload()">
                        <i class="bi bi-arrow-repeat"></i> Onesha Upya
                    </button>
                </div>
            </div>
        </div>

    </div>

    
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-chat-dots me-2"></i>Mazungumzo na Muuzaji</h5>
            </div>
            <div class="card-body">
                <div class="chat-messages" style="height: 280px; overflow-y: auto;" id="chatMessages">
                    <?php $__empty_1 = true; $__currentLoopData = $messages ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php if($msg->sender_id == Auth::id()): ?>
                            <div class="d-flex justify-content-end mb-3">
                                <div class="chat-bubble-right">
                                    <strong>Wewe</strong>
                                    <p class="mb-0 mt-1"><?php echo e($msg->message); ?></p>
                                    <small class="opacity-75"><?php echo e($msg->created_at->format('H:i')); ?></small>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="d-flex mb-3">
                                <div class="chat-bubble-left">
                                    <strong><?php echo e($msg->sender->full_name ?? 'Muuzaji'); ?></strong>
                                    <p class="mb-0 mt-1"><?php echo e($msg->message); ?></p>
                                    <small class="text-muted"><?php echo e($msg->created_at->format('H:i')); ?></small>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center text-muted py-5" id="emptyChatMessage">
                            <i class="bi bi-chat-dots fs-1"></i>
                            <p class="mt-2">Bado hakuna mazungumzo.</p>
                            <small>Anza mazungumzo na muuzaji kwa kutuma ujumbe hapa chini.</small>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if($order->status != 'delivered' && $order->status != 'cancelled'): ?>
                <form method="POST" action="<?php echo e(route('consumer.chat.send', $order->id)); ?>" class="chat-form mt-4" id="chatForm">
                    <?php echo csrf_field(); ?>
                    <div class="chat-input-wrapper">
                        <input type="text" name="message" class="chat-input" id="messageInput" placeholder="Andika ujumbe..." required autocomplete="off">
                        <button type="submit" class="chat-send-btn" id="sendMessageBtn"><i class="bi bi-send-fill"></i></button>
                    </div>
                </form>
                <?php else: ?>
                <div class="text-center text-muted py-3"><small><i class="bi bi-info-circle"></i> Mazungumzo yamefungwa kwa agizo hili.</small></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    
    <div class="col-12">
        <div class="d-flex gap-2 justify-content-between">
            <a href="<?php echo e(route('consumer.dashboard')); ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Rudi Dashboard</a>
            <div>
                <?php if(in_array($order->status, ['pending', 'accepted'])): ?>
                    <form method="POST" action="<?php echo e(route('consumer.order.cancel', $order->id)); ?>" class="d-inline" onsubmit="return confirm('Una uhakika unataka kufuta agizo hili?');">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-outline-danger me-2"><i class="bi bi-x-circle"></i> Futa Agizo</button>
                    </form>
                <?php endif; ?>
                <a href="<?php echo e(route('consumer.history')); ?>" class="btn btn-outline-primary"><i class="bi bi-clock-history"></i> Historia</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="retailerDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-shop me-2 text-success"></i>Taarifa za Muuzaji</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4">
                <div class="text-center mb-4">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-shop-window text-success" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="fw-bold"><?php echo e($retailerName); ?></h4>
                    <span class="badge <?php echo e($retailerOpen ? 'bg-success' : 'bg-danger'); ?> px-3 py-2 rounded-pill"><?php echo e($retailerOpen ? 'Liko Wazi' : 'Limefungwa'); ?></span>
                </div>
                <hr>
                <div class="mb-3"><h6 class="fw-bold"><i class="bi bi-geo-alt me-2 text-danger"></i>Anwani ya Duka</h6><p class="text-muted"><?php echo e($retailerAddress); ?></p></div>
                <div class="row mb-3">
                    <div class="col-6"><h6 class="fw-bold"><i class="bi bi-telephone me-2 text-primary"></i>Simu</h6><a href="tel:<?php echo e($retailerPhone); ?>" class="text-decoration-none"><?php echo e($retailerPhone); ?></a></div>
                    <div class="col-6"><h6 class="fw-bold"><i class="bi bi-hash me-2 text-secondary"></i>TIN</h6><p class="text-muted mb-0"><?php echo e($retailerTIN); ?></p></div>
                </div>
                <div class="row mb-3">
                    <div class="col-6"><h6 class="fw-bold"><i class="bi bi-truck me-2 text-info"></i>Usafirishaji</h6><p class="text-muted mb-0"><?php echo e($retailerDelivery ? 'Ndiyo (km '.$retailerRadius.')' : 'Hapana'); ?></p></div>
                    <div class="col-6"><h6 class="fw-bold"><i class="bi bi-geo me-2 text-warning"></i>Coordinates</h6><p class="text-muted mb-0 small"><?php echo e(number_format($retailerLat,6)); ?>, <?php echo e(number_format($retailerLng,6)); ?></p></div>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 px-4">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Funga</button>
                <a href="tel:<?php echo e($retailerPhone); ?>" class="btn btn-primary rounded-pill px-4"><i class="bi bi-telephone-fill me-1"></i> Mpigie Simu</a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .chat-messages { background: linear-gradient(145deg, #a3a3e4 0%, #b3b896 100%); border-radius: 24px; padding: 1.5rem 1rem; box-shadow: inset 0 2px 8px rgba(0,0,0,0.03); scroll-behavior: smooth; }
    .chat-messages::-webkit-scrollbar { width: 5px; }
    .chat-messages::-webkit-scrollbar-track { background: transparent; }
    .chat-messages::-webkit-scrollbar-thumb { background: linear-gradient(145deg, #a3a3e4 0%, #b3b896 100%); border-radius: 20px; }
    .chat-bubble-left { background: white; border-radius: 18px 18px 18px 4px; padding: 12px 16px; box-shadow: 0 2px 5px rgba(0,0,0,0.03); max-width: 75%; }
    .chat-bubble-left p { color: #334155; line-height: 1.5; }
    .chat-bubble-left small { display: block; margin-top: 8px; font-size: 0.7rem; color: #64748b; text-align: right; }
    .chat-bubble-right { background-color: #979696; color: white; border-radius: 18px 18px 4px 18px; padding: 12px 16px; max-width: 75%; }
    .chat-bubble-right:hover { transform: scale(1.01); box-shadow: 0 12px 20px rgba(255, 107, 53, 0.25); }
    .chat-bubble-right strong { color: rgba(255,255,255,0.9); font-size: 0.9rem; display: block; margin-bottom: 4px; }
    .chat-bubble-right p { color: white; line-height: 1.5; }
    .chat-bubble-right small { display: block; margin-top: 8px; font-size: 0.7rem; color: rgba(255,255,255,0.7); text-align: right; }
    .chat-form { width: auto; }
    .chat-input-wrapper { display: flex; align-items: center; gap: 12px; background: linear-gradient(145deg, #a3a3e4 0%, #c0c0d3 100%); padding: 6px 6px 6px 20px; border-radius: 60px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04); border: 1.5px solid #eef2f6; transition: all 0.3s ease; }
    .chat-input-wrapper:focus-within { border-color: #a0593f; box-shadow: 0 8px 25px rgba(255, 107, 53, 0.12); }
    .chat-input { flex: 1; border: none; outline: none; background: transparent; padding: 14px 0; font-size: 1rem; color: #1A1A2E; }
    .chat-input::placeholder { color: #535353; font-weight: 400; }
    .chat-send-btn { width: 56px; height: 56px; border-radius: 50%; background-color: #abb66e; border: white; color: white; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: 0 6px 14px rgba(255, 107, 53, 0.25); }
    .chat-send-btn i { font-size: 1.4rem; transition: transform 0.2s; }
    .chat-send-btn:hover { transform: scale(1.05); box-shadow: 0 10px 20px rgba(255, 107, 53, 0.35); background-color: #abb66e; }
    .chat-send-btn:hover i { transform: translateX(2px) rotate(-5deg); }
    .chat-send-btn:active { transform: scale(0.98); box-shadow: 0 4px 10px rgba(255, 107, 53, 0.3); }
    @media (max-width: 576px) { .chat-input-wrapper { padding: 4px 4px 4px 16px; } .chat-input { padding: 12px 0; font-size: 0.95rem; } .chat-send-btn { width: 48px; height: 48px; } .chat-send-btn i { font-size: 1.2rem; } .chat-bubble-left, .chat-bubble-right { max-width: 90%; padding: 12px 14px; } }
    @keyframes messageIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .chat-bubble-left, .chat-bubble-right { animation: messageIn 0.3s ease forwards; }
    .steps { position: relative; margin-bottom: 10px; }
    .step { text-align: center; flex: 1; position: relative; z-index: 1; }
    .step-icon { width: 45px; height: 45px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: #6c757d; transition: all 0.3s ease; }
    .step.completed .step-icon { background: #198754; color: white; box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.2); }
    .step.active .step-icon { background: var(--gaspoa-primary, #300027); color: white; box-shadow: 0 0 0 5px rgba(255, 107, 53, 0.2); }
    .step-label { font-size: 0.85rem; font-weight: 500; }
    .steps:before { content: ''; position: absolute; top: 22px; left: 12%; right: 12%; height: 3px; background: #dee2e6; z-index: 0; }
    #trackingMap { border: 1px solid #ddd; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Countdown Timer
        const countdownEl = document.getElementById('countdown');
        if (countdownEl) {
            const estimatedTimestamp = parseInt(countdownEl.dataset.estimated);
            function updateCountdown() {
                const now = new Date().getTime();
                const distance = estimatedTimestamp - now;
                if (distance < 0) {
                    countdownEl.innerHTML = 'Imefika au imechelewa';
                    countdownEl.classList.remove('text-success');
                    countdownEl.classList.add('text-warning');
                    return;
                }
                const minutes = Math.floor(distance / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                countdownEl.innerHTML = `${minutes} dak ${seconds} sec`;
            }
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }

        // Initialize Leaflet Map
        const retailerLat = <?php echo e($retailerLat); ?>;
        const retailerLng = <?php echo e($retailerLng); ?>;
        const consumerLat = <?php echo e($consumerLat); ?>;
        const consumerLng = <?php echo e($consumerLng); ?>;
        
        const map = L.map('trackingMap').setView([retailerLat, retailerLng], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Custom icons
        const shopIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        
        const deliveryIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Add markers
        const shopMarker = L.marker([retailerLat, retailerLng], {icon: shopIcon})
            .addTo(map)
            .bindPopup('<strong>🏪 <?php echo e($retailerName); ?></strong><br><?php echo e($retailerAddress); ?>');
        
        const deliveryMarker = L.marker([consumerLat, consumerLng], {icon: deliveryIcon})
            .addTo(map)
            .bindPopup('<strong>📍 Ufikishaji</strong><br><?php echo e($order->delivery_address ?? 'Eneo lako'); ?>');

        // Draw route line
        const routeLine = L.polyline([
            [retailerLat, retailerLng],
            [consumerLat, consumerLng]
        ], {
            color: '#FF6B35',
            weight: 4,
            opacity: 0.8,
            dashArray: '10, 10'
        }).addTo(map);

        // Fit map to show both points
        const group = new L.featureGroup([shopMarker, deliveryMarker]);
        map.fitBounds(group.getBounds().pad(0.2));

        // Chat functionality (unchanged)
        const chatMessages = document.getElementById('chatMessages');
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        const emptyChatMessage = document.getElementById('emptyChatMessage');

        if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;

        if (chatForm) {
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const message = messageInput.value.trim();
                if (!message) return;
                const formData = new FormData(this);
                const sendBtn = document.getElementById('sendMessageBtn');
                sendBtn.disabled = true;
                messageInput.disabled = true;
                sendBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const now = new Date();
                        const time = now.toLocaleTimeString('sw-TZ', { hour: '2-digit', minute: '2-digit', timeZone: 'Africa/Dar_es_Salaam', hour12: false });
                        const messageHtml = `
                            <div class="d-flex justify-content-end mb-3">
                                <div class="chat-bubble-right">
                                    <strong>Wewe</strong>
                                    <p class="mb-0 mt-1">${escapeHtml(message)}</p>
                                    <small class="opacity-75">${time}</small>
                                </div>
                            </div>
                        `;
                        if (emptyChatMessage) emptyChatMessage.style.display = 'none';
                        chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                        messageInput.value = '';
                    } else {
                        alert(data.message || 'Imeshindikana kutuma ujumbe.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Imeshindikana kutuma ujumbe. Tafadhali jaribu tena.');
                })
                .finally(() => {
                    sendBtn.disabled = false;
                    messageInput.disabled = false;
                    sendBtn.innerHTML = '<i class="bi bi-send-fill"></i>';
                    messageInput.focus();
                });
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Reference ID ni tarakimu 10 au zaidi. Hii huzuia kutuma mapema,
        // huku validation ya server ikibaki kinga ya mwisho.
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

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views/consumer/order/tracking.blade.php ENDPATH**/ ?>