@extends('layouts.dashboard')

@section('title', 'Historia ya Maagizo')
@section('page-title', 'Historia ya Maagizo Yangu')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-clock-history me-2"></i>Maagizo Yote 
                    <span class="badge bg-primary ms-2">{{ $orders->total() ?? 0 }}</span>
                </h5>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-md-end mt-2 mt-md-0">
                    <select class="form-select form-select-sm w-auto" id="filterStatus">
                        <option value="">Hali Zote</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Inasubiri</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Imekubaliwa</option>
                        <option value="picked_up" {{ request('status') == 'picked_up' ? 'selected' : '' }}>Imeshachukuliwa</option>
                        <option value="out_for_delivery" {{ request('status') == 'out_for_delivery' ? 'selected' : '' }}>Njiani</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Imekamilika</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Imefutwa</option>
                    </select>
                    <form method="GET" action="{{ route('consumer.history') }}" class="d-flex ms-2">
                        <input type="text" name="search" class="form-control form-control-sm w-auto" 
                               placeholder="Tafuta agizo..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-secondary btn-sm ms-1">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Namba ya Agizo</th>
                        <th>Tarehe</th>
                        <th>Bidhaa</th>
                        <th>Kasi</th>
                        <th>Jumla (TZS)</th>
                        <th>Hali</th>
                        <th>Muuzaji</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders ?? [] as $order)
                    @php
                        $productNames = $order->items->map(function($item) {
                            return $item->quantity . 'x ' . ($item->product->name ?? 'Bidhaa');
                        })->join(', ');
                        
                        $productName = $productNames ?: 'Bidhaa';
                        
                        $statusBadges = [
                            'pending' => 'bg-secondary',
                            'accepted' => 'bg-info',
                            'picked_up' => 'bg-primary',
                            'out_for_delivery' => 'bg-warning text-dark',
                            'delivered' => 'bg-success',
                            'cancelled' => 'bg-danger',
                        ];
                        $statusBadge = $statusBadges[$order->status] ?? 'bg-secondary';
                        
                        $statusLabels = [
                            'pending' => 'Inasubiri',
                            'accepted' => 'Imekubaliwa',
                            'picked_up' => 'Imeshachukuliwa',
                            'out_for_delivery' => 'Njiani',
                            'delivered' => 'Imekamilika',
                            'cancelled' => 'Imefutwa',
                        ];
                        $statusLabel = $statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status));
                        
                        $retailerName = $order->retailer->business_name ?? 'Haijulikani';
                        $orderDate = $order->created_at ? $order->created_at->format('d M Y') : 'Hivi karibuni';
                        $orderTotal = $order->total_amount ?? 0;
                        
                        $urgencyLevel = $order->urgency_level ?? 'normal';
                        $urgencyBadge = $urgencyLevel === 'urgent' ? 'bg-danger' : 'bg-secondary';
                        $urgencyLabel = $urgencyLevel === 'urgent' ? 'Haraka' : 'Kawaida';
                    @endphp
                    <tr data-status="{{ $order->status }}">
                        <td class="fw-medium">{{ $order->order_number }}</td>
                        <td>{{ $orderDate }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($productName, 20) }}</td>
                        <td>
                            <span class="badge {{ $urgencyBadge }}">{{ $urgencyLabel }}</span>
                        </td>
                        <td>TZS {{ number_format($orderTotal) }}</td>
                        <td>
                            <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($retailerName, 12) }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('consumer.order.details', $order->id) }}">
                                            <i class="bi bi-eye"></i> Tazama Maelezo
                                        </a>
                                    </li>
                                    @if(in_array($order->status, ['pending', 'accepted', 'picked_up', 'out_for_delivery']))
                                    <li>
                                        <a class="dropdown-item" href="{{ route('consumer.order.tracking', $order->id) }}">
                                            <i class="bi bi-geo-alt"></i> Fuatilia
                                        </a>
                                    </li>
                                    @endif
                                    <li>
                                        @if($order->payment_status === 'paid')
                                            <a class="dropdown-item" href="{{ route('consumer.order.receipt', $order->id) }}"><i class="bi bi-receipt"></i> Pakua Risiti</a>
                                        @else
                                            <span class="dropdown-item disabled text-muted" aria-disabled="true" title="Risiti hupatikana baada ya malipo kuthibitishwa."><i class="bi bi-lock"></i> Pakua Risiti</span>
                                        @endif
                                    </li>
                                    @if($order->status == 'delivered')
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $order->id }}">
                                            <i class="bi bi-star"></i> Toa Maoni
                                        </a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('consumer.order.reorder', $order->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-arrow-repeat"></i> Agiza Tena
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                    @if(in_array($order->status, ['pending', 'accepted']))
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('consumer.order.cancel', $order->id) }}" 
                                              onsubmit="return confirm('Una uhakika unataka kufuta agizo hili?');">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-x-circle"></i> Futa Agizo
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-4 opacity-50 mb-3 d-block"></i>
                            <h5>Hakuna Maagizo</h5>
                            <p>Bado hujaweka agizo lolote.</p>
                            <a href="{{ route('consumer.order.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle"></i> Agiza Sasa
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- PAGINATION FOOTER --}}
    <div class="card-footer bg-white border-0 py-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="pagination-info text-muted small">
                @if(method_exists($orders, 'total') && $orders->total() > 0)
                    @php
                        $from = $orders->firstItem() ?? 0;
                        $to = $orders->lastItem() ?? 0;
                        $total = $orders->total();
                    @endphp
                    Inaonyesha {{ $from }} - {{ $to }} kati ya {{ $total }} matokeo
                @endif
            </div>
            
            @if(method_exists($orders, 'hasPages') && $orders->hasPages())
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                    {{-- Previous Page Link --}}
                    @if($orders->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    @endif
                    
                    {{-- Pagination Elements --}}
                    @foreach($orders->links()->elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if(is_string($element))
                            <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                        @endif
                        
                        {{-- Array Of Links --}}
                        @if(is_array($element))
                            @foreach($element as $page => $url)
                                @if($page == $orders->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                    
                    {{-- Next Page Link --}}
                    @if($orders->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                        </li>
                    @endif
                </ul>
            </nav>
            @endif
        </div>
    </div>
</div>

{{-- Review Modal kwa kila order iliyokamilika --}}
@if(isset($orders) && method_exists($orders, 'count') && $orders->count() > 0)
    @foreach($orders as $order)
        @if($order->status == 'delivered')
        <div class="modal fade" id="reviewModal{{ $order->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('consumer.order.review', $order->id) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Toa Maoni kwa Agizo #{{ $order->order_number }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Ukadiriaji (Nyota)</label>
                                <select name="rating" class="form-select" required>
                                    <option value="">Chagua...</option>
                                    <option value="5">⭐⭐⭐⭐⭐ (5) - Bora</option>
                                    <option value="4">⭐⭐⭐⭐ (4) - Nzuri</option>
                                    <option value="3">⭐⭐⭐ (3) - Wastani</option>
                                    <option value="2">⭐⭐ (2) - Chini ya Wastani</option>
                                    <option value="1">⭐ (1) - Mbaya</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Maoni Yako</label>
                                <textarea name="review" class="form-control" rows="3" 
                                          placeholder="Andika maoni yako kuhusu huduma..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ghairi</button>
                            <button type="submit" class="btn btn-primary">Tuma Maoni</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endforeach
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterSelect = document.getElementById('filterStatus');
        if (filterSelect) {
            filterSelect.addEventListener('change', function() {
                const status = this.value;
                const url = new URL(window.location.href);
                if (status) {
                    url.searchParams.set('status', status);
                } else {
                    url.searchParams.delete('status');
                }
                window.location.href = url.toString();
            });
        }
        
        let searchTimeout;
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.form.submit();
                }, 500);
            });
        }
        
        const currentStatus = new URLSearchParams(window.location.search).get('status');
        if (currentStatus) {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                if (row.dataset.status === currentStatus) {
                    row.classList.add('table-active');
                }
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .table-active {
        background-color: #e7f1ff !important;
    }
    .badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
        font-weight: 500;
    }
    
    /* ===== PAGINATION INFO ===== */
    .pagination-info {
        color: #6c757d;
        font-size: 0.75rem;
    }
    
    /* ===== CUSTOM PAGINATION STYLES ===== */
    .pagination {
        gap: 4px;
    }
    
    .pagination .page-link {
        border-radius: 6px !important;
        padding: 5px 10px;
        font-size: 0.75rem;
        font-weight: 500;
        color: #495057;
        background-color: white;
        border: 1px solid #dee2e6;
        transition: all 0.15s ease;
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(145deg, #FF6B35, #E85D2C);
        border-color: #FF6B35;
        color: white;
        box-shadow: 0 2px 6px rgba(255, 107, 53, 0.2);
    }
    
    .pagination .page-link:hover {
        background-color: #fff3ef;
        border-color: #FF6B35;
        color: #FF6B35;
    }
    
    .pagination .page-item.active .page-link:hover {
        background: linear-gradient(145deg, #E85D2C, #d44f20);
        color: white;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #adb5bd;
        background-color: #f8f9fa;
        border-color: #e9ecef;
        opacity: 0.6;
    }
    
    /* Responsive */
    @media (max-width: 576px) {
        .pagination .page-link {
            padding: 4px 8px;
            font-size: 0.7rem;
        }
        .pagination-info {
            font-size: 0.7rem;
            text-align: center;
        }
    }
</style>
@endpush
