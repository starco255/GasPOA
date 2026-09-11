<?php $__env->startSection('title', 'Kikapu cha Manunuzi'); ?>
<?php $__env->startSection('page-title', 'Kikapu Changu'); ?>

<?php $__env->startSection('content'); ?>
<div class="row"><div class="col-lg-10 mx-auto"><div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-0"><h5 class="fw-bold"><i class="bi bi-cart3 me-2 text-primary"></i>Bidhaa Zilizomo Kikapuni</h5><p class="text-muted small">Kila wholesaler ana njia zake za malipo. Chagua moja kwa kila agizo.</p></div>
    <div class="card-body">
    <?php if(count($cartItems)): ?>
        <div class="table-responsive"><table class="table table-hover align-middle"><thead class="table-light"><tr><th>Muuzaji</th><th>Bidhaa</th><th>Idadi</th><th>Bei/Unit</th><th>Jumla</th><th></th></tr></thead><tbody>
        <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr><td class="fw-medium"><?php echo e($item['wholesaler_name']); ?></td><td><?php echo e($item['product_name']); ?></td><td><form method="POST" action="<?php echo e(route('retailer.procurement.cart.update', $key)); ?>" class="d-inline-flex gap-2"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?><input type="number" name="quantity" value="<?php echo e($item['quantity']); ?>" min="5" class="form-control form-control-sm" style="width:80px"><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-repeat"></i></button></form></td><td>TZS <?php echo e(number_format($item['price'])); ?></td><td class="fw-bold">TZS <?php echo e(number_format($item['quantity'] * $item['price'])); ?></td><td><form method="POST" action="<?php echo e(route('retailer.procurement.cart.remove', $key)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-outline-danger" onclick="return confirm('Ondoa bidhaa hii?')"><i class="bi bi-trash"></i></button></form></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody></table></div>

        <?php ($cartWholesalers = collect($cartItems)->groupBy('wholesaler_id')); ?>
        <form method="POST" action="<?php echo e(route('retailer.procurement.checkout')); ?>" id="checkoutForm"><?php echo csrf_field(); ?>
        <div class="row g-3 mt-2">
            <div class="col-lg-7">
            <?php $__currentLoopData = $cartWholesalers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wholesalerId => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php ($methods = $wholesalerPaymentMethods[$wholesalerId] ?? []); ?>
                <div class="card border-0 bg-light mb-3"><div class="card-body"><h6 class="fw-bold"><i class="bi bi-building me-2"></i><?php echo e($items->first()['wholesaler_name']); ?></h6><p class="small text-muted">Agizo: TZS <?php echo e(number_format($items->sum(fn($item) => $item['quantity'] * $item['price']) + 15000)); ?> (pamoja na usafirishaji TZS 15,000)</p>
                <?php $__empty_1 = true; $__currentLoopData = $methods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <label class="border rounded p-3 d-block mb-2 bg-white"><input class="form-check-input me-2" required type="radio" name="payment_methods[<?php echo e($wholesalerId); ?>]" value="<?php echo e($method['value']); ?>"> <strong><?php echo e($method['label']); ?></strong>
                    <?php if(!empty($method['account'])): ?><span class="d-block small text-muted ms-4">Lipa kupitia: <?php echo e($method['account']); ?></span><?php endif; ?>
                    <?php if(!empty($method['bank_name'])): ?><span class="d-block small text-muted ms-4"><?php echo e($method['bank_name']); ?> — <?php echo e($method['account_number']); ?><br><?php echo e($method['account_name']); ?></span><?php endif; ?>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="alert alert-warning mb-0">Wholesaler huyu hajaweka njia ya malipo inayotumika. Wasiliana naye au ondoa bidhaa zake.</div>
                <?php endif; ?>
                </div></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="col-lg-5"><div class="card bg-light border-0"><div class="card-body"><h6 class="fw-bold">Muhtasari wa Agizo</h6><div class="d-flex justify-content-between mb-2"><span>Bidhaa:</span><span>TZS <?php echo e(number_format($subtotal)); ?></span></div><div class="d-flex justify-content-between mb-2"><span>Usafirishaji:</span><span>TZS <?php echo e(number_format($deliveryFee)); ?></span></div><hr><div class="d-flex justify-content-between fw-bold fs-5"><span>Jumla:</span><span class="text-primary">TZS <?php echo e(number_format($total)); ?></span></div></div></div>
            <button type="submit" class="btn btn-success btn-lg w-100 mt-3" <?php echo e(collect($wholesalerPaymentMethods)->contains(fn($methods) => empty($methods)) ? 'disabled' : ''); ?>><i class="bi bi-check2-circle me-2"></i>Thibitisha Agizo</button><a href="<?php echo e(route('retailer.procurement.browse')); ?>" class="btn btn-outline-secondary w-100 mt-2">Endelea Kununua</a>
            <p class="small text-muted text-center mt-2">Kwa ClickPesa iliyosanidiwa utaelekezwa kwenye checkout salama; vinginevyo agizo hubaki pending hadi malipo yathibitishwe.</p></div>
        </div></form>
    <?php else: ?>
        <div class="text-center py-5"><i class="bi bi-cart-x display-1 text-muted"></i><h5 class="fw-bold mt-3">Kikapu ni Tupu</h5><a href="<?php echo e(route('retailer.procurement.browse')); ?>" class="btn btn-primary mt-2">Nenda Soko la Jumla</a></div>
    <?php endif; ?>
    </div>
</div></div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views/retailer/procurement/cart.blade.php ENDPATH**/ ?>