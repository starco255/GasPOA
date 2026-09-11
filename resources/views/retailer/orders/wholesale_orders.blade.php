@extends('layouts.dashboard')

@section('title', 'Maagizo ya Jumla')
@section('page-title', 'Maagizo Yangu ya Jumla')

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <h5 class="fw-bold"><i class="bi bi-truck me-2"></i>Orodha ya Maagizo ya Jumla</h5>
    </div>
    <div class="card-body p-0">
        @if(isset($orders) && count($orders) > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Agizo #</th>
                            <th>Muuzaji Jumla</th>
                            <th>Bidhaa</th>
                            <th>Jumla (TZS)</th>
                            <th>Hali</th>
                            <th>Tarehe</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>{{ $order['number'] }}</td>
                            <td>{{ $order['wholesaler'] }}</td>
                            <td>{{ $order['items'] }}</td>
                            <td>{{ number_format($order['total']) }}</td>
                            <td>
                                @if($order['status'] == 'dispatched')
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">Imesafirishwa</span>
                                @elseif($order['status'] == 'delivered')
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">Imekamilika</span>
                                @elseif($order['status'] == 'cancelled')
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">Imefutwa</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">{{ ucfirst($order['status']) }}</span>
                                @endif
                            </td>
                            <td>{{ $order['created_at'] }}</td>
                            <td>
                                @if(in_array($order['status'], ['dispatched', 'confirmed', 'processing']))
                                    <a href="{{ route('retailer.orders.wholesale.tracking', $order['id']) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="bi bi-geo-alt"></i> Fuatilia
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h5 class="mt-3">Hakuna Maagizo ya Jumla</h5>
                <p class="text-muted">Bado hujaweka maagizo yoyote ya jumla.</p>
            </div>
        @endif
    </div>
</div>
{{-- ✅ KITUFE KIPYA CHINI --}}
<div class="text-center mt-4">
    <a href="{{ route('retailer.procurement.browse') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
        <i class="bi bi-cart-plus me-2"></i> Nunua Bidhaa za Jumla
    </a>
</div>
@endsection