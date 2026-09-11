@extends('layouts.dashboard')

@section('title', 'Hisa ya Duka')
@section('page-title', 'Simamia Hisa Zako')

@section('content')
{{-- Quick Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10">
            <div class="card-body py-3">
                <h6 class="text-muted mb-1">Jumla ya Bidhaa</h6>
                <h3 class="mb-0">{{ count($inventory ?? []) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10">
            <div class="card-body py-3">
                <h6 class="text-muted mb-1">Hisa Chache</h6>
                <h3 class="mb-0">{{ $lowStockCount ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-success bg-opacity-10">
            <div class="card-body py-3">
                <h6 class="text-muted mb-1">Jumla ya Vitu</h6>
                <h3 class="mb-0">{{ $inventory->sum('qty') ?? 0 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-info bg-opacity-10">
            <div class="card-body py-3">
                <h6 class="text-muted mb-1">Thamani ya Hisa</h6>
                <h3 class="mb-0">TZS {{ number_format($inventory->sum(function($i) { return $i['qty'] * $i['retail']; }) ?? 0) }}</h3>
            </div>
        </div>
    </div>
</div>

{{-- Actions Row --}}
<div class="d-flex gap-2 mb-3">
    <a href="{{ route('retailer.inventory.update') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Ongeza / Sasisha Hisa
    </a>
    <a href="{{ route('retailer.procurement.browse') }}" class="btn btn-outline-success">
        <i class="bi bi-cart-plus"></i> Nunua kwa Jumla (Wholesaler)
    </a>
</div>

{{-- Inventory Table --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i>Bidhaa Zote Dukani</h5>
            </div>
            <div class="col-md-6 text-md-end">
                <small class="text-muted">
                    <i class="bi bi-info-circle"></i> Bonyeza <strong>Nunua kwa Jumla</strong> kuagiza kutoka kwa Wholesaler
                </small>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Picha</th>
                        <th>Bidhaa</th>
                        <th>Aina ya Huduma</th>
                        <th>Idadi</th>
                        <th>Bei ya Rejareja (TZS)</th>
                        <th>Bei ya Jumla (TZS)</th>
                        <th>Ilivyosasishwa</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventory ?? [] as $item)
                    <tr>
                        <td>
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" 
                                 style="width: 40px; height: 40px;">
                                <i class="bi bi-box text-secondary"></i>
                            </div>
                        </td>
                        <td class="fw-medium">{{ $item['name'] }}</td>
                        <td>
                            @if($item['type'] == 'new_cylinder')
                                <span class="badge bg-warning text-dark">Mtungi Mpya</span>
                            @else
                                <span class="badge bg-success">Kubadilisha (Refill)</span>
                            @endif
                        </td>
                        <td>
                            @if($item['is_low_stock'])
                                <span class="text-danger fw-bold">{{ $item['qty'] }}</span>
                                <i class="bi bi-exclamation-triangle-fill text-warning ms-1" title="Hisa chache"></i>
                            @else
                                {{ $item['qty'] }}
                            @endif
                        </td>
                        <td>{{ number_format($item['retail']) }}</td>
                        <td>{{ number_format($item['wholesale']) }}</td>
                        <td>{{ $item['updated'] }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('retailer.inventory.update', ['id' => $item['id']]) }}">
                                            <i class="bi bi-pencil"></i> Hariri
                                        </a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('retailer.inventory.destroy', $item['id']) }}" 
                                              onsubmit="return confirm('Una uhakika unataka kufuta hisa hii?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash"></i> Futa
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam display-4 opacity-50 mb-3 d-block"></i>
                            <h5>Hakuna Hisa Dukani</h5>
                            <p>Bado hujaongeza bidhaa zozote kwenye hisa yako.</p>
                            <a href="{{ route('retailer.inventory.update') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle"></i> Ongeza Hisa Sasa
                            </a>
                            <p class="mt-3">au</p>
                            <a href="{{ route('retailer.procurement.browse') }}" class="btn btn-outline-success">
                                <i class="bi bi-cart-plus"></i> Nunua kutoka kwa Wholesaler
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">Jumla ya bidhaa: {{ count($inventory ?? []) }} aina</small>
            @if($lowStockCount > 0)
                <a href="{{ route('retailer.procurement.browse') }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-exclamation-triangle"></i> Agiza Hisa Chache ({{ $lowStockCount }})
                </a>
            @endif
        </div>
    </div>
</div>

{{-- Recent Wholesale Orders --}}
@if(isset($recentWholesaleOrders) && count($recentWholesaleOrders) > 0)
<div class="card border-0 shadow-sm rounded-4 mt-4">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <h5 class="fw-bold"><i class="bi bi-truck me-2"></i>Maagizo ya Hivi Karibuni kutoka kwa Wholesaler</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Namba</th>
                        <th>Wholesaler</th>
                        <th>Jumla</th>
                        <th>Hali</th>
                        <th>Tarehe</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentWholesaleOrders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->wholesaler->business_name ?? 'Wholesaler' }}</td>
                        <td>TZS {{ number_format($order->total_amount) }}</td>
                        <td>
                            @if($order->status == 'delivered')
                                <span class="badge bg-success">Imepokelewa</span>
                            @elseif($order->status == 'processing')
                                <span class="badge bg-warning">Inashughulikiwa</span>
                            @else
                                <span class="badge bg-info">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .bg-opacity-10 { --bs-bg-opacity: 0.1; }
    .table tbody tr:hover { background-color: #fafbfc; }
</style>
@endpush