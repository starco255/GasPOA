<?php $__env->startSection('title', 'Maelezo ya Agizo'); ?>
<?php $__env->startSection('page-title', 'Maelezo ya Agizo #' . $order->order_number); ?>

<?php $__env->startSection('content'); ?>
<?php
    $isUrgent = ($order->urgency_level ?? 'normal') === 'urgent';
    $urgencyFee = $isUrgent ? 3000 : 0;
    
    // Status badge
    $statusBadges = [
        'pending' => 'bg-secondary',
        'accepted' => 'bg-info',
        'picked_up' => 'bg-primary',
        'out_for_delivery' => 'bg-warning text-dark',
        'delivered' => 'bg-success',
        'cancelled' => 'bg-danger',
    ];
    $statusBadge = $statusBadges[$order->status] ?? 'bg-secondary';
    
    // Status label
    $statusLabels = [
        'pending' => 'Inasubiri',
        'accepted' => 'Imekubaliwa',
        'picked_up' => 'Imeshachukuliwa',
        'out_for_delivery' => 'Njiani',
        'delivered' => 'Imekamilika',
        'cancelled' => 'Imefutwa',
    ];
    $statusLabel = $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));
    
    // Payment method
    $paymentMethodLabels = [
        'cash' => 'Pesa Taslimu',
        'mobile_money' => 'M-Pesa/TigoPesa/Airtel Money',
        'card' => 'Kadi ya Benki',
        'mpesa' => 'M-Pesa',
        'tigopesa' => 'TigoPesa / Mixx by Yas',
        'airtelmoney' => 'Airtel Money',
        'halopesa' => 'HaloPesa',
        'bank' => 'Benki',
    ];
    $selectedPaymentMethod = $order->payment_method_provider ?? $order->payment_method;
    $paymentMethod = $paymentMethodLabels[$selectedPaymentMethod] ?? ucfirst($selectedPaymentMethod);
    
    $paymentStatusLabels = [
        'pending' => 'Haijalipwa',
        'paid' => 'Imelipwa',
        'failed' => 'Imeshindikana',
    ];
    $paymentStatus = $paymentStatusLabels[$order->payment_status] ?? ucfirst($order->payment_status);
    $paymentStatusBadge = $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-info-circle me-2"></i>Maelezo ya Agizo</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Namba ya Agizo</label>
                        <h6 class="fw-bold"><?php echo e($order->order_number); ?></h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Tarehe ya Kuagiza</label>
                        <h6><?php echo e($order->created_at ? $order->created_at->format('d M Y, H:i') : 'Hivi karibuni'); ?></h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Hali ya Agizo</label>
                        <h6><span class="badge <?php echo e($statusBadge); ?>"><?php echo e($statusLabel); ?></span></h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Kasi ya Huduma</label>
                        <h6>
                            <span class="badge <?php echo e($isUrgent ? 'bg-danger' : 'bg-secondary'); ?>">
                                <?php echo e($isUrgent ? 'Haraka' : 'Kawaida'); ?>

                            </span>
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-box-seam me-2"></i>Bidhaa Zilizoagizwa</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Bidhaa</th>
                                <th>Aina</th>
                                <th>Idadi</th>
                                <th>Bei/Unit</th>
                                <th>Jumla</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $product = $item->product;
                                $serviceType = $item->service_type_requested;
                                $serviceLabel = $serviceType === 'new_cylinder' ? 'Mtungi Mpya' : 'Kubadilisha';
                            ?>
                            <tr>
                                <td><?php echo e($product->name ?? 'Bidhaa'); ?> (<?php echo e($product->weight_kg ?? 0); ?>kg)</td>
                                <td><?php echo e($serviceLabel); ?></td>
                                <td><?php echo e($item->quantity); ?></td>
                                <td>TZS <?php echo e(number_format($item->price_per_item)); ?></td>
                                <td>TZS <?php echo e(number_format($item->price_per_item * $item->quantity)); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-receipt me-2"></i>Muhtasari wa Gharama</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <?php
                        $subtotal = $order->items->sum(function($item) {
                            return $item->price_per_item * $item->quantity;
                        });
                    ?>
                    <tr>
                        <td>Jumla ya Bidhaa:</td>
                        <td class="text-end">TZS <?php echo e(number_format($subtotal)); ?></td>
                    </tr>
                    <tr>
                        <td>Usafirishaji:</td>
                        <td class="text-end">TZS 0 (Bure)</td>
                    </tr>
                    <?php if($isUrgent): ?>
                    <tr>
                        <td>Ada ya Haraka:</td>
                        <td class="text-end">TZS 3,000</td>
                    </tr>
                    <?php endif; ?>
                    <tr class="border-top fw-bold">
                        <td>Jumla Kuu:</td>
                        <td class="text-end">TZS <?php echo e(number_format($order->total_amount)); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-shop me-2"></i>Maelezo ya Muuzaji</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Jina la Biashara</label>
                        <h6><?php echo e($order->retailer->business_name ?? 'Haijulikani'); ?></h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Namba ya Simu</label>
                        <h6><?php echo e($order->retailer->user->phone_number ?? 'Haijulikani'); ?></h6>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small">Anwani</label>
                        <h6><?php echo e($order->retailer->physical_address ?? 'Haijulikani'); ?></h6>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-geo-alt me-2"></i>Maelezo ya Ufikishaji</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Anwani ya Kufikishia</label>
                    <h6><?php echo e($order->delivery_address); ?></h6>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Latitude</label>
                        <h6><?php echo e($order->delivery_latitude); ?></h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Longitude</label>
                        <h6><?php echo e($order->delivery_longitude); ?></h6>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-credit-card me-2"></i>Maelezo ya Malipo</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Njia ya Malipo</label>
                        <h6><?php echo e($paymentMethod); ?></h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Hali ya Malipo</label>
                        <h6><span class="badge <?php echo e($paymentStatusBadge); ?>"><?php echo e($paymentStatus); ?></span></h6>
                    </div>
                    <?php if($order->transaction_reference): ?>
                    <div class="col-12 mb-3">
                        <label class="text-muted small">Kumbukumbu ya Muamala</label>
                        <h6><?php echo e($order->transaction_reference); ?></h6>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if($order->payment_status === 'paid' && $order->transaction_reference): ?>
                    <div class="alert alert-success mb-0"><i class="bi bi-lock-fill me-1"></i>Reference ID imethibitishwa na haiwezi kubadilishwa.</div>
                <?php elseif($order->payment_status !== 'paid'): ?>
                    <div class="alert alert-secondary mb-0 small"><i class="bi bi-info-circle me-1"></i>Haya ni maelezo ya agizo pekee. Tumia ukurasa wa kufuatilia agizo kufanya malipo au kutuma Reference ID.</div>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="d-flex gap-2 justify-content-between mb-4">
            <a href="<?php echo e(route('consumer.history')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Rudi kwenye Historia
            </a>
            <div>
                <?php if(in_array($order->status, ['pending', 'accepted', 'picked_up', 'out_for_delivery'])): ?>
                <a href="<?php echo e(route('consumer.order.tracking', $order->id)); ?>" class="btn btn-outline-primary me-2">
                    <i class="bi bi-geo-alt"></i> Fuatilia
                </a>
                <?php endif; ?>
                <?php if($order->payment_status === 'paid'): ?>
                    <a href="<?php echo e(route('consumer.order.receipt', $order->id)); ?>" class="btn btn-primary"><i class="bi bi-download"></i> Pakua Risiti</a>
                <?php else: ?>
                    <button type="button" class="btn btn-primary" disabled title="Risiti hupatikana baada ya malipo kuthibitishwa."><i class="bi bi-lock"></i> Pakua Risiti</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.transaction-reference-input').forEach(function(input) {
            const submitButton = input.closest('.payment-reference-form')?.querySelector('.reference-submit-button');
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

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\consumer\order\details.blade.php ENDPATH**/ ?>