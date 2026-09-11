@extends('layouts.dashboard')

@section('title', 'Agiza Gesi')
@section('page-title', 'Agiza Gesi Mpya au Kubadilisha')

@section('content')
@php
    $isNew = $serviceType === 'new';
@endphp

<div class="row">
    <div class="col-lg-10 mx-auto">
        {{-- Uchaguzi wa Aina ya Huduma --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('consumer.order.create', ['type' => 'refill']) }}" 
                       class="btn btn-lg {{ !$isNew ? 'btn-success' : 'btn-outline-success' }} px-4">
                        <i class="bi bi-arrow-repeat"></i> Kubadilisha (Refill)
                    </a>
                    <a href="{{ route('consumer.order.create', ['type' => 'new']) }}" 
                       class="btn btn-lg {{ $isNew ? 'btn-warning' : 'btn-outline-warning' }} px-4">
                        <i class="bi bi-cart4"></i> Mtungi Mpya
                    </a>
                </div>
            </div>
        </div>

        {{-- Fomu Kuu --}}
        <form method="POST" action="{{ route('consumer.order.place-order') }}" id="orderForm">
            @csrf
            <input type="hidden" name="service_type" id="serviceTypeInput" 
                   value="{{ $isNew ? 'new_cylinder' : 'refill_exchange' }}">
            <input type="hidden" name="retailer_id" id="selectedRetailerId">
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            {{-- 1. CHAGUA BIDHAA NA IDADI --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-box-seam me-2"></i> Chagua Bidhaa</h5>
                </div>
                <div class="card-body">
                    @if($products->isEmpty())
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            Hakuna bidhaa zinazopatikana kwa sasa.
                        </div>
                    @else
                        <div class="row g-3">
                            @php
                                $filteredProducts = $isNew ? $newCylinders : $refillProducts;
                            @endphp
                            
                            @forelse($filteredProducts as $product)
                            <div class="col-md-6">
                                <div class="form-check card-radio p-3 border rounded-3 h-100">
                                    <input class="form-check-input product-radio" type="radio" name="product_id" 
                                           id="product_{{ $product->id }}" value="{{ $product->id }}"
                                           data-price="{{ $isNew ? $product->suggested_retail_price : $product->suggested_wholesale_price }}"
                                           data-weight="{{ $product->weight_kg }}"
                                           data-name="{{ $product->name }}"
                                           data-category="{{ $product->category->name ?? 'Bidhaa' }}">
                                    <label class="form-check-label d-block" for="product_{{ $product->id }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $product->name }}</h6>
                                                <span class="badge bg-light text-dark">{{ $product->weight_kg }}kg</span>
                                                @if($product->category)
                                                    <span class="badge bg-secondary">{{ $product->category->name }}</span>
                                                @endif
                                            </div>
                                            <div class="text-end">
                                                <span class="fw-bold text-primary fs-5">
                                                    TZS {{ number_format($isNew ? $product->suggested_retail_price : $product->suggested_wholesale_price) }}
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    @if(!$isNew) 
                                                        <span class="text-success">Bei ya Kubadilisha</span>
                                                    @else
                                                        <span class="text-warning">Bei ya Mtungi Mpya</span>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="alert alert-info">Hakuna bidhaa za aina hii kwa sasa.</div>
                            </div>
                            @endforelse
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <label for="quantity" class="form-label fw-semibold">Idadi</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" 
                               value="1" min="1" max="10" style="width: 100px;">
                    </div>
                </div>
            </div>

            {{-- 2. MAELEZO YA UFIKISHAJI --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-geo-alt me-2"></i> Maelezo Ya Ufikishaji</h5>
                </div>
                <div class="card-body">
                    {{-- DROPDOWN YA ANWANI ZAIDI YA MOJA --}}
                    <div class="mb-3">
                        <label for="delivery_address" class="form-label fw-semibold">Chagua Anwani ya Kufikishia</label>
                        <select class="form-select form-select-lg" id="delivery_address" name="delivery_address" required>
                            <option value="">-- Chagua Anwani --</option>
                            @if(isset($userAddresses) && $userAddresses->isNotEmpty())
                                @foreach($userAddresses as $addr)
                                    <option value="{{ $addr->address }}" 
                                            data-lat="{{ $addr->latitude }}" 
                                            data-lng="{{ $addr->longitude }}"
                                            data-label="{{ $addr->label }}">
                                        {{ $addr->label }} - {{ \Illuminate\Support\Str::limit($addr->address, 40) }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>Hakuna anwani zilizohifadhiwa</option>
                            @endif
                        </select>
                        <small class="text-muted">Chagua moja ya anwani zako ulizohifadhi kwenye mipangilio.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="text" class="form-control" id="latitude_display" placeholder="-6.792354" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="text" class="form-control" id="longitude_display" placeholder="39.208328" readonly>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" id="getLocationBtn">
                            <i class="bi bi-geo-alt"></i> Tumia GPS Yangu
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="clearLocationBtn">
                            <i class="bi bi-x-circle"></i> Futa
                        </button>
                    </div>
                    <div id="locationStatus" class="mt-2 small"></div>
                </div>
            </div>

            {{-- 3. ORODHA YA WAUZAJI WALIOPO KARIBU --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4" id="retailersCard">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-shop me-2"></i> Wauzaji Karibu Yako</h5>
                    <p class="text-muted">Chagua muuzaji unayemtaka kutoka kwenye orodha.</p>
                </div>
                <div class="card-body">
                    <div id="retailersLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Inapakia...</span>
                        </div>
                        <p class="mt-2 text-muted">Chagua bidhaa na uweke eneo lako ili kuona wauzaji...</p>
                    </div>
                    
                    <div id="retailersList" class="retailers-container d-none"></div>
                    
                    <div id="noRetailersMessage" class="text-center py-4 d-none">
                        <i class="bi bi-shop-window display-4 text-muted"></i>
                        <h5 class="mt-3">Hakuna Muuzaji wa Karibu</h5>
                        <p class="text-muted">Tafadhali jaribu bidhaa nyingine au eneo jingine.</p>
                    </div>
                    
                    <div id="noLocationMessage" class="text-center py-4">
                        <i class="bi bi-geo-alt display-4 text-muted"></i>
                        <h5 class="mt-3">Weka Eneo Lako</h5>
                        <p class="text-muted">Tumia GPS au ingiza eneo lako hapo juu kuona wauzaji wa karibu.</p>
                    </div>
                </div>
            </div>
              
         {{-- MAELEZO YA MALIPO --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 d-none" id="paymentSection">
                <div class="card-header bg-white border-0 pt-4 pb-0"><h5 class="fw-bold"><i class="bi bi-credit-card me-2"></i> NJIA YA MALIPO</h5></div>
                <div class="card-body">
                    <p class="text-muted" id="paymentRetailerName"></p>
                    <div id="paymentMethodsList"></div>
                    <div class="alert alert-primary border-0 mt-3 d-none" id="paymentInstruction">
                        <div class="fw-bold mb-1"><i class="bi bi-info-circle-fill me-1"></i> Maelekezo ya Malipo</div>
                        <div id="paymentInstructionText"></div>
                    </div>
                </div>
            </div>

              {{-- 4. KASI YA HUDUMA --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-truck me-2"></i> KASI YA HUDUMA</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kasi ya Huduma</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="urgency" id="normal" value="normal" checked>
                                <label class="form-check-label" for="normal">
                                    <strong>Kawaida</strong> - Muda wa kufika 45-90 min
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="urgency" id="urgent" value="urgent">
                                <label class="form-check-label" for="urgent">
                                    <strong>Haraka</strong> <span class="badge bg-danger">+ TZS 3,000</span> - Muda wa kufika 20-40 min
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{--MUHTASARI WA GHARAMA --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold"><i class="bi bi-receipt me-2"></i> MUHTASARI WA MALIPO</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td>Bidhaa:</td>
                            <td class="text-end" id="summaryProduct">-</td>
                        </tr>
                        <tr>
                            <td>Huduma:</td>
                            <td class="text-end" id="summaryService">{{ $isNew ? 'Mtungi Mpya' : 'Kubadilisha' }}</td>
                        </tr>
                        <tr>
                            <td>Idadi:</td>
                            <td class="text-end" id="summaryQuantity">1</td>
                        </tr>
                        <tr>
                            <td>Ada ya Usafirishaji:</td>
                            <td class="text-end" id="summaryDelivery">TZS 0</td>
                        </tr>
                        <tr>
                            <td>Ada ya Haraka:</td>
                            <td class="text-end" id="summaryUrgent">TZS 0</td>
                        </tr>
                        <tr class="border-top fw-bold fs-5">
                            <td>Jumla:</td>
                            <td class="text-end" id="summaryTotal">TZS 0</td>
                        </tr>
                        <tr id="summaryPaymentRow" class="d-none">
                            <td>Utalipa kupitia:</td>
                            <td class="text-end" id="summaryPayment">-</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Loading Indicator --}}
            <div id="loadingIndicator" class="text-center mb-3 d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Inashughulikia...</span>
                </div>
                <p class="mt-2 text-muted">Tunashughulikia agizo lako, tafadhali subiri...</p>
            </div>

            {{-- 6. VITUFE VYA KUAGIZA NA GHAIRI --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg" id="submitOrderBtn" disabled>
                    <i class="bi bi-check2-circle"></i> Agiza Sasa
                </button>
                <a href="{{ route('consumer.dashboard') }}" class="btn btn-outline-secondary">
                    Ghairi
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // DOM Elements
        const productRadios = document.querySelectorAll('.product-radio');
        const quantityInput = document.getElementById('quantity');
        const submitBtn = document.getElementById('submitOrderBtn');
        const orderForm = document.getElementById('orderForm');
        const loadingIndicator = document.getElementById('loadingIndicator');
        
        const summaryProduct = document.getElementById('summaryProduct');
        const summaryQuantity = document.getElementById('summaryQuantity');
        const summaryDelivery = document.getElementById('summaryDelivery');
        const summaryUrgent = document.getElementById('summaryUrgent');
        const summaryTotal = document.getElementById('summaryTotal');
        
        const latitudeDisplay = document.getElementById('latitude_display');
        const longitudeDisplay = document.getElementById('longitude_display');
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const deliveryAddress = document.getElementById('delivery_address'); // hii ni SELECT
        const locationStatus = document.getElementById('locationStatus');
        
        const retailersList = document.getElementById('retailersList');
        const retailersLoading = document.getElementById('retailersLoading');
        const noRetailersMessage = document.getElementById('noRetailersMessage');
        const noLocationMessage = document.getElementById('noLocationMessage');
        const selectedRetailerId = document.getElementById('selectedRetailerId');
        const paymentSection = document.getElementById('paymentSection');
        const paymentMethodsList = document.getElementById('paymentMethodsList');
        const paymentRetailerName = document.getElementById('paymentRetailerName');
        const paymentInstruction = document.getElementById('paymentInstruction');
        const paymentInstructionText = document.getElementById('paymentInstructionText');
        const summaryPaymentRow = document.getElementById('summaryPaymentRow');
        const summaryPayment = document.getElementById('summaryPayment');
        
        let selectedProductPrice = 0;
        let selectedProductName = '-';
        let selectedProductId = null;
        let deliveryFee = 0;
        let currentRetailersData = null;
        let selectedPaymentDetails = null;

        // Helper to set coordinates and update UI
        function setCoords(lat, lng, message) {
            latitudeDisplay.value = lat;
            longitudeDisplay.value = lng;
            latitudeInput.value = lat;
            longitudeInput.value = lng;
            locationStatus.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill"></i> ${message}</span>`;
        }

        // Update summary and check conditions
        function updateSummary() {
            const quantity = parseInt(quantityInput.value) || 1;
            const price = parseFloat(selectedProductPrice) || 0;
            const subtotal = price * quantity;
            const isUrgent = document.getElementById('urgent').checked;
            const urgentFee = isUrgent ? 3000 : 0;
            
            const delivery = 0;
            
            summaryProduct.textContent = selectedProductName + ' (TZS ' + price.toLocaleString() + ' x ' + quantity + ')';
            summaryQuantity.textContent = quantity;
            summaryUrgent.textContent = 'TZS ' + urgentFee.toLocaleString();
            summaryDelivery.textContent = 'TZS ' + delivery.toLocaleString();
            
            const total = subtotal + delivery + urgentFee;
            summaryTotal.textContent = 'TZS ' + total.toLocaleString();

            if (selectedPaymentDetails) {
                summaryPaymentRow.classList.remove('d-none');
                summaryPayment.textContent = selectedPaymentDetails.label + (selectedPaymentDetails.account ? ' — ' + selectedPaymentDetails.account : '');
                paymentInstructionText.innerHTML = selectedPaymentDetails.value === 'cash'
                    ? `Lipa <strong>TZS ${total.toLocaleString()}</strong> taslimu wakati wa kupokea bidhaa.`
                    : `Lipa <strong>TZS ${total.toLocaleString()}</strong> kupitia <strong>${selectedPaymentDetails.label}</strong>${selectedPaymentDetails.account ? ` kwenda namba/akaunti <strong>${selectedPaymentDetails.account}</strong>` : ''}. Baada ya malipo, bonyeza <strong>Weka Order</strong>; hali ya malipo itabaki pending hadi ithibitishwe.`;
            } else {
                summaryPaymentRow.classList.add('d-none');
                paymentInstruction.classList.add('d-none');
            }
            
            const hasProduct = price > 0;
            const hasLocation = latitudeInput.value && longitudeInput.value && deliveryAddress.value.trim() !== '';
            const hasRetailer = selectedRetailerId.value !== '';
            const hasPayment = document.querySelector('input[name="payment_method"]:checked');
            
            submitBtn.disabled = !(hasProduct && hasLocation && hasRetailer && hasPayment);
        }

        async function loadPaymentMethods(retailerId) {
            paymentSection.classList.remove('d-none');
            selectedPaymentDetails = null;
            paymentInstruction.classList.add('d-none');
            paymentRetailerName.textContent = 'Inapakia njia za malipo za muuzaji...';
            paymentMethodsList.innerHTML = '';
            try {
                const response = await fetch(`{{ url('/consumer/order/payment-methods') }}/${retailerId}`, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
                const data = await response.json();
                paymentRetailerName.textContent = `Muuzaji aliyechaguliwa: ${data.retailer.name}`;
                if (!data.payment_methods.length) { paymentMethodsList.innerHTML = '<div class="alert alert-warning mb-0">Muuzaji huyu hajaweka njia ya malipo inayoweza kutumika.</div>'; return; }
                paymentMethodsList.innerHTML = data.payment_methods.map(method => `<label class="border rounded p-3 mb-2 d-block payment-option"><input class="form-check-input me-2" type="radio" name="payment_method" value="${method.value}" data-method='${JSON.stringify(method).replace(/'/g, '&#39;')}'> <strong>${method.label}</strong>${method.account ? `<div class="small text-muted ms-4">Lipa kupitia: ${method.account}</div>` : ''}${method.bank_name ? `<div class="small text-muted ms-4">${method.bank_name} — ${method.account_number}<br>${method.account_name || ''}</div>` : ''}</label>`).join('');
                document.querySelectorAll('input[name="payment_method"]').forEach(input => input.addEventListener('change', function () {
                    selectedPaymentDetails = JSON.parse(this.dataset.method);
                    if (selectedPaymentDetails.bank_name) selectedPaymentDetails.account = `${selectedPaymentDetails.bank_name} / ${selectedPaymentDetails.account_number}`;
                    paymentInstruction.classList.remove('d-none');
                    updateSummary();
                }));
            } catch (_) { paymentRetailerName.textContent = 'Imeshindikana kupakia njia za malipo.'; }
            updateSummary();
        }

        // Fetch retailers
        async function fetchRetailers() {
            selectedRetailerId.value = '';
            paymentSection.classList.add('d-none');
            paymentMethodsList.innerHTML = '';
            selectedPaymentDetails = null;
            if (!selectedProductId || !latitudeInput.value || !longitudeInput.value) {
                retailersList.classList.add('d-none');
                noRetailersMessage.classList.add('d-none');
                noLocationMessage.classList.remove('d-none');
                retailersLoading.classList.add('d-none');
                return;
            }
            
            const quantity = quantityInput.value;
            const urgencyRadio = document.querySelector('input[name="urgency"]:checked');
            const urgency = urgencyRadio ? urgencyRadio.value : 'normal';
            const serviceType = document.getElementById('serviceTypeInput').value;
            
            retailersList.classList.add('d-none');
            noRetailersMessage.classList.add('d-none');
            noLocationMessage.classList.add('d-none');
            retailersLoading.classList.remove('d-none');
            
            try {
                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                formData.append('product_id', selectedProductId);
                formData.append('quantity', quantity);
                formData.append('delivery_address', deliveryAddress.options[deliveryAddress.selectedIndex]?.text || 'N/A');
                formData.append('latitude', latitudeInput.value);
                formData.append('longitude', longitudeInput.value);
                formData.append('urgency', urgency);
                formData.append('service_type', serviceType);

                const response = await fetch('{{ route("consumer.order.find-retailers") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const data = await response.json();
                
                retailersLoading.classList.add('d-none');
                
                if (data.success && data.retailers.length > 0) {
                    currentRetailersData = data.retailers;
                    displayRetailers(data.retailers);
                    retailersList.classList.remove('d-none');
                } else {
                    noRetailersMessage.classList.remove('d-none');
                    selectedRetailerId.value = '';
                    deliveryFee = 0;
                    summaryDelivery.textContent = 'TZS 0';
                    updateSummary();
                }
            } catch (error) {
                console.error('❌ Error:', error);
                retailersLoading.classList.add('d-none');
                noRetailersMessage.classList.remove('d-none');
                selectedRetailerId.value = '';
                deliveryFee = 0;
                summaryDelivery.textContent = 'TZS 0';
                updateSummary();
            }
        }

        // Display retailers
        function displayRetailers(retailers) {
            const isUrgent = document.getElementById('urgent').checked;
            const urgencyFee = isUrgent ? 3000 : 0;
            const quantity = parseInt(quantityInput.value) || 1;
            const price = parseFloat(selectedProductPrice) || 0;
            const subtotal = price * quantity;
            
            let html = '';
            retailers.forEach((retailer, index) => {
                const deliveryFeeValue = parseFloat(retailer.delivery_fee) || 0;
                const effectiveDeliveryFee = 0;
                const totalAmount = subtotal + effectiveDeliveryFee + urgencyFee;
                const isRecommended = index === 0;
                
                html += `
                    <div class="retailer-card ${isRecommended ? 'recommended' : ''}" data-retailer-id="${retailer.id}" data-delivery-fee="${deliveryFeeValue}">
                        ${isRecommended ? '<div class="retailer-badge"><i class="bi bi-star-fill"></i> Karibu Zaidi</div>' : ''}
                        <div class="retailer-main">
                            <div class="retailer-radio">
                                <input class="form-check-input retailer-radio" type="radio" name="retailer_select" 
                                       id="retailer_${retailer.id}" value="${retailer.id}" 
                                       data-id="${retailer.id}" data-fee="${deliveryFeeValue}"
                                       >
                                <label class="form-check-label" for="retailer_${retailer.id}"></label>
                            </div>
                            <div class="retailer-info">
                                <div class="retailer-icon"><i class="bi bi-shop-window"></i></div>
                                <div class="retailer-details">
                                    <h5 class="retailer-name">${retailer.business_name || 'Muuzaji'}</h5>
                                    <p class="retailer-address">
                                        <i class="bi bi-geo-alt-fill"></i> ${(retailer.physical_address || 'Anwani haipo').substring(0, 40)}...
                                    </p>
                                    <div class="retailer-meta">
                                        <span class="badge bg-light text-dark"><i class="bi bi-truck"></i> ${retailer.distance || 0} km</span>
                                        <span class="badge bg-light text-dark"><i class="bi bi-clock"></i> ~${retailer.estimated_delivery || 45} dak</span>
                                    </div>
                                </div>
                            </div>
                            <div class="retailer-pricing">
                                <div class="price-breakdown">
                                    <small>Bidhaa: TZS ${subtotal.toLocaleString()}</small>
                                    <small>Usafirishaji: TZS 0 (Bure)</small>
                                    ${urgencyFee > 0 ? `<small>Ada ya Haraka: TZS ${urgencyFee.toLocaleString()}</small>` : ''}
                                </div>
                                <div class="total-price">TZS ${totalAmount.toLocaleString()}</div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            retailersList.innerHTML = html;
            
            // Target the actual input only: the surrounding icon container also
            // uses this CSS class and does not have a retailer id/value.
            document.querySelectorAll('input.retailer-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    const retailerId = this.value;
                    const fee = parseFloat(this.dataset.fee) || 0;
                    selectedRetailerId.value = retailerId;
                    deliveryFee = fee;
                    summaryDelivery.textContent = 'TZS 0';
                    document.querySelectorAll('.retailer-card').forEach(card => card.classList.remove('selected'));
                    this.closest('.retailer-card').classList.add('selected');
                    loadPaymentMethods(retailerId);
                    updateSummary();
                });
            });
            
            document.querySelectorAll('.retailer-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    if (e.target.type === 'radio') return;
                    const radio = this.querySelector('input.retailer-radio');
                    if (radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change'));
                    }
                });
            });
            
            updateSummary();
        }

        // Product selection
        productRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                selectedProductId = this.value;
                selectedProductPrice = parseFloat(this.dataset.price) || 0;
                selectedProductName = this.dataset.name + ' (' + this.dataset.weight + 'kg)';
                selectedRetailerId.value = '';
                selectedPaymentDetails = null;
                paymentSection.classList.add('d-none');
                paymentInstruction.classList.add('d-none');
                deliveryFee = 0;
                summaryDelivery.textContent = 'TZS 0';
                retailersList.classList.add('d-none');
                currentRetailersData = null;
                updateSummary();
                fetchRetailers();
            });
        });

        quantityInput.addEventListener('input', function() {
            updateSummary();
            if (selectedProductId) fetchRetailers();
        });

        // DROPDOWN CHANGE - jaza lat/lng kutoka data attributes
        deliveryAddress.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const lat = selectedOption.dataset.lat;
            const lng = selectedOption.dataset.lng;
            
            if (lat && lng) {
                setCoords(lat, lng, 'Anwani imechaguliwa!');
                updateSummary();
                fetchRetailers();
            } else {
                // Kama hakuna option valid
                latitudeDisplay.value = '';
                longitudeDisplay.value = '';
                latitudeInput.value = '';
                longitudeInput.value = '';
                updateSummary();
            }
        });

        // GPS Button (imeboreshwa) - inajaza dropdown pia
        document.getElementById('getLocationBtn').addEventListener('click', function() {
            if (!navigator.geolocation) {
                locationStatus.innerHTML = '<span class="text-danger">Kifaa hakitumii GPS.</span>';
                return;
            }
            locationStatus.innerHTML = '<span class="text-info"><i class="bi bi-arrow-repeat spin"></i> Inatafuta location...</span>';
            navigator.geolocation.getCurrentPosition(async function(position) {
                const lat = position.coords.latitude.toFixed(6);
                const lng = position.coords.longitude.toFixed(6);
                setCoords(lat, lng, 'Location imepatikana!');

                // Reverse geocode – pata jina la eneo
                try {
                    const revUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;
                    const revResp = await fetch(revUrl, { headers: { 'User-Agent': 'GasPOA/1.0' } });
                    const revData = await revResp.json();
                    if (revData.display_name) {
                        const shortName = revData.address.suburb || revData.address.neighbourhood || revData.address.road || revData.display_name.split(',')[0];
                        // Ongeza option mpya kwenye dropdown na ichague
                        const option = document.createElement('option');
                        option.value = shortName || revData.display_name;
                        option.text = `📍 GPS - ${shortName || revData.display_name}`;
                        option.dataset.lat = lat;
                        option.dataset.lng = lng;
                        option.selected = true;
                        deliveryAddress.appendChild(option);
                    }
                } catch (err) {
                    console.warn('Reverse geocode failed, using coordinates');
                    const option = document.createElement('option');
                    option.value = `GPS ${lat}, ${lng}`;
                    option.text = `📍 GPS - ${lat}, ${lng}`;
                    option.dataset.lat = lat;
                    option.dataset.lng = lng;
                    option.selected = true;
                    deliveryAddress.appendChild(option);
                }
                updateSummary();
                fetchRetailers();
            }, function() {
                locationStatus.innerHTML = '<span class="text-danger">Imeshindikana kupata GPS.</span>';
            });
        });

        // Clear Location
        document.getElementById('clearLocationBtn').addEventListener('click', function() {
            deliveryAddress.value = '';
            latitudeDisplay.value = '';
            longitudeDisplay.value = '';
            latitudeInput.value = '';
            longitudeInput.value = '';
            locationStatus.innerHTML = '<span class="text-muted">Anwani imefutwa.</span>';
            selectedRetailerId.value = '';
            selectedPaymentDetails = null;
            paymentSection.classList.add('d-none');
            paymentInstruction.classList.add('d-none');
            deliveryFee = 0;
            summaryDelivery.textContent = 'TZS 0';
            currentRetailersData = null;
            retailersList.classList.add('d-none');
            noRetailersMessage.classList.add('d-none');
            noLocationMessage.classList.remove('d-none');
            updateSummary();
        });

        // Latitude/Longitude manual input (kwa debug)
        latitudeDisplay.addEventListener('input', function() {
            latitudeInput.value = this.value;
            updateSummary();
            fetchRetailers();
        });
        longitudeDisplay.addEventListener('input', function() {
            longitudeInput.value = this.value;
            updateSummary();
            fetchRetailers();
        });
        latitudeDisplay.addEventListener('dblclick', () => latitudeDisplay.readOnly = false);
        longitudeDisplay.addEventListener('dblclick', () => longitudeDisplay.readOnly = false);

        orderForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            loadingIndicator.classList.remove('d-none');
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Inashughulikia...';
        });

        // Urgency change
        document.querySelectorAll('input[name="urgency"]').forEach(radio => {
            radio.addEventListener('change', function() {
                updateSummary();
                if (selectedProductId && latitudeInput.value && longitudeInput.value) {
                    retailersList.classList.add('d-none');
                    noRetailersMessage.classList.add('d-none');
                    retailersLoading.classList.remove('d-none');
                    fetchRetailers();
                } else if (selectedProductId && currentRetailersData) {
                    displayRetailers(currentRetailersData);
                }
            });
        });

        // Pre-selected product
        const checkedProduct = document.querySelector('.product-radio:checked');
        if (checkedProduct) {
            selectedProductId = checkedProduct.value;
            selectedProductPrice = parseFloat(checkedProduct.dataset.price) || 0;
            selectedProductName = checkedProduct.dataset.name + ' (' + checkedProduct.dataset.weight + 'kg)';
        }
        updateSummary();
    });
</script>

<style>
    /* ===== RETAILERS LIST STYLES ===== */
    .retailers-container { display: flex; flex-direction: column; gap: 15px; max-height: 400px; overflow-y: auto; padding-right: 5px; }
    .retailers-container::-webkit-scrollbar { width: 5px; }
    .retailers-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .retailers-container::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }
    
    .retailer-card { background: white; border: 1.5px solid #e9ecef; border-radius: 16px; padding: 15px 18px; position: relative; transition: all 0.3s ease; cursor: pointer; }
    .retailer-card:hover { border-color: #FF6B35; box-shadow: 0 8px 20px rgba(255, 107, 53, 0.1); }
    .retailer-card.selected { border-color: #FF6B35; border-width: 2px; background: linear-gradient(145deg, #ffffff 0%, #fff8f5 100%); box-shadow: 0 8px 20px rgba(255, 107, 53, 0.15); }
    .retailer-card.recommended { border-color: #FF6B35; border-width: 2px; }
    .retailer-badge { position: absolute; top: -10px; left: 15px; background: linear-gradient(145deg, #FF6B35, #E85D2C); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .retailer-main { display: flex; flex-wrap: wrap; gap: 15px; }
    .retailer-radio { display: flex; align-items: center; margin-right: 5px; }
    .retailer-radio input[type="radio"] { width: 20px; height: 20px; cursor: pointer; accent-color: #FF6B35; }
    .retailer-info { flex: 2; min-width: 200px; display: flex; gap: 12px; }
    .retailer-icon { width: 45px; height: 45px; background: linear-gradient(145deg, #a3a3e4 0%, #b3b896 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.3rem; flex-shrink: 0; }
    .retailer-name { font-weight: 700; margin-bottom: 4px; color: #1A1A2E; font-size: 1rem; }
    .retailer-address { color: #6c757d; font-size: 0.85rem; margin-bottom: 6px; }
    .retailer-meta { display: flex; flex-wrap: wrap; gap: 6px; }
    .retailer-pricing { flex: 1; min-width: 150px; text-align: right; }
    .price-breakdown { display: flex; flex-direction: column; gap: 2px; margin-bottom: 8px; color: #6c757d; font-size: 0.8rem; }
    .total-price { font-size: 1.3rem; font-weight: 700; color: #FF6B35; }

    /* ===== STYLES ZA AWALI ZIMEHIFADHIWA ===== */
    .card-radio { cursor: pointer; transition: all 0.2s; background: linear-gradient(145deg, #a3a3e4 0%, #b3b896 100%); }
    .card-radio:hover { border-color: #0d6efd !important; background-color: #f8f9fa; }
    .card-radio input[type="radio"]:checked + label { color: #0d6efd; }
    .card-radio:has(input[type="radio"]:checked) { border-color: #0d6efd !important; background-color: #e7f1ff; }
    .spin { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    /* ===== VITUFE VYA UCHAGUZI WA HUDUMA ===== */
    .btn-success, .btn-outline-success { border-radius: 50px; font-weight: 600; padding: 0.9rem 2rem; transition: all 0.3s ease; }
    .btn-success { background: linear-gradient(145deg, #2A9D8F 0%, #1e7a6e 100%); border: none; color: white; box-shadow: 0 8px 18px rgba(42, 157, 143, 0.25); }
    .btn-success:hover { background: linear-gradient(145deg, #1e7a6e 0%, #2A9D8F 100%); transform: translateY(-2px); box-shadow: 0 12px 22px rgba(42, 157, 143, 0.3); color: white; }
    .btn-outline-success { background: transparent; border: 2px solid #2A9D8F; color: #2A9D8F; }
    .btn-outline-success:hover { background: #2A9D8F; color: white; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(42, 157, 143, 0.2); }
    .btn-warning { background: linear-gradient(145deg, #F9C22E 0%, #E6A800 100%); border: none; color: #1A1A2E; box-shadow: 0 8px 18px rgba(249, 194, 46, 0.25); }
    .btn-warning:hover { background: linear-gradient(145deg, #E6A800 0%, #F9C22E 100%); transform: translateY(-2px); box-shadow: 0 12px 22px rgba(249, 194, 46, 0.3); color: #1A1A2E; }
    .btn-outline-warning { background: transparent; border: 2px solid #F9C22E; color: #B8860B; }
    .btn-outline-warning:hover { background: #d4a31a; color: #1A1A2E; transform: translateY(-2px); }
    #submitOrderBtn:disabled { cursor: not-allowed; opacity: 0.6; }
    .spinner-border { width: 2rem; height: 2rem; }

    @media (max-width: 768px) {
        .retailer-main { flex-direction: column; }
        .retailer-pricing { text-align: left; }
        .total-price { font-size: 1.2rem; }
        .retailer-radio { align-self: flex-start; }
    }
    @media (max-width: 576px) {
        .btn-success, .btn-outline-success, .btn-warning, .btn-outline-warning { padding: 0.7rem 1.2rem; font-size: 0.9rem; }
    }
</style>
@endpush
