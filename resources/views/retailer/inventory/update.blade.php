@extends('layouts.dashboard')

@section('title', 'Sasisha Hisa')
@section('page-title', isset($inventory) ? 'Hariri Hisa' : 'Ongeza Hisa Mpya')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold"><i class="bi bi-pencil-square me-2"></i>Maelezo ya Hisa</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('retailer.inventory.store') }}">
                    @csrf
                    @if(isset($inventory))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="product_id" class="form-label">Chagua Bidhaa</label>
                        <select class="form-select" id="product_id" name="product_id" required {{ isset($inventory) ? 'disabled' : '' }}>
                            <option value="">-- Chagua Bidhaa --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                    {{ old('product_id', $inventory->product_id ?? '') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->weight_kg }}kg) - 
                                    {{ $product->service_type == 'new_cylinder' ? 'Mtungi Mpya' : 'Refill' }}
                                </option>
                            @endforeach
                        </select>
                        @if(isset($inventory))
                            <input type="hidden" name="product_id" value="{{ $inventory->product_id }}">
                            <small class="text-muted">Bidhaa haiwezi kubadilishwa wakati wa kuhariri.</small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label for="service_type" class="form-label">Aina ya Huduma</label>
                        <select class="form-select" id="service_type" name="service_type" required {{ isset($inventory) ? 'disabled' : '' }}>
                            <option value="">-- Chagua --</option>
                            <option value="new_cylinder" {{ old('service_type', $inventory->product->service_type ?? '') == 'new_cylinder' ? 'selected' : '' }}>Mtungi Mpya</option>
                            <option value="refill_exchange" {{ old('service_type', $inventory->product->service_type ?? '') == 'refill_exchange' ? 'selected' : '' }}>Kubadilisha (Refill)</option>
                        </select>
                        @if(isset($inventory))
                            <input type="hidden" name="service_type" value="{{ $inventory->product->service_type }}">
                        @endif
                        <small class="text-muted">Hii itaonekana kwa wateja wanapotafuta.</small>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Idadi (Stock)</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" 
                               value="{{ old('quantity', $inventory->quantity ?? 0) }}" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="price_override" class="form-label">Bei ya Kuuza (TZS) - Hiari</label>
                        <input type="number" class="form-control" id="price_override" name="price_override" 
                               value="{{ old('price_override', $inventory->price_override ?? '') }}" 
                               placeholder="Acha tupu kutumia bei ya mwongozo">
                        <small class="text-muted">Kama utaacha wazi, mfumo utatumia bei iliyowekwa na Admin.</small>
                    </div>

                    {{-- Quick Info --}}
                    @if(isset($inventory) && $inventory->product)
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        Bei ya mwongozo: 
                        <strong>TZS {{ number_format($inventory->product->suggested_retail_price) }}</strong>
                    </div>
                    @endif

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> {{ isset($inventory) ? 'Sasisha' : 'Hifadhi' }} Hisa
                        </button>
                        <a href="{{ route('retailer.inventory.index') }}" class="btn btn-outline-secondary">
                            Ghairi
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Quick Tip --}}
        <div class="card border-0 shadow-sm rounded-4 mt-3">
            <div class="card-body">
                <h6 class="fw-bold"><i class="bi bi-lightbulb text-warning me-2"></i>Kidokezo</h6>
                <p class="mb-0 small text-muted">
                    Unahitaji kuongeza hisa nyingi? Tumia kitufe cha 
                    <a href="{{ route('retailer.procurement.browse') }}" class="text-primary">Nunua kwa Jumla</a> 
                    kuagiza moja kwa moja kutoka kwa Wholesaler.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection