<?php $__env->startSection('title', 'Sasisha Hisa'); ?>
<?php $__env->startSection('page-title', isset($inventory) ? 'Hariri Hisa' : 'Ongeza Hisa Mpya'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-pencil-square me-2"></i>Maelezo ya Hisa</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('retailer.inventory.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php if(isset($inventory)): ?>
                        <?php echo method_field('PUT'); ?>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="product_id" class="form-label">Chagua Bidhaa</label>
                        <select class="form-select" id="product_id" name="product_id" required <?php echo e(isset($inventory) ? 'disabled' : ''); ?>>
                            <option value="">-- Chagua Bidhaa --</option>
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($product->id); ?>" 
                                    <?php echo e(old('product_id', $inventory->product_id ?? '') == $product->id ? 'selected' : ''); ?>>
                                    <?php echo e($product->name); ?> (<?php echo e($product->weight_kg); ?>kg) - 
                                    <?php echo e($product->service_type == 'new_cylinder' ? 'Mtungi Mpya' : 'Refill'); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php if(isset($inventory)): ?>
                            <input type="hidden" name="product_id" value="<?php echo e($inventory->product_id); ?>">
                            <small class="text-muted">Bidhaa haiwezi kubadilishwa wakati wa kuhariri.</small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="service_type" class="form-label">Aina ya Huduma</label>
                        <select class="form-select" id="service_type" name="service_type" required <?php echo e(isset($inventory) ? 'disabled' : ''); ?>>
                            <option value="">-- Chagua --</option>
                            <option value="new_cylinder" <?php echo e(old('service_type', $inventory->product->service_type ?? '') == 'new_cylinder' ? 'selected' : ''); ?>>Mtungi Mpya</option>
                            <option value="refill_exchange" <?php echo e(old('service_type', $inventory->product->service_type ?? '') == 'refill_exchange' ? 'selected' : ''); ?>>Kubadilisha (Refill)</option>
                        </select>
                        <?php if(isset($inventory)): ?>
                            <input type="hidden" name="service_type" value="<?php echo e($inventory->product->service_type); ?>">
                        <?php endif; ?>
                        <small class="text-muted">Hii itaonekana kwa wateja wanapotafuta.</small>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Idadi (Stock)</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" 
                               value="<?php echo e(old('quantity', $inventory->quantity ?? 0)); ?>" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="price_override" class="form-label">Bei ya Kuuza (TZS) - Hiari</label>
                        <input type="number" class="form-control" id="price_override" name="price_override" 
                               value="<?php echo e(old('price_override', $inventory->price_override ?? '')); ?>" 
                               placeholder="Acha tupu kutumia bei ya mwongozo">
                        <small class="text-muted">Kama utaacha wazi, mfumo utatumia bei iliyowekwa na Admin.</small>
                    </div>

                    
                    <?php if(isset($inventory) && $inventory->product): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Bei ya mwongozo: 
                        <strong>TZS <?php echo e(number_format($inventory->product->suggested_retail_price)); ?></strong>
                    </div>
                    <?php endif; ?>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> <?php echo e(isset($inventory) ? 'Sasisha' : 'Hifadhi'); ?> Hisa
                        </button>
                        <a href="<?php echo e(route('retailer.inventory.index')); ?>" class="btn btn-outline-secondary">
                            Ghairi
                        </a>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4 mt-3">
            <div class="card-body">
                <h6 class="fw-bold"><i class="bi bi-lightbulb text-warning me-2"></i>Kidokezo</h6>
                <p class="mb-0 small text-muted">
                    Unahitaji kuongeza hisa nyingi? Tumia kitufe cha 
                    <a href="<?php echo e(route('retailer.procurement.browse')); ?>" class="text-primary">Nunua kwa Jumla</a> 
                    kuagiza moja kwa moja kutoka kwa Wholesaler.
                </p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\retailer\inventory\update.blade.php ENDPATH**/ ?>