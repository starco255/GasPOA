<?php $__env->startSection('title', 'Hariri Bidhaa'); ?>
<?php $__env->startSection('page-title', 'Hariri Bidhaa'); ?>

<?php $__env->startSection('content'); ?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold">
                    <i class="bi bi-pencil me-2 text-primary"></i> Hariri Bidhaa
                </h5>
                <p class="text-muted">Badilisha maelezo ya bidhaa.</p>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('admin.products.update', $product->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    
                    <div class="row g-4">
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-diagram-3 me-1"></i> Aina ya Huduma <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" name="service_type" required>
                                <option value="new_cylinder" <?php echo e($product->service_type == 'new_cylinder' ? 'selected' : ''); ?>>
                                    Mtungi Mpya (New Cylinder)
                                </option>
                                <option value="refill_exchange" <?php echo e($product->service_type == 'refill_exchange' ? 'selected' : ''); ?>>
                                    Kubadilisha (Refill Exchange)
                                </option>
                            </select>
                        </div>
                        
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-tag me-1"></i> Chapa / Brand <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" name="brand_name" required>
                                <?php if(isset($brands) && count($brands) > 0): ?>
                                    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($brand); ?>" <?php echo e($product->category->name == $brand ? 'selected' : ''); ?>>
                                            <?php echo e($brand); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-weight me-1"></i> Uzito (kg) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control form-control-lg" name="weight_kg" 
                                   value="<?php echo e(old('weight_kg', $product->weight_kg)); ?>" min="1" step="0.1" required>
                        </div>
                        
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-cash-stack me-1"></i> Bei ya Jumla (TZS) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">TZS</span>
                                <input type="number" class="form-control" name="suggested_wholesale_price" 
                                       value="<?php echo e(old('suggested_wholesale_price', $product->suggested_wholesale_price)); ?>" 
                                       min="0" step="100" required>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-shop me-1"></i> Bei ya Rejareja (TZS) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">TZS</span>
                                <input type="number" class="form-control" name="suggested_retail_price" 
                                       value="<?php echo e(old('suggested_retail_price', $product->suggested_retail_price)); ?>" 
                                       min="0" step="100" required>
                            </div>
                        </div>
                        
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-pencil me-1"></i> Maelezo (Hiari)
                            </label>
                            <textarea class="form-control" name="description" rows="2"><?php echo e(old('description', $product->description)); ?></textarea>
                        </div>
                        
                        
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                       <?php echo e(old('is_active', $product->is_active) ? 'checked' : ''); ?>>
                                <label class="form-check-label">
                                    <strong>Bidhaa iko active?</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <a href="<?php echo e(route('admin.products.index', '#products')); ?>" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-x-circle me-1"></i> Ghairi
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-check-lg me-2"></i> Hifadhi Mabadiliko
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\admin\products\edit.blade.php ENDPATH**/ ?>