<?php $__env->startSection('title', 'Mipangilio ya Bei'); ?>
<?php $__env->startSection('page-title', 'Mipangilio ya Bei za Bidhaa'); ?>

<?php $__env->startSection('content'); ?>


<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="bg-success bg-opacity-25 rounded-circle p-2 me-3">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
            </div>
            <div class="flex-grow-1"><?php echo e(session('success')); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="bg-danger bg-opacity-25 rounded-circle p-2 me-3">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
            </div>
            <div class="flex-grow-1"><?php echo e(session('error')); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php endif; ?>




<div class="row g-3 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-4 bg-primary d-flex align-items-center justify-content-center">
                        <i class="bi bi-box-seam text-white" style="font-size: 2.5rem;"></i>
                    </div>
                    <div class="col-8 p-3">
                        <h6 class="text-muted mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Jumla ya Bidhaa</h6>
                        <h3 class="fw-bold mb-1" style="font-size: 1.8rem;"><?php echo e(count($products ?? [])); ?></h3>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2">
                                <i class="bi bi-database me-1"></i> Bidhaa zote
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-4 bg-warning d-flex align-items-center justify-content-center">
                        <i class="bi bi-cart-plus text-white" style="font-size: 2.5rem;"></i>
                    </div>
                    <div class="col-8 p-3">
                        <h6 class="text-muted mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Mitungi Mipya</h6>
                        <h3 class="fw-bold mb-1" style="font-size: 1.8rem;"><?php echo e($products->where('service_type', 'new_cylinder')->count() ?? 0); ?></h3>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2">
                                <i class="bi bi-plus-circle me-1"></i> New Cylinder
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-4 bg-success d-flex align-items-center justify-content-center">
                        <i class="bi bi-arrow-repeat text-white" style="font-size: 2.5rem;"></i>
                    </div>
                    <div class="col-8 p-3">
                        <h6 class="text-muted mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Kubadilisha (Refill)</h6>
                        <h3 class="fw-bold mb-1" style="font-size: 1.8rem;"><?php echo e($products->where('service_type', 'refill_exchange')->count() ?? 0); ?></h3>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">
                                <i class="bi bi-arrow-left-right me-1"></i> Refill Exchange
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                    <i class="bi bi-tags fs-5 text-primary"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">Bei za Bidhaa (MSRP)</h5>
                    <p class="text-muted small mb-0">Weka bei pendekezwa kwa wauzaji wote • Scroll down kuona bidhaa zote</p>
                </div>
            </div>


    <div class="d-flex align-items-center gap-2">
           
              <button class="btn btn-outline-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                 <i class="bi bi-building-add me-2"></i> Chapa Mpya
              </button>
           
              <button class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addProductModal">
                  <i class="bi bi-plus-circle me-2"></i> Sajili Bidhaa
               </button>
             <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
             <?php echo e(count($products ?? [])); ?> Bidhaa
            </span>
        </div>


        </div>
    </div>
    
    
    <div class="card-body px-0 pb-0">
        <form method="POST" action="<?php echo e(route('admin.pricing.update.products')); ?>" id="productsForm">
            <?php echo csrf_field(); ?>
            
            <div class="table-scrollable" style="max-height: 500px; overflow-y: auto; border-top: 1px solid #e9ecef; border-bottom: 1px solid #e9ecef;">
                <table class="table table-hover align-middle mb-0">
                    <thead style="position: sticky; top: 0; z-index: 10; background: #f8f9fa;">
                        <tr>
                            <th style="width: 30%; padding-left: 1.5rem;">Bidhaa</th>
                            <th style="width: 15%;" class="text-center">Aina</th>
                            <th style="width: 25%;">Bei ya Jumla</th>
                            <th style="width: 25%; padding-right: 1.5rem;">Bei ya Rejareja</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $products ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td style="padding-left: 1.5rem;">
                                <div class="d-flex align-items-center">
                                    <div class="product-icon bg-light rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; flex-shrink: 0;">
                                        <i class="bi bi-box-seam text-primary"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block" style="font-size: 0.9rem;"><?php echo e($product->name); ?></strong>
                                        <small class="text-muted"><?php echo e($product->weight_kg ?? 'N/A'); ?> kg</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <?php if($product->service_type == 'new_cylinder'): ?>
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 rounded-pill" style="font-size: 0.75rem;">Mtungi Mpya</span>
                                <?php else: ?>
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill" style="font-size: 0.75rem;">Kubadilisha</span>
                                <?php endif; ?>
                            </td>
                            <td style="min-width: 180px;">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted fw-medium" style="font-size: 0.8rem; min-width: 42px;">TZS</span>
                                    <input type="number" 
                                           class="form-control border-0 bg-light text-end fw-bold" 
                                           name="wholesale[<?php echo e($product->id); ?>]" 
                                           value="<?php echo e(old('wholesale.'.$product->id, $product->suggested_wholesale_price)); ?>" 
                                           min="0" step="100" 
                                           style="font-size: 0.9rem; padding: 0.5rem 0.75rem; min-width: 110px; letter-spacing: 0.3px;">
                                </div>
                            </td>
                            <td style="min-width: 180px; padding-right: 1.5rem;">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted fw-medium" style="font-size: 0.8rem; min-width: 42px;">TZS</span>
                                    <input type="number" 
                                           class="form-control border-0 bg-light text-end fw-bold" 
                                           name="retail[<?php echo e($product->id); ?>]" 
                                           value="<?php echo e(old('retail.'.$product->id, $product->suggested_retail_price)); ?>" 
                                           min="0" step="100" 
                                           style="font-size: 0.9rem; padding: 0.5rem 0.75rem; min-width: 110px; letter-spacing: 0.3px;">
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4">
                                <div class="text-center py-5">
                                    <div class="empty-state-icon bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                    </div>
                                    <h6 class="text-muted">Hakuna Bidhaa</h6>
                                    <p class="text-muted small">Bidhaa zitaonekana hapa baada ya kuongezwa.</p>
                                    <button class="btn btn-primary rounded-pill mt-2" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                        <i class="bi bi-plus-circle me-2"></i> Ongeza Bidhaa ya Kwanza
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
      
      <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-white border-top">
       <div>
        <small class="text-muted">
            <i class="bi bi-info-circle me-1"></i> 
            <span class="d-none d-md-inline">Bidhaa zote zipo hapa </span>
            Bonyeza kuhifadhi baada ya mabadiliko.
        </small>
        </div>
        <div class="d-flex gap-2">
        <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-outline-primary px-4 py-2 rounded-pill">
            <i class="bi bi-grid me-2"></i> Bidhaa Zote
        </a>
        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill">
            <i class="bi bi-check-lg me-2"></i> Hifadhi Bei Zote
        </button>
     </div>
     </div>

        </form>
    </div>
</div>




<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST" action="<?php echo e(route('admin.products.store')); ?>" id="addProductForm">
                <?php echo csrf_field(); ?>
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle me-2 text-success"></i>
                        Sajili Bidhaa Mpya
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="row g-4">
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-diagram-3 me-1"></i> Aina ya Huduma <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" name="service_type" required>
                                <option value="">-- Chagua Aina --</option>
                                <option value="new_cylinder">Mtungi Mpya (New Cylinder)</option>
                                <option value="refill_exchange">Kubadilisha (Refill Exchange)</option>
                            </select>
                        </div>
                        
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-tag me-1"></i> Chapa / Brand <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" name="brand_name" id="adminBrandName" required>
                                <option value="">-- Chagua Chapa --</option>
                                <?php if(isset($brands) && count($brands) > 0): ?>
                                    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($brand); ?>"><?php echo e($brand); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Chagua chapa iliyopo au andika mpya.</small>
                        </div>
                        
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-weight me-1"></i> Uzito (kg) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control form-control-lg" name="weight_kg" 
                                   placeholder="Mf: 14Kg" min="1" step="0.1" required>
                        </div>
                        
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-cash-stack me-1"></i> Bei ya Jumla (TZS) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">TZS</span>
                                <input type="number" class="form-control" name="suggested_wholesale_price" 
                                       placeholder="Mf: 45000" min="0" step="100" required>
                            </div>
                            <small class="text-muted">Bei kwa wauzaji wa jumla.</small>
                        </div>
                        
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-shop me-1"></i> Bei ya Rejareja (TZS) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">TZS</span>
                                <input type="number" class="form-control" name="suggested_retail_price" 
                                       placeholder="Mf: 55000" min="0" step="100" required>
                            </div>
                            <small class="text-muted">Bei pendekezwa kwa wauzaji rejareja.</small>
                        </div>
                        
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-pencil me-1"></i> Maelezo (Hiari)
                            </label>
                            <textarea class="form-control" name="description" rows="2" 
                                      placeholder="Maelezo mafupi kuhusu bidhaa..."></textarea>
                        </div>
                    </div>
                    
                    
                    <div id="adminProductNamePreview" class="mt-4" style="display: none;">
                        <div class="alert alert-info rounded-3 mb-0">
                            <i class="bi bi-eye me-2"></i>
                            <strong>Jina la Bidhaa:</strong> <span id="adminProductNameText" class="fw-bold">--</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Ghairi
                    </button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="bi bi-check-lg me-2"></i> Sajili Bidhaa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>





<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST" action="<?php echo e(route('admin.categories.store')); ?>" id="addCategoryForm">
                <?php echo csrf_field(); ?>
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-building-add me-2 text-primary"></i>
                        Sajili Chapa Mpya (Brand)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-tag me-1"></i> Jina la Chapa / Brand <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-lg" name="name" 
                               placeholder="Mf: Oryx, Meko, Manzi, Total" required>
                        <small class="text-muted">Weka jina la chapa kama linavyojulikana sokoni.</small>
                    </div>
                    
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-pencil me-1"></i> Maelezo (Hiari)
                        </label>
                        <textarea class="form-control" name="description" rows="2" 
                                  placeholder="Maelezo mafupi kuhusu chapa hii..."></textarea>
                    </div>
                    
                    
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="isActiveCategory" name="is_active" value="1" checked>
                        <label class="form-check-label" for="isActiveCategory">
                            <strong>Chapa iko active?</strong> (Itaonekana kwenye orodha mara moja)
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Ghairi
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-check-lg me-2"></i> Sajili Chapa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="card border-0 shadow-sm rounded-4 mt-4" style="background: linear-gradient(135deg, #e8f0fe 0%, #d4e4fc 100%);">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-white rounded-circle p-2 me-3 shadow-sm">
                <i class="bi bi-lightbulb-fill text-warning fs-5"></i>
            </div>
            <h6 class="fw-bold mb-0">Vidokezo vya Mipangilio ya Bei</h6>
        </div>
        <ul class="mb-0 ps-3 small">
            <li class="mb-2">Bei za mwongozo zinaonekana kwa wauzaji wote kwenye mfumo kama bei pendekezwa.</li>
            <li class="mb-2">Wauzaji wanaweza kuweka bei zao tofauti kwa bidhaa zao kwenye maduka yao.</li>
            <li class="mb-2">Mabadiliko ya bei yanaathiri bidhaa zote zinazofanana kwenye mfumo.</li>
            <li>Hakikisha umebonyeza <strong>"Hifadhi Bei Zote"</strong> baada ya kila mabadiliko.</li>
        </ul>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirm before saving prices
        const productsForm = document.getElementById('productsForm');
        if (productsForm) {
            productsForm.addEventListener('submit', function(e) {
                if (!confirm('Una uhakika unataka kuhifadhi bei zote za bidhaa?')) {
                    e.preventDefault();
                    return false;
                }
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Inahifadhi...';
                }
            });
        }
        
        // Add visual feedback when input changes
        document.querySelectorAll('input[type="number"]').forEach(input => {
            const originalValue = input.value;
            input.addEventListener('change', function() {
                if (this.value !== originalValue) {
                    this.style.backgroundColor = '#fff8e1';
                    setTimeout(() => { this.style.backgroundColor = ''; }, 1500);
                }
            });
        });
        
        // ✅ AUTO-GENERATE PRODUCT NAME PREVIEW (Admin Add Product Modal)
        const adminBrandSelect = document.getElementById('adminBrandName');
        const adminWeightInput = document.querySelector('#addProductModal input[name="weight_kg"]');
        const adminServiceTypeSelect = document.querySelector('#addProductModal select[name="service_type"]');
        const adminProductNamePreview = document.getElementById('adminProductNamePreview');
        const adminProductNameText = document.getElementById('adminProductNameText');
        
        function updateAdminProductName() {
            const brand = adminBrandSelect?.value || '';
            const weight = adminWeightInput?.value || '';
            const serviceType = adminServiceTypeSelect?.value || '';
            
            if (brand && weight) {
                let suffix = serviceType === 'refill_exchange' ? ' Refill' : '';
                const productName = brand + ' ' + parseFloat(weight) + 'kg' + suffix;
                if (adminProductNameText) adminProductNameText.textContent = productName;
                if (adminProductNamePreview) adminProductNamePreview.style.display = 'block';
            } else {
                if (adminProductNamePreview) adminProductNamePreview.style.display = 'none';
            }
        }
        
        if (adminBrandSelect) adminBrandSelect.addEventListener('change', updateAdminProductName);
        if (adminWeightInput) adminWeightInput.addEventListener('input', updateAdminProductName);
        if (adminServiceTypeSelect) adminServiceTypeSelect.addEventListener('change', updateAdminProductName);
    });

    // Handle Add Category Form Submission
const addCategoryForm = document.getElementById('addCategoryForm');
if (addCategoryForm) {
    addCategoryForm.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Inahifadhi...';
        }
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .card { transition: all 0.3s ease; border: 1px solid rgba(0,0,0,0.03); }
    .card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important; }
    .table thead th {
        font-weight: 600; text-transform: uppercase; font-size: 0.7rem;
        letter-spacing: 1px; color: #f8f8f8; border-bottom: 2px solid #e2e8f0;
        padding: 0.85rem 0.75rem;
    }
    .table tbody tr { transition: background-color 0.2s ease; }
    .table tbody tr:hover { background-color: #f8fafc; }
    .table td { padding: 0.75rem; vertical-align: middle; }
    .table-scrollable { scrollbar-width: thin; scrollbar-color: #c1c1c1 #f1f1f1; }
    .table-scrollable::-webkit-scrollbar { width: 8px; }
    .table-scrollable::-webkit-scrollbar-track { background: #f8f9fa; border-radius: 10px; }
    .table-scrollable::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #4a6cf7, #3651d5); border-radius: 10px; }
    .table thead { position: sticky; top: 0; z-index: 10; }
    .product-icon { background: linear-gradient(135deg, #e8f0fe 0%, #d4e4fc 100%) !important; flex-shrink: 0; }
    .input-group .form-control { font-size: 0.9rem !important; padding: 0.5rem 0.75rem !important; min-width: 110px !important; transition: all 0.3s ease; }
    .input-group .form-control:focus { box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.15); border-color: #4a6cf7; background-color: #fff !important; }
    .btn { font-weight: 500; transition: all 0.2s ease; }
    .btn-primary { background: linear-gradient(135deg, #4a6cf7 0%, #3651d5 100%); border: none; }
    .btn-primary:hover { background: linear-gradient(135deg, #3651d5 0%, #2a40b0 100%); transform: translateY(-1px); box-shadow: 0 5px 15px rgba(74, 108, 247, 0.3); }
    .btn-success { background: linear-gradient(135deg, #28a745 0%, #0f6b25 100%); border: none; color: white; }
    .btn-success:hover { background: linear-gradient(135deg, #1e7e34 0%, #0f5721 100%); transform: translateY(-1px); box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3); }
    .rounded-pill { border-radius: 50px !important; }
    .badge { font-weight: 500; }
    @media (max-width: 768px) {
        .table-scrollable { max-height: 400px !important; }
        .table td, .table th { padding: 0.5rem 0.25rem; }
        .input-group .form-control { min-width: 90px !important; font-size: 0.8rem !important; }
    }
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\admin\pricing\index.blade.php ENDPATH**/ ?>