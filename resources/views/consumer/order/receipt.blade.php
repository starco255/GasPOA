@extends('layouts.dashboard')

@section('title', 'Risiti ya Agizo')
@section('page-title', 'Risiti #' . $order->order_number)

@section('content')
@php
    $isUrgent = ($order->urgency_level ?? 'normal') === 'urgent';
    
    $paymentMethodLabels = [
        'cash' => 'Pesa Taslimu',
        'mobile_money' => 'M-Pesa/TigoPesa/Airtel Money',
        'card' => 'Kadi ya Benki',
    ];
    $paymentMethod = $paymentMethodLabels[$order->payment_method] ?? ucfirst($order->payment_method);
@endphp

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4" id="receipt-content">
            <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
                <h4 class="fw-bold text-primary"><i class="bi bi-fire"></i> GasPOA Market</h4>
                <p class="text-muted mb-0">Risiti ya Agizo</p>
                <hr>
            </div>
            <div class="card-body">
                {{-- Maelezo ya Agizo --}}
                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted">Namba ya Agizo:</small>
                        <h6 class="fw-bold">{{ $order->order_number }}</h6>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted">Tarehe:</small>
                        <h6>{{ $order->created_at ? $order->created_at->format('d M Y, H:i') : 'Hivi karibuni' }}</h6>
                    </div>
                </div>

                {{-- Maelezo ya Mteja --}}
                <div class="mb-4">
                    <small class="text-muted">Mteja:</small>
                    <h6>{{ $order->consumer->full_name ?? 'Mteja' }}</h6>
                    <p class="mb-1">{{ $order->consumer->phone_number ?? '' }}</p>
                    <p class="mb-0">{{ $order->consumer->email ?? '' }}</p>
                </div>

                {{-- Maelezo ya Muuzaji --}}
                <div class="mb-4">
                    <small class="text-muted">Muuzaji:</small>
                    <h6>{{ $order->retailer->business_name ?? 'Haijulikani' }}</h6>
                    <p class="mb-1">{{ $order->retailer->physical_address ?? '' }}</p>
                    <p class="mb-0">{{ $order->retailer->user->phone_number ?? '' }}</p>
                </div>

                {{-- Anwani ya Ufikishaji --}}
                <div class="mb-4">
                    <small class="text-muted">Anwani ya Kufikishia:</small>
                    <p class="mb-0">{{ $order->delivery_address }}</p>
                </div>

                {{-- Bidhaa Zilizoagizwa --}}
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Bidhaa</th>
                                <th>Idadi</th>
                                <th>Bei/Unit</th>
                                <th>Jumla</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'Bidhaa' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>TZS {{ number_format($item->price_per_item) }}</td>
                                <td>TZS {{ number_format($item->price_per_item * $item->quantity) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            @php
                                $subtotal = $order->items->sum(function($item) {
                                    return $item->price_per_item * $item->quantity;
                                });
                            @endphp
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Jumla ya Bidhaa:</td>
                                <td>TZS {{ number_format($subtotal) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end">Usafirishaji:</td>
                                <td>TZS 0 (Bure)</td>
                            </tr>
                            @if($isUrgent)
                            <tr>
                                <td colspan="3" class="text-end">Ada ya Haraka:</td>
                                <td>TZS 3,000</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Jumla Kuu:</td>
                                <td class="fw-bold">TZS {{ number_format($order->total_amount) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Maelezo ya Malipo --}}
                <div class="row mb-4">
                    <div class="col-6">
                        <small class="text-muted">Njia ya Malipo:</small>
                        <h6>{{ $paymentMethod }}</h6>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted">Hali ya Malipo:</small>
                        <h6>
                            <span class="badge {{ $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                {{ $order->payment_status == 'paid' ? 'Imelipwa' : 'Haijalipwa' }}
                            </span>
                        </h6>
                    </div>
                </div>

                {{-- Footer --}}
                <hr>
                <p class="text-center text-muted small mb-0">
                    Asante kwa kutumia GasPOA Market!<br>
                    Kwa msaada zaidi, piga *150*99# au wasiliana nasi.
                </p>
            </div>
        </div>

        {{-- Vitufe --}}
        <div class="d-flex gap-2 justify-content-between mt-4 mb-4">
            <a href="{{ route('consumer.history') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Rudi kwenye Historia
            </a>
            <div>
                <button class="btn btn-outline-primary me-2" onclick="window.print()">
                    <i class="bi bi-printer"></i> Chapisha
                </button>
                <a href="{{ route('consumer.order.details', $order->id) }}" class="btn btn-primary">
                    <i class="bi bi-eye"></i> Tazama Maelezo
                </a>
            </div>
        </div>

        <button class="btn btn-primary" onclick="window.print()">
    <i class="bi bi-printer"></i> Chapisha / Hifadhi kama PDF
</button>



    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .sidebar, .topbar, .btn, .mobile-menu, .card-header a {
            display: none !important;
        }
        #receipt-content {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
    .badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
    }

    @media print {
    .sidebar, .topbar, .btn, .mobile-menu, .card-header a, .dropdown, footer {
        display: none !important;
    }
    #receipt-content {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        padding: 20px !important;
    }
}
</style>
@endpush