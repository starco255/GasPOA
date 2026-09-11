@extends('layouts.dashboard')

@section('title', 'Maelezo ya Agizo')
@section('page-title', 'Maelezo ya Agizo #' . $order->order_number)

@section('content')
@php
    $isUrgent = ($order->urgency_level ?? 'normal') === 'urgent';
    $urgencyFee = $isUrgent ? 3000 : 0;
    
    // Status badge
    $statusBadges = [
        'pending' => 'bg-secondary',
        'accepted' => 'bg-info',
        'picked_up' => 'bg-primary',
        'out_for_delivery' => 'bg-warning text-dark',
        'delivered' => 'bg-success',
        'cancelled' => 'bg-danger',
    ];
    $statusBadge = $statusBadges[$order->status] ?? 'bg-secondary';
    
    // Status label
    $statusLabels = [
        'pending' => 'Inasubiri',
        'accepted' => 'Imekubaliwa',
        'picked_up' => 'Imeshachukuliwa',
        'out_for_delivery' => 'Njiani',
        'delivered' => 'Imekamilika',
        'cancelled' => 'Imefutwa',
    ];
    $statusLabel = $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));
    
    // Payment method
    $paymentMethodLabels = [
        'cash' => 'Pesa Taslimu',
        'mobile_money' => 'M-Pesa/TigoPesa/Airtel Money',
        'card' => 'Kadi ya Benki',
        'mpesa' => 'M-Pesa',
        'tigopesa' => 'TigoPesa / Mixx by Yas',
        'airtelmoney' => 'Airtel Money',
        'halopesa' => 'HaloPesa',
        'bank' => 'Benki',
    ];
    $selectedPaymentMethod = $order->payment_method_provider ?? $order->payment_method;
    $paymentMethod = $paymentMethodLabels[$selectedPaymentMethod] ?? ucfirst($selectedPaymentMethod);
    
    $paymentStatusLabels = [
        'pending' => 'Haijalipwa',
        'paid' => 'Imelipwa',
        'failed' => 'Imeshindikana',
    ];
    $paymentStatus = $paymentStatusLabels[$order->payment_status] ?? ucfirst($order->payment_status);
    $paymentStatusBadge = $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning';
@endphp

<div class="row">
    <div class="col-lg-8 mx-auto">
        {{-- Maelezo ya Msingi --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-info-circle me-2"></i>Maelezo ya Agizo</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Namba ya Agizo</label>
                        <h6 class="fw-bold">{{ $order->order_number }}</h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Tarehe ya Kuagiza</label>
                        <h6>{{ $order->created_at ? $order->created_at->format('d M Y, H:i') : 'Hivi karibuni' }}</h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Hali ya Agizo</label>
                        <h6><span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span></h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Kasi ya Huduma</label>
                        <h6>
                            <span class="badge {{ $isUrgent ? 'bg-danger' : 'bg-secondary' }}">
                                {{ $isUrgent ? 'Haraka' : 'Kawaida' }}
                            </span>
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bidhaa Zilizoagizwa --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-box-seam me-2"></i>Bidhaa Zilizoagizwa</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Bidhaa</th>
                                <th>Aina</th>
                                <th>Idadi</th>
                                <th>Bei/Unit</th>
                                <th>Jumla</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            @php
                                $product = $item->product;
                                $serviceType = $item->service_type_requested;
                                $serviceLabel = $serviceType === 'new_cylinder' ? 'Mtungi Mpya' : 'Kubadilisha';
                            @endphp
                            <tr>
                                <td>{{ $product->name ?? 'Bidhaa' }} ({{ $product->weight_kg ?? 0 }}kg)</td>
                                <td>{{ $serviceLabel }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>TZS {{ number_format($item->price_per_item) }}</td>
                                <td>TZS {{ number_format($item->price_per_item * $item->quantity) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Muhtasari wa Gharama --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-receipt me-2"></i>Muhtasari wa Gharama</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    @php
                        $subtotal = $order->items->sum(function($item) {
                            return $item->price_per_item * $item->quantity;
                        });
                    @endphp
                    <tr>
                        <td>Jumla ya Bidhaa:</td>
                        <td class="text-end">TZS {{ number_format($subtotal) }}</td>
                    </tr>
                    <tr>
                        <td>Usafirishaji:</td>
                        <td class="text-end">TZS 0 (Bure)</td>
                    </tr>
                    @if($isUrgent)
                    <tr>
                        <td>Ada ya Haraka:</td>
                        <td class="text-end">TZS 3,000</td>
                    </tr>
                    @endif
                    <tr class="border-top fw-bold">
                        <td>Jumla Kuu:</td>
                        <td class="text-end">TZS {{ number_format($order->total_amount) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Maelezo ya Muuzaji --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-shop me-2"></i>Maelezo ya Muuzaji</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Jina la Biashara</label>
                        <h6>{{ $order->retailer->business_name ?? 'Haijulikani' }}</h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Namba ya Simu</label>
                        <h6>{{ $order->retailer->user->phone_number ?? 'Haijulikani' }}</h6>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small">Anwani</label>
                        <h6>{{ $order->retailer->physical_address ?? 'Haijulikani' }}</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- Maelezo ya Ufikishaji --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-geo-alt me-2"></i>Maelezo ya Ufikishaji</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Anwani ya Kufikishia</label>
                    <h6>{{ $order->delivery_address }}</h6>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Latitude</label>
                        <h6>{{ $order->delivery_latitude }}</h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Longitude</label>
                        <h6>{{ $order->delivery_longitude }}</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- Maelezo ya Malipo --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-credit-card me-2"></i>Maelezo ya Malipo</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Njia ya Malipo</label>
                        <h6>{{ $paymentMethod }}</h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Hali ya Malipo</label>
                        <h6><span class="badge {{ $paymentStatusBadge }}">{{ $paymentStatus }}</span></h6>
                    </div>
                    @if($order->transaction_reference)
                    <div class="col-12 mb-3">
                        <label class="text-muted small">Kumbukumbu ya Muamala</label>
                        <h6>{{ $order->transaction_reference }}</h6>
                    </div>
                    @endif
                </div>
                @if($order->payment_status === 'paid' && $order->transaction_reference)
                    <div class="alert alert-success mb-0"><i class="bi bi-lock-fill me-1"></i>Reference ID imethibitishwa na haiwezi kubadilishwa.</div>
                @elseif($order->payment_status !== 'paid')
                    <div class="alert alert-secondary mb-0 small"><i class="bi bi-info-circle me-1"></i>Haya ni maelezo ya agizo pekee. Tumia ukurasa wa kufuatilia agizo kufanya malipo au kutuma Reference ID.</div>
                @endif
            </div>
        </div>

        {{-- Vitufe --}}
        <div class="d-flex gap-2 justify-content-between mb-4">
            <a href="{{ route('consumer.history') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Rudi kwenye Historia
            </a>
            <div>
                @if(in_array($order->status, ['pending', 'accepted', 'picked_up', 'out_for_delivery']))
                <a href="{{ route('consumer.order.tracking', $order->id) }}" class="btn btn-outline-primary me-2">
                    <i class="bi bi-geo-alt"></i> Fuatilia
                </a>
                @endif
                @if($order->payment_status === 'paid')
                    <a href="{{ route('consumer.order.receipt', $order->id) }}" class="btn btn-primary"><i class="bi bi-download"></i> Pakua Risiti</a>
                @else
                    <button type="button" class="btn btn-primary" disabled title="Risiti hupatikana baada ya malipo kuthibitishwa."><i class="bi bi-lock"></i> Pakua Risiti</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.transaction-reference-input').forEach(function(input) {
            const submitButton = input.closest('.payment-reference-form')?.querySelector('.reference-submit-button');
            const validateReference = function() {
                input.value = input.value.replace(/\D/g, '').slice(0, 100);
                const isValid = input.value.length >= 10;
                input.setCustomValidity(isValid ? '' : 'Reference ID lazima iwe na tarakimu 10 au zaidi.');
                if (submitButton) submitButton.disabled = !isValid;
            };
            input.addEventListener('input', validateReference);
            validateReference();
        });
    });
</script>
@endpush
