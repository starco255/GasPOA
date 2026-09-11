@extends('layouts.dashboard')

@section('title', 'Dashboard ya Jumla')
@section('page-title', 'Habari, ' . Auth::user()->full_name . '! 🏢')

@section('content')
{{-- Stats Cards --}}
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-cash-stack fs-3 text-primary"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Mauzo ya Wiki Hii</h6>
                        <h3 class="mb-0">TZS {{ number_format($weeklySales ?? 0) }}</h3>
                        @if(($salesChange ?? 0) > 0)
                            <small class="text-success"><i class="bi bi-arrow-up"></i> {{ $salesChange }}%</small>
                        @elseif(($salesChange ?? 0) < 0)
                            <small class="text-danger"><i class="bi bi-arrow-down"></i> {{ abs($salesChange) }}%</small>
                        @else
                            <small class="text-muted">Hakuna mabadiliko</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-inbox fs-3 text-warning"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Maagizo Mapya</h6>
                        <h3 class="mb-0">{{ $newOrdersCount ?? 0 }}</h3>
                        <small class="text-warning">Yanasubiri kukubaliwa</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-people fs-3 text-success"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Wauzaji Rejareja</h6>
                        <h3 class="mb-0">{{ $activeRetailersCount ?? 0 }}</h3>
                        <small class="text-muted">Walio hai</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-box-seam fs-3 text-info"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Ghala Langu</h6>
                        <h3 class="mb-0">{{ number_format($totalStock ?? 0) }}</h3>
                        <small class="text-muted">Jumla ya mitungi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Stats Row --}}
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Mauzo Mwezi Huu</h6>
                        <h4 class="mb-0">TZS {{ number_format($monthlySales ?? 0) }}</h4>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Idadi ya Maagizo</h6>
                        <h4 class="mb-0">{{ $totalOrdersMonth ?? 0 }}</h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-calendar-check fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Bidhaa Zinazouzwa Sana</h6>
                        <div class="mt-2">
                            @forelse($topProducts ?? [] as $index => $product)
                                <span class="badge bg-light text-dark me-2 mb-1">
                                    {{ $index + 1 }}. {{ $product->name }} ({{ $product->total_sold }})
                                </span>
                            @empty
                                <span class="text-muted">Hakuna mauzo bado</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-trophy fs-3 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Maagizo Mapya Yanayosubiri --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold"><i class="bi bi-bell me-2 text-primary"></i>Maagizo Mapya kutoka kwa Wauzaji </h5>
                <a href="{{ route('wholesaler.orders.incoming') }}" class="text-decoration-none">Ona Yote <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Duka</th>
                                <th>Bidhaa</th>
                                <th>Jumla (TZS)</th>
                                <th>Muda</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- SULUHISHO: Tumia take() kwa Collection badala ya array_slice() --}}
                            @php
                                // Ikiwa ni Collection, tumia take(2). Ikiwa ni array, badilisha kwanza.
                                if ($incomingOrders instanceof \Illuminate\Support\Collection) {
                                    $sampleOrders = $incomingOrders->take(2);
                                } else {
                                    $sampleOrders = collect($incomingOrders ?? [])->take(2);
                                }
                            @endphp
                            
                            @forelse($sampleOrders as $order)
                            <tr>
                                <td class="fw-medium">{{ $order['number'] ?? $order->number ?? '' }}</td>
                                <td>{{ $order['retailer'] ?? $order->retailer ?? '' }}</td>
                                <td>{{ Str::limit($order['items'] ?? $order->items ?? '', 30) }}</td>
                                <td>TZS {{ number_format($order['total'] ?? $order->total ?? 0) }}</td>
                                <td>{{ $order['time'] ?? $order->time ?? '' }}</td>
                                <td>
                                    <a href="{{ route('wholesaler.orders.incoming') }}" class="btn btn-sm btn-outline-primary">
                                        Shughulikia
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    Hakuna maagizo mapya kwa sasa.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Ikiwa kuna maagizo zaidi, onyesha hint --}}
                @php
                    $totalOrders = ($incomingOrders instanceof \Illuminate\Support\Collection) 
                        ? $incomingOrders->count() 
                        : count($incomingOrders ?? []);
                @endphp
                
            </div>
        </div>
    </div>

    {{-- Top Retailers --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-trophy me-2 text-warning"></i>Wauzaji Rejareja Wanaoongoza</h5>
            </div>
            <div class="card-body">
                @forelse($topRetailers ?? [] as $index => $retailer)
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0 me-3">
                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">#{{ $index + 1 }}</span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $retailer['name'] ?? $retailer->name ?? '' }}</h6>
                        <small class="text-muted">Maagizo: {{ $retailer['orders'] ?? $retailer->orders ?? 0 }}</small>
                    </div>
                    <div class="text-end">
                        <strong>TZS {{ number_format($retailer['total'] ?? $retailer->total ?? 0) }}</strong>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-trophy fs-1 d-block mb-2 opacity-50"></i>
                    Hakuna mauzo bado.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Bidhaa Zinazokaribia Kuisha --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Bidhaa Zinazokaribia Kuisha</h5>
                <a href="{{ route('wholesaler.products.index') }}" class="text-decoration-none">Bidhaa Zote <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="card-body">
                @if(count($lowStockItems ?? []) > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Bidhaa</th>
                                <th>Idadi</th>
                                <th>Kiwango cha Chini</th>
                                <th>Hali</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockItems as $item)
                            <tr>
                                <td>{{ $item['name'] ?? $item->name ?? '' }}</td>
                                <td>
                                    @if(($item['quantity'] ?? $item->quantity ?? 0) == 0)
                                        <span class="text-danger fw-bold">0</span>
                                    @else
                                        {{ $item['quantity'] ?? $item->quantity ?? 0 }}
                                    @endif
                                </td>
                                <td>{{ $item['min'] ?? $item->min ?? 0 }}</td>
                                <td>
                                    @if(($item['quantity'] ?? $item->quantity ?? 0) == 0)
                                        <span class="badge bg-danger">Imekwisha</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Agiza Upya</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-3 text-muted">
                    <i class="bi bi-check-circle fs-1 d-block mb-2 opacity-50"></i>
                    Hakuna bidhaa zinazokaribia kuisha.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
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
</style>
@endpush