@extends('layouts.dashboard')

@section('title', 'Maagizo Yanayoendelea')
@section('page-title', 'Maagizo Yanayoshughulikiwa Sasa')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold"><i class="bi bi-truck me-2 text-primary"></i>Maagizo Yanayoendelea ({{ isset($activeOrders) ? count($activeOrders) : 0 }})</h5>
                <p class="text-muted">Maagizo uliyokubali na yanayosubiri kufikishwa kwa wateja.</p>
            </div>
            <a href="{{ route('retailer.orders.incoming') }}" class="btn btn-outline-warning btn-sm">
                <i class="bi bi-bell"></i> Maagizo Mapya
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        @if(isset($activeOrders) && count($activeOrders) > 0)
            @foreach($activeOrders as $order)
            <div class="border-bottom p-4 order-row">
                <div class="row">
                    <div class="col-md-8">
                        {{-- Header ya Agizo --}}
                        <div class="d-flex align-items-center mb-2 flex-wrap">
                            <span class="fw-bold fs-5 me-3">{{ $order['order_number'] }}</span>
                            @if($order['status'] == 'accepted')
                                <span class="badge bg-info">Imekubaliwa</span>
                            @elseif($order['status'] == 'picked_up')
                                <span class="badge bg-primary">Imeshachukuliwa</span>
                            @elseif($order['status'] == 'out_for_delivery')
                                <span class="badge bg-warning text-dark">Njiani</span>
                            @endif
                            
                            @if(isset($order['is_urgent']) && $order['is_urgent'])
                                <span class="badge bg-danger ms-2">
                                    <i class="bi bi-lightning"></i> Haraka
                                </span>
                            @endif
                            
                            <span class="text-muted ms-auto small">
                                <i class="bi bi-clock"></i> {{ $order['created_at'] ?? 'Hivi karibuni' }}
                            </span>
                        </div>
                        
                        {{-- Maelezo ya Mteja na Agizo --}}
                        <div class="row">
                            <div class="col-sm-6">
                                <p class="mb-1"><i class="bi bi-person me-2"></i>{{ $order['customer'] ?? 'Mteja' }} ({{ $order['customer_phone'] ?? 'Haipo' }})</p>
                                <p class="mb-1"><i class="bi bi-geo-alt me-2"></i>{{ $order['address'] ?? 'Haipo' }}</p>
                                @if(isset($order['distance']))
                                    <p class="mb-1"><i class="bi bi-signpost me-2"></i>Umbali: {{ $order['distance'] }} km</p>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1"><i class="bi bi-box me-2"></i>{{ $order['service'] ?? 'Huduma' }}</p>
                                <p class="mb-1"><i class="bi bi-cash me-2"></i>Jumla: TZS {{ number_format($order['total'] ?? 0) }}</p>
                                
                                {{-- Orodha ya Bidhaa --}}
                                @if(isset($order['items']) && count($order['items']) > 0)
                                    <div class="mt-2">
                                        @foreach($order['items'] as $item)
                                            <span class="badge bg-light text-dark me-2 mb-1">
                                                {{ $item['name'] ?? 'Bidhaa' }} x{{ $item['quantity'] ?? 1 }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Muda wa Kufika --}}
                        <div class="alert alert-info py-2 mt-3">
                            <i class="bi bi-clock-fill me-2"></i>
                            <strong>Muda wa Kufika:</strong> 
                            @if(isset($order['estimated_delivery']))
                                @if(is_object($order['estimated_delivery']))
                                    {{ $order['estimated_delivery']->format('H:i') }} ({{ $order['estimated_delivery']->diffForHumans() }})
                                @else
                                    {{ $order['estimated_delivery'] }}
                                @endif
                            @else
                                Dakika 20
                            @endif
                        </div>
                        
                        @if(isset($order['accepted_at']))
                            <p class="text-muted small mb-0">
                                <i class="bi bi-check-circle me-1"></i> Imekubaliwa: {{ $order['accepted_at'] }}
                            </p>
                        @endif
                    </div>
            
                    {{-- Actions --}}
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="d-grid gap-2">
                            @if($order['status'] == 'accepted')
                                <form method="POST" action="{{ route('retailer.orders.pickup', $order['id']) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-box-arrow-up"></i> Nimeshachukua Stock
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('retailer.orders.deliver', $order['id']) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check2-circle"></i> Nimefikisha
                                    </button>
                                </form>
                            @endif
                            
                            {{-- CHAT BUTTON - IMEREKEBISHWA KWA GET ROUTE --}}
                            <a href="{{ route('retailer.chat.show', $order['id']) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-chat-dots"></i> Fungua Chat
                            </a>
                            
                            {{-- CALL BUTTON --}}
                            <a href="tel:{{ $order['customer_phone'] ?? '#' }}" class="btn btn-outline-primary">
                                <i class="bi bi-telephone"></i> Mpigie
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center py-5">
                <i class="bi bi-check-circle display-1 text-muted"></i>
                <h5 class="mt-3">Hakuna Maagizo Yanayoendelea</h5>
                <p class="text-muted">Huna maagizo yanayoshughulikiwa kwa sasa.</p>
                <a href="{{ route('retailer.orders.incoming') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-bell"></i> Tazama Maagizo Mapya
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Quick Stats --}}
@if(isset($activeOrders) && count($activeOrders) > 0)
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-info bg-opacity-10">
            <div class="card-body text-center py-3">
                <h6 class="text-muted mb-2">Imekubaliwa</h6>
                <h3 class="mb-0">{{ $activeOrders->where('status', 'accepted')->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10">
            <div class="card-body text-center py-3">
                <h6 class="text-muted mb-2">Imeshachukuliwa</h6>
                <h3 class="mb-0">{{ $activeOrders->where('status', 'picked_up')->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10">
            <div class="card-body text-center py-3">
                <h6 class="text-muted mb-2">Njiani</h6>
                <h3 class="mb-0">{{ $activeOrders->where('status', 'out_for_delivery')->count() }}</h3>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .badge.bg-info { background-color: #e6f3ff !important; color: #0056b3 !important; }
    .badge.bg-primary { background-color: #cfe2ff !important; color: #084298 !important; }
    .badge.bg-warning { background-color: #fff3cd !important; color: #856404 !important; }
    .badge.bg-danger { background-color: #f8d7da !important; color: #842029 !important; }
    .badge.bg-success { background-color: #d1e7dd !important; color: #0f5132 !important; }
    .bg-opacity-10 { --bs-bg-opacity: 0.1; }
    .alert-info { background-color: #cff4fc; border-color: #b6effb; color: #055160; }
    .order-row:hover { background-color: #fafbfc; }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirm before pickup
        document.querySelectorAll('form[action*="pickup"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Una uhakika umeshachukua stock kwa agizo hili?')) {
                    e.prevent Default();
                }
            }); 
        });
        
        // Confirm before delivery
        document.querySelectorAll('form[action*="deliver"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Una uhakika umefikisha agizo hili kwa mteja?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush