@extends('layouts.dashboard')

@section('title', 'Kikapu cha Manunuzi')
@section('page-title', 'Kikapu Changu')

@section('content')
<div class="row"><div class="col-lg-10 mx-auto"><div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-0"><h5 class="fw-bold"><i class="bi bi-cart3 me-2 text-primary"></i>Bidhaa Zilizomo Kikapuni</h5><p class="text-muted small">Kila wholesaler ana njia zake za malipo. Chagua moja kwa kila agizo.</p></div>
    <div class="card-body">
    @if(count($cartItems))
        <div class="table-responsive"><table class="table table-hover align-middle"><thead class="table-light"><tr><th>Muuzaji</th><th>Bidhaa</th><th>Idadi</th><th>Bei/Unit</th><th>Jumla</th><th></th></tr></thead><tbody>
        @foreach($cartItems as $key => $item)
        <tr><td class="fw-medium">{{ $item['wholesaler_name'] }}</td><td>{{ $item['product_name'] }}</td><td><form method="POST" action="{{ route('retailer.procurement.cart.update', $key) }}" class="d-inline-flex gap-2">@csrf @method('PUT')<input type="number" name="quantity" value="{{ $item['quantity'] }}" min="5" class="form-control form-control-sm" style="width:80px"><button class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-repeat"></i></button></form></td><td>TZS {{ number_format($item['price']) }}</td><td class="fw-bold">TZS {{ number_format($item['quantity'] * $item['price']) }}</td><td><form method="POST" action="{{ route('retailer.procurement.cart.remove', $key) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Ondoa bidhaa hii?')"><i class="bi bi-trash"></i></button></form></td></tr>
        @endforeach
        </tbody></table></div>

        @php($cartWholesalers = collect($cartItems)->groupBy('wholesaler_id'))
        <form method="POST" action="{{ route('retailer.procurement.checkout') }}" id="checkoutForm">@csrf
        <div class="row g-3 mt-2">
            <div class="col-lg-7">
            @foreach($cartWholesalers as $wholesalerId => $items)
                @php($methods = $wholesalerPaymentMethods[$wholesalerId] ?? [])
                <div class="card border-0 bg-light mb-3"><div class="card-body"><h6 class="fw-bold"><i class="bi bi-building me-2"></i>{{ $items->first()['wholesaler_name'] }}</h6><p class="small text-muted">Agizo: TZS {{ number_format($items->sum(fn($item) => $item['quantity'] * $item['price']) + 15000) }} (pamoja na usafirishaji TZS 15,000)</p>
                @forelse($methods as $method)
                    <label class="border rounded p-3 d-block mb-2 bg-white"><input class="form-check-input me-2" required type="radio" name="payment_methods[{{ $wholesalerId }}]" value="{{ $method['value'] }}"> <strong>{{ $method['label'] }}</strong>
                    @if(!empty($method['account']))<span class="d-block small text-muted ms-4">Lipa kupitia: {{ $method['account'] }}</span>@endif
                    @if(!empty($method['bank_name']))<span class="d-block small text-muted ms-4">{{ $method['bank_name'] }} — {{ $method['account_number'] }}<br>{{ $method['account_name'] }}</span>@endif
                    </label>
                @empty
                    <div class="alert alert-warning mb-0">Wholesaler huyu hajaweka njia ya malipo inayotumika. Wasiliana naye au ondoa bidhaa zake.</div>
                @endforelse
                </div></div>
            @endforeach
            </div>
            <div class="col-lg-5"><div class="card bg-light border-0"><div class="card-body"><h6 class="fw-bold">Muhtasari wa Agizo</h6><div class="d-flex justify-content-between mb-2"><span>Bidhaa:</span><span>TZS {{ number_format($subtotal) }}</span></div><div class="d-flex justify-content-between mb-2"><span>Usafirishaji:</span><span>TZS {{ number_format($deliveryFee) }}</span></div><hr><div class="d-flex justify-content-between fw-bold fs-5"><span>Jumla:</span><span class="text-primary">TZS {{ number_format($total) }}</span></div></div></div>
            <button type="submit" class="btn btn-success btn-lg w-100 mt-3" {{ collect($wholesalerPaymentMethods)->contains(fn($methods) => empty($methods)) ? 'disabled' : '' }}><i class="bi bi-check2-circle me-2"></i>Thibitisha Agizo</button><a href="{{ route('retailer.procurement.browse') }}" class="btn btn-outline-secondary w-100 mt-2">Endelea Kununua</a>
            <p class="small text-muted text-center mt-2">Kwa ClickPesa iliyosanidiwa utaelekezwa kwenye checkout salama; vinginevyo agizo hubaki pending hadi malipo yathibitishwe.</p></div>
        </div></form>
    @else
        <div class="text-center py-5"><i class="bi bi-cart-x display-1 text-muted"></i><h5 class="fw-bold mt-3">Kikapu ni Tupu</h5><a href="{{ route('retailer.procurement.browse') }}" class="btn btn-primary mt-2">Nenda Soko la Jumla</a></div>
    @endif
    </div>
</div></div></div>
@endsection
