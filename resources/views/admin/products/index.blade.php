@extends('layouts.admin')

@section('title', 'Bidhaa Zote')
@section('page-title', 'Usimamizi wa Bidhaa')

@section('content')

{{-- Alert Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="bg-success bg-opacity-25 rounded-circle p-2 me-3">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
            </div>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <div class="bg-danger bg-opacity-25 rounded-circle p-2 me-3">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
            </div>
            <div class="flex-grow-1">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
@endif

{{-- Header Actions --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.pricing.index') }}" class="btn btn-outline-secondary rounded-pill px-3 me-2">
            <i class="bi bi-arrow-left me-1"></i> Rudi kwenye Bei
        </a>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-building-add me-2"></i> Chapa Mpya
        </button>
        <button class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-circle me-2"></i> Sajili Bidhaa
        </button>
    </div>
</div>

{{-- Tabs Navigation --}}
<ul class="nav nav-tabs mb-4" id="productTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories" type="button" role="tab">
            <i class="bi bi-building me-1"></i> Chapa za Bidhaa
            <span class="badge bg-primary ms-1">{{ count($categories ?? []) }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab">
            <i class="bi bi-box-seam me-1"></i> Bidhaa Zilizosajiliwa
            <span class="badge bg-success ms-1">{{ count($products ?? []) }}</span>
        </button>
    </li>
</ul>

{{-- Tab Content --}}
<div class="tab-content" id="productTabsContent">
    
    {{-- ===================================================== --}}
    {{-- TAB 1: CHAPA ZA BIDHAA (CATEGORIES) --}}
    {{-- ===================================================== --}}
    <div class="tab-pane fade show active" id="categories" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Jina la Chapa</th>
                                <th>Maelezo</th>
                                <th class="text-center">Idadi ya Bidhaa</th>
                                <th class="text-center">Hali</th>
                                <th class="text-end pe-4">Vitendo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories ?? [] as $category)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $category->id }}</td>
                                <td>
                                    <strong>{{ $category->name }}</strong>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $category->description ?? 'Hakuna maelezo' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill">{{ $category->products_count ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    @if($category->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                             class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                             <i class="bi bi-pencil me-1"></i> Hariri
                                           </a>   
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" 
                                              onsubmit="return confirm('Una uhakika unataka kufuta chapa hii?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                <i class="bi bi-trash me-1"></i> Ondoa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-building fs-1 d-block mb-2 opacity-50"></i>
                                    Hakuna chapa zilizosajiliwa.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    {{-- ===================================================== --}}
    {{-- TAB 2: BIDHAA ZILIZOSAJILIWA (PRODUCTS) --}}
    {{-- ===================================================== --}}
    <div class="tab-pane fade" id="products" role="tabpanel">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Bidhaa</th>
                                <th>Chapa</th>
                                <th class="text-center">Aina</th>
                                <th class="text-end">Bei ya Jumla</th>
                                <th class="text-end">Bei ya Rejareja</th>
                                <th class="text-center">Hali</th>
                                <th class="text-end pe-4">Vitendo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products ?? [] as $product)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $product->id }}</td>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $product->weight_kg ?? 'N/A' }} kg</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $product->category->name ?? 'N/A' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($product->service_type == 'new_cylinder')
                                        <span class="badge bg-warning text-dark">Mtungi Mpya</span>
                                    @else
                                        <span class="badge bg-success">Kubadilisha</span>
                                    @endif
                                </td>
                                <td class="text-end">TZS {{ number_format($product->suggested_wholesale_price ?? 0) }}</td>
                                <td class="text-end">TZS {{ number_format($product->suggested_retail_price ?? 0) }}</td>
                                <td class="text-center">
                                    @if($product->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" 
                                           class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-pencil me-1"></i> Hariri
                                        </a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" 
                                              onsubmit="return confirm('Una uhakika unataka kufuta bidhaa hii?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                <i class="bi bi-trash me-1"></i> Ondoa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-50"></i>
                                    Hakuna bidhaa zilizosajiliwa.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===================================================== --}}
{{-- ADD PRODUCT MODAL --}}
{{-- ===================================================== --}}
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST" action="{{ route('admin.products.store') }}" id="addProductForm">
                @csrf
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
                                @if(isset($brands) && count($brands) > 0)
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand }}">{{ $brand }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-weight me-1"></i> Uzito (kg) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control form-control-lg" name="weight_kg" 
                                   placeholder="Mf: 15" min="1" step="0.1" required>
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
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Ghairi</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">
                        <i class="bi bi-check-lg me-2"></i> Sajili Bidhaa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===================================================== --}}
{{-- ADD CATEGORY/BRAND MODAL --}}
{{-- ===================================================== --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST" action="{{ route('admin.categories.store') }}" id="addCategoryForm">
                @csrf
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
                            <strong>Chapa iko active?</strong>
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Ghairi</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-check-lg me-2"></i> Sajili Chapa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-generate product name preview
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
        
        // Handle Add Product Form Submission
        const addProductForm = document.getElementById('addProductForm');
        if (addProductForm) {
            addProductForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Inahifadhi...';
                }
            });
        }
        
        // Keep tab state
        const hash = window.location.hash;
        if (hash) {
            const tab = document.querySelector(`button[data-bs-target="${hash}"]`);
            if (tab) new bootstrap.Tab(tab).show();
        }
    });
</script>
@endpush

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        padding: 0.75rem 1.5rem;
    }
    .nav-tabs .nav-link:hover { color: #0d6efd; border: none; }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: transparent;
        border-bottom: 3px solid #0d6efd;
    }
    .card { transition: all 0.3s ease; }
    .table tbody tr { transition: background-color 0.2s ease; }
    .table tbody tr:hover { background-color: #f8fafc; }
    .btn { font-weight: 500; transition: all 0.2s ease; }
    .rounded-pill { border-radius: 50px !important; }
</style>
@endpush