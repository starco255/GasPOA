<?php $__env->startSection('title', 'Mipangilio ya Ghala'); ?>
<?php $__env->startSection('page-title', 'Mipangilio ya Ghala Langu'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8 mx-auto">
        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-building me-2 text-primary"></i>Maelezo ya Ghala</h5>
                <p class="text-muted">Weka taarifa sahihi za ghala lako ili wateja wakuone.</p>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('wholesaler.settings.update')); ?>" id="warehouseSettingsForm">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    
                    <div class="mb-4">
                        <label for="business_name" class="form-label fw-semibold">
                            <i class="bi bi-building me-1"></i> Jina la Biashara / Ghala
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-lg" id="business_name" name="business_name" 
                              value="<?php echo e(old('business_name', $businessProfile->business_name ?? Auth::user()->full_name ?? '')); ?>" 
                              placeholder="Mf: Gesi Link Wholesale Ltd">
                    </div>

                    
                    <div class="mb-4">
                        <label for="tinn_number" class="form-label fw-semibold">
                            <i class="bi bi-file-text me-1"></i> Namba ya TIN (TRA)
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="tinn_number" name="tinn_number" 
                               value="<?php echo e(old('tinn_number', $businessProfile->tinn_number ?? '')); ?>" 
                               placeholder="Mf: 123-456-789" required>
                        <small class="text-muted">
                            <i class="bi bi-shield-check"></i> Namba ya TIN inahitajika kwa ajili ya utambulisho wa biashara.
                        </small>
                    </div>

                    
                    <div class="mb-4">
                        <label for="physical_address" class="form-label fw-semibold">
                            <i class="bi bi-geo-alt me-1"></i> Anwani Kamili ya Ghala
                            <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="physical_address" name="physical_address" 
                                  rows="3" placeholder="Mf: Kariakoo, Ilala, Dar es Salaam" 
                                  required><?php echo e(old('physical_address', $businessProfile->physical_address ?? '')); ?></textarea>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-credit-card me-2 text-success"></i>Njia za Malipo</h5>
                <p class="text-muted">Washa njia unazokubali na ujaze namba au akaunti yake. Njia isiyo na taarifa kamili haitaonyeshwa kwa retailer.</p>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" id="accept_cash" name="accept_cash" value="1" form="warehouseSettingsForm" <?php echo e(old('accept_cash', $businessProfile->accept_cash ?? false) ? 'checked' : ''); ?>><label class="form-check-label fw-semibold" for="accept_cash">Pesa Taslimu</label></div>
                <?php ($paymentOptions = ['accept_mpesa' => ['M-Pesa', 'mpesa_number'], 'accept_tigopesa' => ['TigoPesa / Mixx by Yas', 'mixx_number'], 'accept_airtelmoney' => ['Airtel Money', 'airtel_number'], 'accept_halopesa' => ['HaloPesa', 'halopesa_number']]); ?>
                <?php $__currentLoopData = $paymentOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $toggle => [$label, $field]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="form-check form-switch"><input class="form-check-input payment-toggle" type="checkbox" id="<?php echo e($toggle); ?>" name="<?php echo e($toggle); ?>" value="1" data-target="<?php echo e($toggle); ?>-details" form="warehouseSettingsForm" <?php echo e(old($toggle, $businessProfile->$toggle ?? false) ? 'checked' : ''); ?>><label class="form-check-label fw-semibold" for="<?php echo e($toggle); ?>"><?php echo e($label); ?></label></div>
                        <div id="<?php echo e($toggle); ?>-details" class="payment-details mt-3"><label class="form-label" for="<?php echo e($field); ?>">Lipa Namba</label><input type="tel" class="form-control" id="<?php echo e($field); ?>" name="<?php echo e($field); ?>" form="warehouseSettingsForm" value="<?php echo e(old($field, $businessProfile->$field ?? '')); ?>" placeholder="Mfano: 2557XXXXXXXX"><small class="text-muted">Retailer ataona namba hii baada ya kuchagua njia hii.</small></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="border rounded-3 p-3">
                    <div class="form-check form-switch"><input class="form-check-input payment-toggle" type="checkbox" id="accept_bank" name="accept_bank" value="1" data-target="accept_bank-details" form="warehouseSettingsForm" <?php echo e(old('accept_bank', $businessProfile->accept_bank ?? false) ? 'checked' : ''); ?>><label class="form-check-label fw-semibold" for="accept_bank">Benki</label></div>
                    <?php ($selectedBank = strtolower(old('bank_name', $businessProfile->bank_name ?? ''))); ?>
                    <div id="accept_bank-details" class="payment-details mt-3 row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="bank_name">Jina la Benki</label>
                            <select class="form-select" id="bank_name" name="bank_name" form="warehouseSettingsForm">
                                <option value="">-- Chagua Benki --</option>
                                <option value="NMB" <?php echo e($selectedBank === 'nmb' ? 'selected' : ''); ?>>NMB</option>
                                <option value="CRDB" <?php echo e($selectedBank === 'crdb' ? 'selected' : ''); ?>>CRDB</option>
                                <option value="NBC" <?php echo e($selectedBank === 'nbc' ? 'selected' : ''); ?>>NBC</option>
                                <option value="Nyingine" <?php echo e(in_array($selectedBank, ['other', 'nyingine'], true) ? 'selected' : ''); ?>>Nyingine</option>
                            </select>
                        </div>
                        <div class="col-md-4"><label class="form-label" for="bank_account_number">Namba ya Akaunti</label><input class="form-control" id="bank_account_number" name="bank_account_number" form="warehouseSettingsForm" value="<?php echo e(old('bank_account_number', $businessProfile->bank_account_number ?? '')); ?>"></div>
                        <div class="col-md-4"><label class="form-label" for="bank_account_name">Jina la Akaunti</label><input class="form-control" id="bank_account_name" name="bank_account_name" form="warehouseSettingsForm" value="<?php echo e(old('bank_account_name', $businessProfile->bank_account_name ?? '')); ?>"></div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-pin-map-fill me-2 text-danger"></i>Mahali pa Ghala (GPS na Ramani)</h5>
                <p class="text-muted">Weka mahali sahihi pa ghala lako ili wateja wakuone kwenye ramani.</p>
            </div>
            <div class="card-body">
                
                <div class="map-preview-container mb-4">
                    <div id="warehouseLocationMap" class="map-preview rounded-4 border"></div>
                    <div class="map-overlay" id="mapOverlay">
                        <div class="text-center">
                            <i class="bi bi-geo-alt-fill text-danger fs-1 mb-2"></i>
                            <h6>Bonyeza "Tumia GPS Yangu" kuona ramani</h6>
                        </div>
                    </div>
                </div>

                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Latitude</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-arrow-up"></i></span>
                            <input type="text" class="form-control" id="shop_latitude" name="shop_latitude" 
                                   value="<?php echo e(old('shop_latitude', $businessProfile->shop_latitude ?? '-6.8192')); ?>" 
                                   placeholder="-6.8192" readonly required form="warehouseSettingsForm">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Longitude</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-arrow-right"></i></span>
                            <input type="text" class="form-control" id="shop_longitude" name="shop_longitude" 
                                   value="<?php echo e(old('shop_longitude', $businessProfile->shop_longitude ?? '39.2695')); ?>" 
                                   placeholder="39.2695" readonly required form="warehouseSettingsForm">
                        </div>
                    </div>
                </div>

                
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary" onclick="getWarehouseLocation()">
                        <i class="bi bi-geo-alt-fill"></i> Tumia GPS Yangu
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="searchLocation()">
                        <i class="bi bi-search"></i> Tafuta Mahali
                    </button>
                    <button type="button" class="btn btn-outline-info" onclick="openFullMap()">
                        <i class="bi bi-map"></i> Fungua Ramani Kubwa
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="refreshMap()">
                        <i class="bi bi-arrow-repeat"></i> Onesha Upya
                    </button>
                </div>

                
                <div id="searchLocationBox" class="mt-3" style="display: none;">
                    <div class="input-group">
                        <input type="text" class="form-control" id="locationSearchInput" 
                               placeholder="Mf: Kariakoo, Dar es Salaam">
                        <button class="btn btn-primary" type="button" onclick="performLocationSearch()">
                            <i class="bi bi-search"></i> Tafuta
                        </button>
                    </div>
                </div>

                
                <div id="locationStatus" class="mt-3 small"></div>

                
                <div class="alert alert-light border mt-3 mb-0">
                    <i class="bi bi-info-circle-fill text-primary me-2"></i>
                    <strong>Kwa nini GPS ni muhimu?</strong> Mahali sahihi husaidia wateja kukupata kwa urahisi.
                </div>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-truck me-2 text-success"></i>Mipangilio ya Huduma</h5>
                <p class="text-muted">Weka mipaka ya huduma zako za usafirishaji.</p>
            </div>
            <div class="card-body">
                
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_open" name="is_open" value="1" 
                               <?php echo e(old('is_open', $businessProfile->is_open ?? true) ? 'checked' : ''); ?> form="warehouseSettingsForm">
                        <label class="form-check-label fw-semibold" for="is_open">
                            <i class="bi bi-door-open me-1"></i> Ghala liko wazi kwa maagizo
                        </label>
                    </div>
                    <small class="text-muted d-block mt-1">Zima kipindi ghala likiwa halifanyi kazi.</small>
                </div>

                
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="can_deliver" name="can_deliver" value="1" 
                               <?php echo e(old('can_deliver', $businessProfile->can_deliver ?? false) ? 'checked' : ''); ?> form="warehouseSettingsForm">
                        <label class="form-check-label fw-semibold" for="can_deliver">
                            <i class="bi bi-bicycle me-1"></i> Natoa huduma ya usafirishaji
                        </label>
                    </div>
                    <small class="text-muted d-block mt-1">Wateja wanaweza kuchagua usafirishaji kutoka kwako.</small>
                </div>
            </div>
        </div>

        
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
    <a href="<?php echo e(route('wholesaler.dashboard')); ?>" class="btn btn-outline-secondary px-4">
        <i class="bi bi-x-circle"></i> Ghairi
    </a>
    <button type="submit" class="btn btn-primary px-5" form="warehouseSettingsForm">
        <i class="bi bi-check-lg"></i> Hifadhi Mabadiliko
    </button>
    </div>

        
        <div class="alert alert-info border-0 shadow-sm rounded-3 mt-4">
            <i class="bi bi-lightbulb-fill me-2"></i>
            <strong>Kidokezo:</strong> Taarifa sahihi za ghala lako husaidia wateja kukuamini na kuagiza kwa urahisi.
        </div>
    </div>
</div>


<div class="modal fade" id="fullMapModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pin-map-fill text-danger me-2"></i>
                    Chagua Mahali pa Ghala Lako
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="fullMapContainer" style="width: 100%; height: 100%;"></div>
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-between w-100">
                    <div>
                        <span class="badge bg-info me-2">Lat: <span id="modalLat">-6.8192</span></span>
                        <span class="badge bg-secondary">Lng: <span id="modalLng">39.2695</span></span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Ghairi</button>
                        <button type="button" class="btn btn-primary" onclick="confirmFullMapLocation()">
                            <i class="bi bi-check-lg"></i> Thibitisha Mahali
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let previewMap = null, currentMarker = null, fullMap = null, fullMapMarker = null;
    
    document.addEventListener('DOMContentLoaded', function() {
        initializePreviewMap();
        document.querySelectorAll('.payment-toggle').forEach(toggle => {
            const details = document.getElementById(toggle.dataset.target);
            if (!details) return;
            const sync = () => details.classList.toggle('d-none', !toggle.checked);
            toggle.addEventListener('change', sync); sync();
        });
        const latInput = document.getElementById('shop_latitude'), lngInput = document.getElementById('shop_longitude');
        if (latInput) latInput.addEventListener('dblclick', function() { this.readOnly = false; this.focus(); });
        if (lngInput) lngInput.addEventListener('dblclick', function() { this.readOnly = false; this.focus(); });
    });
    
    function initializePreviewMap() {
        const lat = parseFloat(document.getElementById('shop_latitude').value) || -6.8192;
        const lng = parseFloat(document.getElementById('shop_longitude').value) || 39.2695;
        if (!previewMap) {
            previewMap = L.map('warehouseLocationMap').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(previewMap);
            currentMarker = L.marker([lat, lng], { draggable: true }).addTo(previewMap);
            currentMarker.on('dragend', e => updateCoordinates(e.target.getLatLng().lat, e.target.getLatLng().lng));
            document.getElementById('mapOverlay').style.display = 'none';
        } else {
            previewMap.setView([lat, lng], 15);
            if (currentMarker) currentMarker.setLatLng([lat, lng]);
        }
    }
    
    function updateCoordinates(lat, lng) {
        document.getElementById('shop_latitude').value = lat.toFixed(6);
        document.getElementById('shop_longitude').value = lng.toFixed(6);
        document.getElementById('locationStatus').innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill"></i> Mahali pamesasishwa!</span>`;
    }
    
    function getWarehouseLocation() {
        const s = document.getElementById('locationStatus');
        s.innerHTML = '<span class="text-info"><i class="bi bi-arrow-repeat spin"></i> Inatafuta GPS...</span>';
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(p => {
                updateCoordinates(p.coords.latitude, p.coords.longitude);
                initializePreviewMap();
                s.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill"></i> GPS imepatikana!</span>';
                document.getElementById('mapOverlay').style.display = 'none';
            }, () => s.innerHTML = '<span class="text-danger">Imeshindikana kupata GPS.</span>');
        } else s.innerHTML = '<span class="text-danger">Kifaa hakitumii GPS.</span>';
    }
    
    function searchLocation() {
        const b = document.getElementById('searchLocationBox');
        b.style.display = b.style.display === 'none' ? 'block' : 'none';
        if (b.style.display === 'block') document.getElementById('locationSearchInput').focus();
    }
    
    function performLocationSearch() {
        const q = document.getElementById('locationSearchInput').value.trim();
        if (!q) { alert('Weka jina la eneo.'); return; }
        const s = document.getElementById('locationStatus');
        s.innerHTML = '<span class="text-info">Inatafuta...</span>';
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q + ', Tanzania')}`)
            .then(r => r.json()).then(d => {
                if (d && d.length) {
                    updateCoordinates(parseFloat(d[0].lat), parseFloat(d[0].lon));
                    initializePreviewMap();
                    s.innerHTML = '<span class="text-success">Eneo limepatikana!</span>';
                    document.getElementById('searchLocationBox').style.display = 'none';
                    document.getElementById('mapOverlay').style.display = 'none';
                } else s.innerHTML = '<span class="text-danger">Halikupatikana.</span>';
            }).catch(() => s.innerHTML = '<span class="text-danger">Imeshindikana.</span>');
    }
    
    function refreshMap() { initializePreviewMap(); document.getElementById('locationStatus').innerHTML = '<span class="text-success">Ramani imesasishwa.</span>'; }
    
    function openFullMap() {
        const lat = parseFloat(document.getElementById('shop_latitude').value) || -6.8192;
        const lng = parseFloat(document.getElementById('shop_longitude').value) || 39.2695;
        document.getElementById('modalLat').textContent = lat.toFixed(6);
        document.getElementById('modalLng').textContent = lng.toFixed(6);
        const modal = new bootstrap.Modal(document.getElementById('fullMapModal')); modal.show();
        setTimeout(() => {
            if (!fullMap) {
                fullMap = L.map('fullMapContainer').setView([lat, lng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(fullMap);
                fullMapMarker = L.marker([lat, lng], { draggable: true }).addTo(fullMap);
                fullMapMarker.on('dragend', e => {
                    const p = e.target.getLatLng();
                    document.getElementById('modalLat').textContent = p.lat.toFixed(6);
                    document.getElementById('modalLng').textContent = p.lng.toFixed(6);
                });
            } else { fullMap.setView([lat, lng], 16); if (fullMapMarker) fullMapMarker.setLatLng([lat, lng]); }
            fullMap.invalidateSize();
        }, 300);
    }
    
    function confirmFullMapLocation() {
        updateCoordinates(parseFloat(document.getElementById('modalLat').textContent), parseFloat(document.getElementById('modalLng').textContent));
        initializePreviewMap();
        document.getElementById('mapOverlay').style.display = 'none';
        bootstrap.Modal.getInstance(document.getElementById('fullMapModal')).hide();
    }
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .form-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; }
    .form-switch .form-check-input:checked { background-color: #198754; border-color: #198754; }
    .map-preview-container { position: relative; }
    .map-preview { width: 100%; height: 250px; background-color: #e9ecef; z-index: 1; }
    .map-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255,255,255,0.9); display: flex; align-items: center; justify-content: center; border-radius: 16px; z-index: 2; cursor: pointer; }
    .card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .card:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
    .form-control:focus, .form-select:focus { border-color: #FF6B35; box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
    .spin { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    #fullMapContainer { min-height: 500px; }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\GasPOA\resources\views\wholesaler\settings\shop.blade.php ENDPATH**/ ?>