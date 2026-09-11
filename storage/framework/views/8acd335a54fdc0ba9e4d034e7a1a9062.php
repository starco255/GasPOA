<?php $__env->startSection('title', 'Bidhaa Zangu'); ?>
<?php $__env->startSection('page-title', 'Simamia Bidhaa za Jumla'); ?>

<?php $__env->startSection('content'); ?>


<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">
        <i class="bi bi-box-seam me-2 text-primary"></i>Bidhaa Zote
        <span class="badge bg-primary rounded-pill ms-2"><?php echo e(count($products ?? [])); ?></span>
    </h5>
    <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addProductModal">
        <i class="bi bi-plus-circle me-2"></i> Ongeza Bidhaa Mpya
    </button>
</div>


<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <?php if(isset($products) && count($products) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Bidhaa</th>
                            <th class="text-center">Idadi Ghalani</th>
                            <th class="text-end">Bei ya Jumla (TZS)</th>
                            <th class="text-end">Bei Pendekezi Rejareja</th>
                            <th>Ilivyosasishwa</th>
                            <th class="text-end pe-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="ps-4 fw-medium">
                                <?php echo e($product['name']); ?>

                                <?php if(($product['service_type'] ?? 'new_cylinder') == 'new_cylinder'): ?>
                                    <span class="badge bg-warning text-dark rounded-pill ms-2">Mtungi Mpya</span>
                                <?php else: ?>
                                    <span class="badge bg-success rounded-pill ms-2">Kubadilisha</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($product['stock'] < 50): ?>
                                    <span class="text-danger fw-bold"><?php echo e($product['stock']); ?></span>
                                    <i class="bi bi-exclamation-triangle-fill text-warning ms-1" title="Stock chache"></i>
                                <?php else: ?>
                                    <?php echo e(number_format($product['stock'])); ?>

                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <span class="text-primary fw-medium">TZS <?php echo e(number_format($product['wholesale'])); ?></span>
                            </td>
                            <td class="text-end">
                                <span class="text-success fw-medium">TZS <?php echo e(number_format($product['msrp'])); ?></span>
                            </td>
                            <td><?php echo e($product['updated']); ?></td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary rounded-circle" 
                                            style="width: 36px; height: 36px; padding: 0;"
                                            data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addStockModal<?php echo e($product['id']); ?>">
                                                <i class="bi bi-plus-circle text-success me-2"></i> Ongeza Stock
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="<?php echo e(route('wholesaler.products.toggle', $product['id'])); ?>" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>
                                                <button type="submit" class="dropdown-item">
                                                    <?php if($product['is_active'] ?? true): ?>
                                                        <i class="bi bi-eye-slash text-warning me-2"></i> Zima Bidhaa
                                                    <?php else: ?>
                                                        <i class="bi bi-eye text-success me-2"></i> Washa Bidhaa
                                                    <?php endif; ?>
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>

                        
                        <div class="modal fade" id="addStockModal<?php echo e($product['id']); ?>" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-4">
                                    <form method="POST" action="<?php echo e(route('wholesaler.products.addStock', $product['id'])); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="modal-header border-0 pt-4 px-4">
                                            <h5 class="modal-title fw-bold">
                                                <i class="bi bi-box-seam me-2 text-success"></i>
                                                Ongeza Stock - <?php echo e($product['name']); ?>

                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body px-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Stock ya Sasa</label>
                                                <div class="bg-light rounded-3 p-3 text-center">
                                                    <h3 class="mb-0"><?php echo e(number_format($product['stock'])); ?></h3>
                                                    <small class="text-muted">mitungi</small>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Idadi ya Kuongeza</label>
                                                <input type="number" class="form-control form-control-lg" 
                                                       name="quantity" value="0" min="1" required>
                                            </div>
                                            <div class="alert alert-info rounded-3">
                                                <i class="bi bi-info-circle me-2"></i>
                                                Stock mpya itakuwa: <strong id="newStock<?php echo e($product['id']); ?>"><?php echo e(number_format($product['stock'])); ?></strong>
                                            </div>
                                            
                                            
                                            <div class="bg-light rounded-3 p-3 mt-3">
                                                <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2 text-primary"></i>Bei Zilizowekwa na Admin</h6>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <small class="text-muted">Bei ya Jumla:</small><br>
                                                        <strong class="text-primary">TZS <?php echo e(number_format($product['wholesale'])); ?></strong>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">Bei ya Rejareja:</small><br>
                                                        <strong class="text-success">TZS <?php echo e(number_format($product['msrp'])); ?></strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pb-4 px-4">
                                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Ghairi</button>
                                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                                <i class="bi bi-plus-lg me-2"></i> Ongeza Stock
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="empty-state-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <h5 class="mt-3 fw-bold">Hakuna Bidhaa</h5>
                <p class="text-muted">Bado hujaongeza bidhaa zozote kwenye ghala lako.</p>
                <button class="btn btn-primary rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="bi bi-plus-circle me-2"></i> Ongeza Bidhaa Mpya
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>


<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST" action="<?php echo e(route('wholesaler.products.store')); ?>" id="addProductForm">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="service_type" value="new_cylinder">
                <input type="hidden" name="is_active" value="1">
                <input type="hidden" name="name" id="productNameHidden">
                
                <div class="modal-header border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle me-2 text-primary"></i>
                        Ongeza Bidhaa Mpya (Mtungi Mpya)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                   <!-- 
                    <div class="alert alert-info rounded-3 mb-4">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <strong>Kumbuka:</strong> Bei za bidhaa zinawekwa na Admin. Chagua Uzito unaopatikana kuona bei husika.
                    </div>  -->
                    
                    <div class="row g-4">
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-tag me-1"></i> Chapa / Brand <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" name="brand_name" id="brandName" required>
                                <option value="">-- Chagua Chapa --</option>
                                <?php if(isset($brands) && count($brands) > 0): ?>
                                    <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($brand); ?>"><?php echo e($brand); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Chagua chapa ya bidhaa kutoka kwenye orodha.</small>
                        </div>
                        
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-weight me-1"></i> Uzito (kg) <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" name="weight_kg" id="weightKg" required>
                                <option value="">-- Chagua Uzito --</option>
                                <?php if(isset($availableWeights) && count($availableWeights) > 0): ?>
                                    <?php $__currentLoopData = $availableWeights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $weight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($weight); ?>"><?php echo e($weight); ?> kg</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Uzito unaopatikana kutoka kwenye bidhaa zilizowekwa na Admin.</small>
                        </div>
                    </div>
                    
                    
                    <div class="product-name-header mt-4" id="productNameHeader" style="display: none;">
                        <div class="alert alert-success rounded-3 mb-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                                <div>
                                    <span id="productNamePreview" class="fs-5 fw-bold text-primary">--</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="row g-4">
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-box-seam me-1"></i> Idadi ya Kuanzia (Stock) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="number" class="form-control" name="quantity" value="0" min="0" required>
                                <span class="input-group-text">mitungi</span>
                            </div>
                            <small class="text-muted">Idadi ya mitungi inayoanza kwenye ghala.</small>
                        </div>
                        
                        
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-tag me-1"></i> Bei Zinazopendekezwa
                                <span class="badge bg-info ms-2">Admin Only</span>
                            </label>
                            <div class="bg-light rounded-3 p-3" id="recommendedPrices">
                                <div class="text-center text-muted py-3" id="pricePlaceholder">
                                    <i class="bi bi-arrow-up-circle fs-4 d-block mb-2"></i>
                                    Chagua Chapa na Uzito kuona bei
                                </div>
                                <div class="d-none" id="priceDetails">
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">Bei ya Jumla:</small>
                                        <strong class="text-primary" id="displayWholesale">TZS 0</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">Bei ya Rejareja:</small>
                                        <strong class="text-success" id="displayRetail">TZS 0</strong>
                                    </div>
                                </div>
                                <input type="hidden" name="wholesale_price" id="hiddenWholesale" value="0">
                                <input type="hidden" name="retail_price" id="hiddenRetail" value="0">
                            </div>
                            <small class="text-muted">Bei hizi zimewekwa na Admin. Hawezi kuzibadilisha.</small>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Ghairi
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-check-lg me-2"></i> Hifadhi Bidhaa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .bg-opacity-10 { --bs-bg-opacity: 0.1; }
    
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
    
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #fafbfc;
    }
    
    .badge.bg-warning {
        background-color: #fff3cd !important;
        color: #856404 !important;
    }
    
    .badge.bg-success {
        background-color: #d1e7dd !important;
        color: #0f5132 !important;
    }
    
    .dropdown-menu {
        min-width: 200px;
        padding: 8px 0;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .dropdown-item {
        padding: 10px 20px;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
        padding-left: 1.5rem;
    }
    
    .product-name-header {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Read-only price display */
    .bg-light.rounded-3.p-3 {
        border: 2px solid #e9ecef;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // =====================================================
        // ALL PRODUCTS DATA FROM DATABASE
        // =====================================================
        const allProducts = <?php echo json_encode($allProducts ?? [], 15, 512) ?>;
        
        // =====================================================
        // DOM ELEMENTS
        // =====================================================
        const brandSelect = document.getElementById('brandName');
        const weightSelect = document.getElementById('weightKg');
        const productNamePreview = document.getElementById('productNamePreview');
        const productNameHidden = document.getElementById('productNameHidden');
        const productNameHeader = document.getElementById('productNameHeader');
        
        const pricePlaceholder = document.getElementById('pricePlaceholder');
        const priceDetails = document.getElementById('priceDetails');
        const displayWholesale = document.getElementById('displayWholesale');
        const displayRetail = document.getElementById('displayRetail');
        const hiddenWholesale = document.getElementById('hiddenWholesale');
        const hiddenRetail = document.getElementById('hiddenRetail');
        
        // =====================================================
        // AUTO-GENERATE PRODUCT NAME (Brand + Weight)
        // =====================================================
        function updateProductName() {
            const brand = brandSelect ? brandSelect.value : '';
            const weight = weightSelect ? weightSelect.value : '';
            
            if (brand && weight) {
                const productName = brand + ' ' + parseFloat(weight) + 'kg';
                if (productNamePreview) productNamePreview.textContent = productName;
                if (productNameHidden) productNameHidden.value = productName;
                if (productNameHeader) productNameHeader.style.display = 'block';
            } else {
                if (productNameHeader) productNameHeader.style.display = 'none';
                if (productNameHidden) productNameHidden.value = '';
            }
            
            // Also update prices when product name changes
            updatePrices();
        }
        
        // =====================================================
        // FETCH PRODUCT PRICES BASED ON BRAND + WEIGHT
        // =====================================================
        function updatePrices() {
            const brand = brandSelect?.value || '';
            const weight = parseFloat(weightSelect?.value) || 0;
            
            if (brand && weight > 0) {
                // Find matching product from allProducts array
                const matchedProduct = allProducts.find(p => {
                    const productName = (p.name || '').toLowerCase();
                    const brandLower = brand.toLowerCase();
                    const weightMatch = Math.abs((p.weight_kg || 0) - weight) < 0.01;
                    return productName.includes(brandLower) && weightMatch;
                });
                
                if (matchedProduct) {
                    // ✅ SHOW PRICES
                    if (pricePlaceholder) pricePlaceholder.classList.add('d-none');
                    if (priceDetails) priceDetails.classList.remove('d-none');
                    
                    const wholesale = parseInt(matchedProduct.suggested_wholesale_price) || 0;
                    const retail = parseInt(matchedProduct.suggested_retail_price) || 0;
                    
                    if (displayWholesale) displayWholesale.textContent = 'TZS ' + wholesale.toLocaleString();
                    if (displayRetail) displayRetail.textContent = 'TZS ' + retail.toLocaleString();
                    if (hiddenWholesale) hiddenWholesale.value = wholesale;
                    if (hiddenRetail) hiddenRetail.value = retail;
                } else {
                    // ❌ NO MATCH - show info message
                    if (pricePlaceholder) {
                        pricePlaceholder.classList.remove('d-none');
                        pricePlaceholder.innerHTML = '<i class="bi bi-info-circle fs-4 d-block mb-2"></i> Hakuna Ujazo huo (Kg) kwa bidhaa hii.... Jaribu Ujazo (Kg) tofauti.';
                    }
                    if (priceDetails) priceDetails.classList.add('d-none');
                    if (hiddenWholesale) hiddenWholesale.value = 0;
                    if (hiddenRetail) hiddenRetail.value = 0;
                }
            } else {
                // 🔄 RESET TO PLACEHOLDER
                if (pricePlaceholder) {
                    pricePlaceholder.classList.remove('d-none');
                    pricePlaceholder.innerHTML = '<i class="bi bi-arrow-up-circle fs-4 d-block mb-2"></i> Chagua Chapa na Uzito kuona bei';
                }
                if (priceDetails) priceDetails.classList.add('d-none');
                if (hiddenWholesale) hiddenWholesale.value = 0;
                if (hiddenRetail) hiddenRetail.value = 0;
            }
        }
        
        // =====================================================
        // EVENT LISTENERS
        // =====================================================
        if (brandSelect) {
            brandSelect.addEventListener('change', function() {
                updateProductName();
                updatePrices();
            });
        }
        
        if (weightSelect) {
            weightSelect.addEventListener('change', function() {
                updateProductName();
                updatePrices();
            });
        }
        
        // =====================================================
        // INITIALIZE
        // =====================================================
        updateProductName();
        updatePrices();
        
        // =====================================================
        // CALCULATE NEW STOCK PREVIEW (Add Stock Modal)
        // =====================================================
        document.querySelectorAll('[id^="addStockModal"]').forEach(modal => {
            const productId = modal.id.replace('addStockModal', '');
            const stockInput = modal.querySelector('input[name="quantity"]');
            const newStockSpan = document.getElementById('newStock' + productId);
            const currentStock = parseInt(newStockSpan?.textContent.replace(/,/g, '')) || 0;
            
            if (stockInput && newStockSpan) {
                stockInput.addEventListener('input', function() {
                    const addQty = parseInt(this.value) || 0;
                    newStockSpan.textContent = (currentStock + addQty).toLocaleString();
                });
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\wholesaler\products\index.blade.php ENDPATH**/ ?>