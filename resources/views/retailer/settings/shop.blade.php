@extends('layouts.dashboard')

@section('title', 'Mipangilio ya Duka')
@section('page-title', 'Mipangilio ya Duka Langu')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        {{-- Maelezo ya Duka --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h4 class="fw-bold"><i class="bi bi-shop me-2 text-primary"></i>Maelezo ya Duka</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('retailer.settings.update') }}" id="shopSettingsForm">
                    @csrf
                    @method('PUT')

                    {{-- Jina la Biashara --}}
          <div class="mb-4">
             <label class="form-label fw-semibold">
                 <i class="bi bi-building me-1"></i> Jina la Biashara / Duka
             </label>
             <div class="business-name-display p-3 bg-light rounded-3 border">
             <div class="d-flex align-items-center">
             <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3" 
                 style="width: 48px; height: 48px;">
                <i class="bi bi-building fs-4 text-primary"></i>
             </div>
             <div>
                <h5 class="fw-bold mb-0">{{ $businessProfile->business_name ?? Auth::user()->full_name . ' Duka' }}</h5>
                <small class="text-muted">
                    <i class="bi bi-check-circle-fill text-success me-1"></i> Jina la biashara | Duka

                    <small class="text-muted mt-2 d-block">
                    <i class="bi bi-info-circle me-1"></i> 
                    <a href="{{ route('retailer.profile.edit') }}" class="text-primary" style="text-decoration:none;">Badilisha Jina la biashara Hapa>></a>
                   </small>
                </small>
               </div>
               </div>
                </div>
              {{-- Hidden input ili jina litumwe kwenye form --}}
              <input type="hidden" name="business_name" value="{{ $businessProfile->business_name ?? Auth::user()->full_name . ' Duka' }}">
                </div>

            {{-- Namba ya Simu --}}
             <div class="mb-4">
               <label for="shop_phone" class="form-label fw-semibold">
               <i class="bi bi-telephone me-1"></i> Namba ya Simu
             </label>
               <div class="input-group">
                  <span class="input-group-text">+255</span>
                     <input type="tel" class="form-control" id="shop_phone" name="shop_phone" 
                  value="{{ old('shop_phone', ltrim($businessProfile->phone_number ?? Auth::user()->phone_number, '+255')) }}" 
                 placeholder="615004300">
                 </div>
                  <small class="text-muted d-block mt-1">
                     <i class="bi bi-info-circle me-1"></i> 
                  Namba hii itasaidia wateja wako kuwasiliana na wewe !
                  </small>
                   @error('shop_phone')
                     <span class="text-danger small d-block">{{ $message }}</span>
                    @enderror

               {{-- Uthibitishaji wa Simu --}}
                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                        <div>
                            <h6 class="fw-semibold mb-1">
                                <i class="bi bi-phone-check me-2 text-primary"></i>Namba ya Simu Imethibitishwa?
                            </h6>
                            <p class="text-muted small mb-0">
                                @if(Auth::user()->is_phone_verified)
                                    Thibitisha Namba kwa OTP
                                @else
                                    Thibitisha namba yako ili kuongeza usalama.
                                @endif
                            </p>
                        </div>
                        <div>
                            @if(Auth::user()->is_phone_verified)
                                <span class="badge bg-success rounded-pill px-4 py-2">
                                    <i class="bi bi-check-circle-fill"></i> Imethibitishwa
                                </span>
                            @else
                                <a href="{{ route('verification.phone.notice') }}" class="btn btn-outline-primary btn-sm">
                                    Thibitisha Sasa
                                </a>
                            @endif
                        </div>
                        </div>
                    </div>

                    {{-- Namba ya TIN (LAZIMA) --}}
                    <div class="mb-4">
                        <label for="tinn_number" class="form-label fw-semibold">
                            <i class="bi bi-file-text me-1"></i> Namba ya TIN (TRA)
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="tinn_number" name="tinn_number" 
                               value="{{ old('tinn_number', $businessProfile->tinn_number ?? '') }}" 
                               placeholder="Mf: 123-456-789" required>
                        <small class="text-muted">
                            <i class="bi bi-shield-check"></i> Namba ya TIN inahitajika kwa ajili ya utambulisho wa biashara.
                        </small>
                        @error('tinn_number')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Anwani ya Duka --}}
                    <div class="mb-4">
                        <label for="physical_address" class="form-label fw-semibold">
                            <i class="bi bi-geo-alt me-1"></i> Anwani Kamili ya Duka
                            <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="physical_address" name="physical_address" 
                                  rows="3" placeholder="Mf: Mtaa wa Mwembechai, Karibu na Msikiti, Kinondoni, Dar es Salaam" 
                                  required>{{ old('physical_address', $businessProfile->physical_address ?? '') }}</textarea>
                        @error('physical_address')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </form>
            </div>
        </div>

        {{-- GPS Location & Map --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-pin-map-fill me-2 text-danger"></i>Mahali pa Duka (GPS na Ramani)</h5>
                <p class="text-muted">Weka mahali sahihi pa duka lako ili wateja wakuone kwenye ramani.</p>
            </div>
            <div class="card-body">
                {{-- Map Preview --}}
                <div class="map-preview-container mb-4">
                    <div id="shopLocationMap" class="map-preview rounded-4 border"></div>
                    <div class="map-overlay" id="mapOverlay">
                        <div class="text-center">
                            <i class="bi bi-geo-alt-fill text-danger fs-1 mb-2"></i>
                            <h6>Bonyeza "Tumia GPS Yangu" au "Tafuta Mahali" kuona ramani</h6>
                        </div>
                    </div>
                </div>

                {{-- GPS Coordinates Display --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-globe me-1"></i> Latitude
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-arrow-up"></i></span>
                            <input type="text" class="form-control" id="shop_latitude" name="shop_latitude" 
                                   value="{{ old('shop_latitude', $businessProfile->shop_latitude ?? '-6.792354') }}" 
                                   placeholder="-6.792354" readonly required form="shopSettingsForm">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-globe me-1"></i> Longitude
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-arrow-right"></i></span>
                            <input type="text" class="form-control" id="shop_longitude" name="shop_longitude" 
                                   value="{{ old('shop_longitude', $businessProfile->shop_longitude ?? '39.208328') }}" 
                                   placeholder="39.208328" readonly required form="shopSettingsForm">
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary" onclick="getShopLocation()">
                        <i class="bi bi-geo-alt-fill"></i> Tumia GPS Yangu
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="searchLocation()">
                        <i class="bi bi-search"></i> Tafuta Mahali
                    </button>
                    <button type="button" class="btn btn-outline-info" onclick="openLocationPicker()">
                        <i class="bi bi-map"></i> Fungua Ramani Kubwa
                    </button>
                    <button type="button" class="btn btn-outline-success" onclick="refreshMap()">
                        <i class="bi bi-arrow-repeat"></i> Onesha Upya
                    </button>
                </div>

                {{-- Search Input --}}
                <div id="searchLocationBox" class="mt-3" style="display: none;">
                    <div class="input-group">
                        <input type="text" class="form-control" id="locationSearchInput" 
                               placeholder="Mf: Mwembechai, Kinondoni, Dar es Salaam">
                        <button class="btn btn-primary" type="button" onclick="performLocationSearch()">
                            <i class="bi bi-search"></i> Tafuta
                        </button>
                    </div>
                    <small class="text-muted mt-1">Weka jina la eneo, mtaa, au jiji</small>
                </div>

                {{-- Location Status --}}
                <div id="locationStatus" class="mt-3 small"></div>

                {{-- Help Text --}}
                <div class="alert alert-light border mt-3 mb-0">
                    <i class="bi bi-info-circle-fill text-primary me-2"></i>
                    <strong>Kwa nini GPS ni muhimu?</strong> Mahali sahihi husaidia wateja kukupata kwa urahisi na kuhesabu umbali wa kufika.
                </div>
            </div>
        </div>

        {{-- Mipangilio ya Huduma --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0"><h5 class="fw-bold"><i class="bi bi-credit-card me-2 text-success"></i>Njia za Malipo</h5><p class="text-muted">Washa njia unayotumia, kisha jaza Lipa Namba au taarifa za benki. Bila taarifa kamili haitatokea kwa mteja.</p></div>
            <div class="card-body">
                <div class="form-check form-switch mb-3"><input class="form-check-input payment-toggle" type="checkbox" id="accept_cash" name="accept_cash" value="1" form="shopSettingsForm" {{ old('accept_cash', $businessProfile->accept_cash ?? false) ? 'checked' : '' }}><label class="form-check-label fw-semibold" for="accept_cash">Pesa Taslimu</label></div>

                @php($mobileMethods = [
                    'accept_mpesa' => ['label' => 'M-Pesa', 'number' => 'mpesa_number', 'placeholder' => 'Mfano: 445123'],
                    'accept_tigopesa' => ['label' => 'TigoPesa / Mixx by Yas', 'number' => 'mixx_number', 'placeholder' => 'Mfano: 445123'],
                    'accept_airtelmoney' => ['label' => 'Airtel Money', 'number' => 'airtel_number', 'placeholder' => 'Mfano: 445123'],
                    'accept_halopesa' => ['label' => 'HaloPesa', 'number' => 'halopesa_number', 'placeholder' => 'Mfano: 445123'],
                ])
                @foreach($mobileMethods as $toggle => $method)
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="form-check form-switch"><input class="form-check-input payment-toggle" type="checkbox" id="{{ $toggle }}" name="{{ $toggle }}" value="1" data-target="{{ $toggle }}-details" form="shopSettingsForm" {{ old($toggle, $businessProfile->$toggle ?? false) ? 'checked' : '' }}><label class="form-check-label fw-semibold" for="{{ $toggle }}">{{ $method['label'] }}</label></div>
                        <div id="{{ $toggle }}-details" class="payment-details mt-3">
                            <label class="form-label" for="{{ $method['number'] }}">Lipa Namba ya {{ $method['label'] }}</label>
                            <input type="tel" class="form-control" id="{{ $method['number'] }}" name="{{ $method['number'] }}" form="shopSettingsForm" value="{{ old($method['number'], $businessProfile->{$method['number']} ?? '') }}" placeholder="{{ $method['placeholder'] }}">
                            <small class="text-muted">Hii ndiyo namba ambayo mteja ataonyeshwa alipie.</small>
                        </div>
                    </div>
                @endforeach

                <div class="border rounded-3 p-3">
                    <div class="form-check form-switch"><input class="form-check-input payment-toggle" type="checkbox" id="accept_bank" name="accept_bank" value="1" data-target="accept_bank-details" form="shopSettingsForm" {{ old('accept_bank', $businessProfile->accept_bank ?? false) ? 'checked' : '' }}><label class="form-check-label fw-semibold" for="accept_bank">Bank Transfer</label></div>
                    <div id="accept_bank-details" class="payment-details mt-3">
                        <div class="row g-3">
                            @php($selectedBank = strtolower(old('bank_name', $businessProfile->bank_name ?? '')))
                            <div class="col-md-4">
                                <label class="form-label" for="bank_name">Jina la Benki</label>
                                <select class="form-select" id="bank_name" name="bank_name" form="shopSettingsForm">
                                    <option value="">-- Chagua Benki --</option>
                                    <option value="NMB" {{ $selectedBank === 'nmb' ? 'selected' : '' }}>NMB</option>
                                    <option value="CRDB" {{ $selectedBank === 'crdb' ? 'selected' : '' }}>CRDB</option>
                                    <option value="NBC" {{ $selectedBank === 'nbc' ? 'selected' : '' }}>NBC</option>
                                    <option value="Nyingine" {{ in_array($selectedBank, ['other', 'nyingine'], true) ? 'selected' : '' }}>Nyingine</option>
                                </select>
                            </div>
                            <div class="col-md-4"><label class="form-label" for="bank_account_number">Namba ya Akaunti</label><input class="form-control" id="bank_account_number" name="bank_account_number" form="shopSettingsForm" value="{{ old('bank_account_number', $businessProfile->bank_account_number ?? '') }}" placeholder="Mfano: 1234567890"></div>
                            <div class="col-md-4"><label class="form-label" for="bank_account_name">Jina Lililosajiliwa</label><input class="form-control" id="bank_account_name" name="bank_account_name" form="shopSettingsForm" value="{{ old('bank_account_name', $businessProfile->bank_account_name ?? '') }}" placeholder="Mfano: TaifaGesi Centre Ltd"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-truck me-2 text-success"></i>Mipangilio ya Huduma</h5>
                <p class="text-muted">Weka mipaka ya huduma zako za usafirishaji.</p>
            </div>
            <div class="card-body">
                {{-- Duka Liko Wazi --}}
                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_open" name="is_open" value="1" 
                               {{ old('is_open', $businessProfile->is_open ?? true) ? 'checked' : '' }} form="shopSettingsForm">
                        <label class="form-check-label fw-semibold" for="is_open">
                            <i class="bi bi-door-open me-1"></i> Duka liko wazi kwa maagizo
                        </label>
                    </div>
                    <small class="text-muted d-block mt-1">Zima kipindi duka likiwa halifanyi kazi.</small>
                </div>

            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="{{ route('retailer.dashboard') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-x-circle"></i> Ghairi
            </a>
            <button type="submit" class="btn btn-primary px-5" form="shopSettingsForm">
                <i class="bi bi-check-lg"></i> Hifadhi Mabadiliko
            </button>
        </div>

        {{-- Quick Tip --}}
        <div class="alert alert-info border-0 shadow-sm rounded-3 mt-4">
            <i class="bi bi-lightbulb-fill me-2"></i>
            <strong>Kidokezo:</strong> Taarifa sahihi za duka lako husaidia wateja kukuamini na kuagiza kwa urahisi.
        </div>
    </div>
</div>

{{-- Full Screen Map Modal --}}
<div class="modal fade" id="fullMapModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pin-map-fill text-danger me-2"></i>
                    Chagua Mahali pa Duka Lako
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div id="fullMapContainer" style="width: 100%; height: 100%;"></div>
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-between w-100">
                    <div>
                        <span class="badge bg-info me-2">Lat: <span id="modalLat">-6.792354</span></span>
                        <span class="badge bg-secondary">Lng: <span id="modalLng">39.208328</span></span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Ghairi</button>
                        <button type="button" class="btn btn-primary" onclick="confirmFullMapLocation()">
                            <i class="bi bi-check-lg"></i> Thibitisha Mahali Hapa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let previewMap = null, fullMap = null, currentMarker = null, fullMapMarker = null;
    
    document.addEventListener('DOMContentLoaded', function() {
        initializePreviewMap();
        const latInput = document.getElementById('shop_latitude'), lngInput = document.getElementById('shop_longitude');
        if (latInput) latInput.addEventListener('dblclick', function() { this.readOnly = false; this.focus(); });
        if (lngInput) lngInput.addEventListener('dblclick', function() { this.readOnly = false; this.focus(); });
        document.querySelectorAll('.payment-toggle[data-target]').forEach(toggle => {
            const details = document.getElementById(toggle.dataset.target);
            const inputs = details ? details.querySelectorAll('input') : [];
            const sync = () => {
                if (details) details.classList.toggle('d-none', !toggle.checked);
                inputs.forEach(input => input.required = toggle.checked);
            };
            toggle.addEventListener('change', sync);
            sync();
        });
    });
    
    function initializePreviewMap() {
        const lat = parseFloat(document.getElementById('shop_latitude').value) || -6.792354;
        const lng = parseFloat(document.getElementById('shop_longitude').value) || 39.208328;
        if (!previewMap) {
            previewMap = L.map('shopLocationMap').setView([lat, lng], 15);
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
    
    function getShopLocation() {
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
    
    function openLocationPicker() {
        const lat = parseFloat(document.getElementById('shop_latitude').value) || -6.792354;
        const lng = parseFloat(document.getElementById('shop_longitude').value) || 39.208328;
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
@endpush

@push('styles')
<style>
    .form-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; }
    .form-switch .form-check-input:checked { background-color: #198754; border-color: #198754; }
    .map-preview-container { position: relative; }
    .map-preview { width: 100%; height: 250px; background-color: #e9ecef; z-index: 1; }
    .map-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255,255,255,0.9); display: flex; align-items: center; justify-content: center; border-radius: 16px; z-index: 2; cursor: pointer; }
    .card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .card:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
    .form-control:focus, .form-select:focus { border-color: #FF6B35; box-shadow: 0 0 0 3px rgba(255,107,53,0.1); }
    .text-danger { color: #dc3545 !important; }
    .spin { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    #fullMapContainer { min-height: 500px; }
</style>
@endpush
