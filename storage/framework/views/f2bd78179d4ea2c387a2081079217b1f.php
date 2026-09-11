<?php $__env->startSection('title', 'Soko la Jumla'); ?>
<?php $__env->startSection('page-title', 'Nunua Bidhaa Kutoka kwa Wauzaji wa Jumla'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">

        
        
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <?php if(($cartCount ?? 0) > 0): ?>
                    <a href="<?php echo e(route('retailer.procurement.cart')); ?>" class="btn btn-success rounded-pill px-4">
                        <i class="bi bi-cart-fill me-2"></i>
                        Kikapu (<?php echo e($cartCount); ?>) • TZS <?php echo e(number_format($cartTotal ?? 0)); ?>

                    </a>
                <?php endif; ?>
            </div>
            <a href="<?php echo e(route('retailer.procurement.wholesale_orders')); ?>" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-truck me-2"></i> Maagizo Yangu ya Jumla
            </a>
        </div>
        
        
        
        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h6 class="fw-bold"><i class="bi bi-funnel me-2"></i>Chuja Bidhaa</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('retailer.procurement.browse')); ?>" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Bidhaa</label>
                            <select class="form-select" name="product_id" id="filterProduct">
                                <option value="">Zote</option>
                                <?php if(isset($allProducts)): ?>
                                    <?php $__currentLoopData = $allProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($product->id); ?>" <?php echo e(request('product_id') == $product->id ? 'selected' : ''); ?>>
                                            <?php echo e($product->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Aina ya Huduma</label>
                            <select class="form-select" name="service_type" id="filterServiceType">
                                <option value="">Zote</option>
                                <option value="new_cylinder" <?php echo e(request('service_type') == 'new_cylinder' ? 'selected' : ''); ?>>Mtungi Mpya</option>
                                <option value="refill_exchange" <?php echo e(request('service_type') == 'refill_exchange' ? 'selected' : ''); ?>>Kubadilisha (Refill)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Wholesaler</label>
                            <select class="form-select" name="wholesaler_id" id="filterWholesaler">
                                <option value="">Wote</option>
                                <?php if(isset($wholesalersList)): ?>
                                    <?php $__currentLoopData = $wholesalersList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($w->id); ?>" <?php echo e(request('wholesaler_id') == $w->id ? 'selected' : ''); ?>>
                                            <?php echo e($w->business_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-search"></i> Chuja
                            </button>
                            <?php if(request()->anyFilled(['product_id', 'service_type', 'wholesaler_id'])): ?>
                                <a href="<?php echo e(route('retailer.procurement.browse')); ?>" class="btn btn-outline-secondary flex-grow-1">
                                    <i class="bi bi-x-circle"></i> Futa Vichujio
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        
        
        
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold"><i class="bi bi-shop me-2"></i>Wauzaji wa Jumla</h5>
                <span class="badge bg-secondary rounded-pill px-3 py-2"><?php echo e(count($wholesalers ?? [])); ?> wauzaji</span>
            </div>
            <div class="card-body">
                <?php if(isset($wholesalers) && count($wholesalers) > 0): ?>
                    <?php $__currentLoopData = $wholesalers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wholesaler): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card border mb-4 rounded-3 shadow-sm">
                        <div class="card-header bg-light border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold"><?php echo e($wholesaler['name']); ?></h6>
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt"></i> <?php echo e($wholesaler['location']); ?>

                                        <?php if(isset($wholesaler['phone']) && $wholesaler['phone']): ?>
                                            • <i class="bi bi-telephone"></i> <?php echo e($wholesaler['phone']); ?>

                                        <?php endif; ?>
                                        • <i class="bi bi-signpost"></i> Umbali: <?php echo e($wholesaler['distance'] ?? 'N/A'); ?> km
                                    </small>
                                </div>
                                <span class="badge bg-primary rounded-pill px-3 py-2">Anauza Jumla</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Bidhaa</th>
                                            <th>Bei ya Jumla</th>
                                            <th>Bei ya Rejareja</th>
                                            <th>Faida/Kipande</th>
                                            <th class="text-end pe-4">Vitendo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $wholesaler['products']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $profit = $product['retail_price'] - $product['wholesale_price'];
                                            $cartKey = $wholesaler['id'] . '-' . $product['id'];
                                            $inCart = isset($cartItems[$cartKey]);
                                        ?>
                                        <tr>
                                            <td class="ps-4 fw-medium"><?php echo e($product['name']); ?></td>
                                            <td>TZS <?php echo e(number_format($product['wholesale_price'])); ?></td>
                                            <td>TZS <?php echo e(number_format($product['retail_price'])); ?></td>
                                            <td>
                                                <span class="text-success fw-bold">
                                                    TZS <?php echo e(number_format($profit)); ?>

                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <form method="POST" action="<?php echo e(route('retailer.procurement.add')); ?>" class="d-inline-flex align-items-center gap-2">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="wholesaler_id" value="<?php echo e($wholesaler['id']); ?>">
                                                        <input type="hidden" name="product_id" value="<?php echo e($product['id']); ?>">
                                                        <input type="number" name="quantity" class="form-control form-control-sm" 
                                                               value="5" min="5" max="<?php echo e($product['available_qty'] ?? 100); ?>" 
                                                               style="width: 75px;">
                                                        <button type="submit" class="btn btn-sm btn-primary" title="Ongeza kwenye Kikapu">
                                                            <i class="bi bi-cart-plus"></i> Ongeza
                                                        </button>
                                                    </form>
                                                    
                                                    <a href="<?php echo e(route('retailer.procurement.cart')); ?>" 
                                                       class="btn btn-sm btn-success" 
                                                       title="Nenda kwenye Kikapu">
                                                        <i class="bi bi-bag-check"></i> Nunua
                                                    </a>
                                                </div>
                                                <?php if($inCart): ?>
                                                    <small class="text-success d-block mt-1">
                                                        <i class="bi bi-check-circle-fill"></i> Imo Kikapuni
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-shop display-1 text-muted"></i>
                        <h5 class="mt-3">Hakuna Wholesaler kwa Sasa</h5>
                        <p class="text-muted">Hakuna wauzaji wa jumla wanaopatikana kwa vigezo vilivyochaguliwa.</p>
                        <a href="<?php echo e(route('retailer.procurement.browse')); ?>" class="btn btn-primary mt-2">
                            <i class="bi bi-arrow-repeat"></i> Onyesha Wote
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<?php if(($cartCount ?? 0) > 0): ?>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1000;">
    <a href="<?php echo e(route('retailer.procurement.cart')); ?>" class="btn btn-primary btn-lg shadow-lg rounded-pill px-4 py-3">
        <i class="bi bi-cart-fill me-2"></i>
        Kikapu (<?php echo e($cartCount); ?>) • TZS <?php echo e(number_format($cartTotal ?? 0)); ?>

        <i class="bi bi-arrow-right ms-2"></i>
    </a>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .bg-opacity-10 { --bs-bg-opacity: 0.1; }
    
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .btn-success {
        background: linear-gradient(145deg, #198754, #157347);
        border: none;
    }
    
    .btn-success:hover {
        background: linear-gradient(145deg, #157347, #116b3a);
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views/retailer/procurement/browse.blade.php ENDPATH**/ ?>