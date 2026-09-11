@props(['order'])

<div class="card border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h6 class="fw-bold mb-1">{{ $order['order_number'] ?? 'GPOA-XXXX' }}</h6>
                <p class="text-muted small mb-1">
                    <i class="bi bi-person"></i> {{ $order['customer_name'] ?? 'Mteja' }}
                </p>
                <p class="text-muted small mb-1">
                    <i class="bi bi-box"></i> {{ $order['service'] ?? 'Huduma' }}
                </p>
                <p class="text-muted small mb-0">
                    <i class="bi bi-geo-alt"></i> {{ \Illuminate\Support\Str::limit($order['address'] ?? 'Anwani', 30) }}
                </p>
            </div>
            <div class="text-end">
                <span class="badge 
                    @if(($order['status'] ?? '') == 'delivered') bg-success 
                    @elseif(($order['status'] ?? '') == 'cancelled') bg-danger 
                    @elseif(($order['status'] ?? '') == 'out_for_delivery') bg-warning text-dark 
                    @else bg-info @endif">
                    {{ ucfirst(str_replace('_', ' ', $order['status'] ?? 'pending')) }}
                </span>
                <h6 class="mt-2 mb-0">TZS {{ number_format($order['total'] ?? 0) }}</h6>
                <small class="text-muted">{{ $order['created_at'] ?? 'Hivi karibuni' }}</small>
            </div>
        </div>
        {{$slot ?? ''}}
    </div>
</div>