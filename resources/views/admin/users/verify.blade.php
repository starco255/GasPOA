@extends('layouts.admin')

@section('title', 'Uidhinishaji wa Biashara')
@section('page-title', 'Akaunti Zinazosubiri Uidhinishaji')

@section('content')

{{-- Quick Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10">
            <div class="card-body text-center py-3">
                <h4 class="mb-0 text-warning">{{ $totalPending ?? 0 }}</h4>
                <small class="text-muted">Zinasubiri Uidhinishaji</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-success bg-opacity-10">
            <div class="card-body text-center py-3">
                <h4 class="mb-0 text-success">{{ $pending->where('is_open', true)->count() ?? 0 }}</h4>
                <small class="text-muted">Zimeidhinishwa</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10">
            <div class="card-body text-center py-3">
                <h4 class="mb-0 text-primary">{{ $pending->where('business_type', 'retailer')->count() ?? 0 }}</h4>
                <small class="text-muted">Wauzaji Rejareja</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 bg-info bg-opacity-10">
            <div class="card-body text-center py-3">
                <h4 class="mb-0 text-info">{{ $pending->where('business_type', 'wholesaler')->count() ?? 0 }}</h4>
                <small class="text-muted">Wauzaji Jumla</small>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 pb-3">
        <form method="GET" action="{{ route('admin.users.verify') }}" id="verifyFilterForm">
            <div class="row align-items-center">
                <div class="col-md-4 mb-2 mb-md-0">
                    <select class="form-select" name="type" onchange="document.getElementById('verifyFilterForm').submit()">
                        <option value="">Aina Zote za Biashara</option>
                        <option value="retailer" {{ request('type') == 'retailer' ? 'selected' : '' }}>Wauzaji Rejareja</option>
                        <option value="wholesaler" {{ request('type') == 'wholesaler' ? 'selected' : '' }}>Wauzaji Jumla</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <select class="form-select" name="status" onchange="document.getElementById('verifyFilterForm').submit()">
                        <option value="">Hali Zote</option>
                        <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>Zisizoidhinishwa</option>
                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Zimeidhinishwa</option>
                    </select>
                </div>
                <div class="col-md-4 text-md-end">
                    @if(request()->filled('type') || request()->filled('status'))
                        <a href="{{ route('admin.users.verify') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i> Futa Vichujio
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 pb-0">
        <h5 class="fw-bold"><i class="bi bi-check-circle me-2 text-warning"></i>Maombi ya Biashara ({{ $pending->total() ?? 0 }})</h5>
    </div>
    <div class="card-body p-0">
        @forelse($pending as $biz)
        <div class="border-bottom p-4">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex align-items-center mb-2">
                        <h6 class="fw-bold mb-0 me-2">{{ $biz->business_name }}</h6>
                        @if($biz->business_type == 'retailer')
                            <span class="badge bg-success">Muuza Rejareja</span>
                        @else
                            <span class="badge bg-primary">Muuza Jumla</span>
                        @endif
                        
                        @if($biz->is_open)
                            <span class="badge bg-success ms-2">Imeidhinishwa</span>
                        @else
                            <span class="badge bg-warning text-dark ms-2">Hajaidhinishwa</span>
                        @endif
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="mb-1"><i class="bi bi-person me-2"></i> Mmiliki: {{ $biz->user->full_name ?? 'Haijulikani' }}</p>
                            <p class="mb-1"><i class="bi bi-telephone me-2"></i> {{ $biz->user->phone_number ?? 'Haipo' }}</p>
                            <p class="mb-1"><i class="bi bi-envelope me-2"></i> {{ $biz->user->email ?? 'Haipo' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-1"><i class="bi bi-geo-alt me-2"></i> {{ $biz->physical_address ?? 'Haipo' }}</p>
                            <p class="mb-1"><i class="bi bi-file-text me-2"></i> TIN: {{ $biz->tinn_number ?? 'Haipo' }}</p>
                            <p class="mb-1">
                                <i class="bi bi-shop me-2"></i> 
                                @if($biz->can_deliver)
                                    <span class="text-success">Inatoa usafirishaji</span>
                                @else
                                    <span class="text-muted">Haijatoa usafirishaji</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <small class="text-muted">
                        <i class="bi bi-calendar me-1"></i> Ilisajiliwa: {{ $biz->created_at->format('d M Y, H:i') }}
                        ({{ $biz->created_at->diffForHumans() }})
                    </small>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="d-grid gap-2">
                        {{-- Tazama Maelezo Button --}}
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $biz->id }}">
                            <i class="bi bi-eye"></i> Tazama Maelezo Kamili
                        </button>
                        
                        {{-- Idhinisha / Kataa Buttons --}}
                        @if(!$biz->is_open)
                            <form method="POST" action="{{ route('admin.verify.approve', $biz->id ) }}">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Una uhakika unataka kuidhinisha biashara hii?')">
                                    <i class="bi bi-check-lg"></i> Idhinisha Biashara
                                </button>
                            </form>
                        @endif
                        
                        <form method="POST" action="{{ route('admin.verify.reject', $biz->id) }}">
                            @csrf
                            <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $biz->id }}">
                                <i class="bi bi-x-lg"></i> Kataa Biashara
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Details Modal --}}
        <div class="modal fade" id="detailsModal{{ $biz->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-building me-2"></i>
                            {{ $biz->business_name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">Maelezo ya Biashara</h6>
                                <p><strong>Jina:</strong> {{ $biz->business_name }}</p>
                                <p><strong>Aina:</strong> {{ $biz->business_type == 'retailer' ? 'Muuza Rejareja' : 'Muuza Jumla' }}</p>
                                <p><strong>TIN:</strong> {{ $biz->tinn_number ?? 'Haipo' }}</p>
                                <p><strong>Anwani:</strong> {{ $biz->physical_address ?? 'Haipo' }}</p>
                                <p><strong>GPS:</strong> {{ $biz->shop_latitude }}, {{ $biz->shop_longitude }}</p>
                                <p><strong>Eneo la Huduma:</strong> {{ $biz->service_radius_km ?? '0' }} km</p>
                                <p><strong>Usafirishaji:</strong> {{ $biz->can_deliver ? 'Ndiyo' : 'Hapana' }}</p>
                                @if($biz->can_deliver)
                                    <p><strong>Ada kwa km:</strong> TZS {{ number_format($biz->delivery_fee_per_km ?? 0) }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">Maelezo ya Mmiliki</h6>
                                <p><strong>Jina:</strong> {{ $biz->user->full_name ?? 'Haijulikani' }}</p>
                                <p><strong>Simu:</strong> {{ $biz->user->phone_number ?? 'Haipo' }}</p>
                                <p><strong>Barua Pepe:</strong> {{ $biz->user->email ?? 'Haipo' }}</p>
                                <p><strong>Aina ya Akaunti:</strong> {{ ucfirst($biz->user->user_type ?? 'N/A') }}</p>
                                <p><strong>Aliyejiunga:</strong> {{ $biz->user->created_at->format('d M Y') ?? 'N/A' }}</p>
                                <p>
                                    <strong>Hali:</strong> 
                                    @if($biz->is_open)
                                        <span class="badge bg-success">Imeidhinishwa</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Hajaidhinishwa</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        {{-- Njia za Malipo --}}
                        <hr>
                        <h6 class="fw-bold mb-3">Njia za Malipo Zinazokubaliwa</h6>
                        <div class="d-flex gap-3">
                            @if($biz->accept_cash)
                                <span class="badge bg-success p-2"><i class="bi bi-cash"></i> Pesa Taslimu</span>
                            @else
                                <span class="badge bg-secondary p-2"><i class="bi bi-cash"></i> Pesa Taslimu</span>
                            @endif
                            
                            @if($biz->accept_mobile_money)
                                <span class="badge bg-success p-2"><i class="bi bi-phone"></i> Lipa Namba</span>
                            @else
                                <span class="badge bg-secondary p-2"><i class="bi bi-phone"></i> Lipa Namba</span>
                            @endif
                            
                            @if($biz->accept_bank)
                                <span class="badge bg-success p-2"><i class="bi bi-bank"></i> Benki</span>
                            @else
                                <span class="badge bg-secondary p-2"><i class="bi bi-bank"></i> Benki</span>
                            @endif
                        </div>
                        
                        {{-- Namba za Malipo (kama zipo) --}}
                        @if($biz->accept_mobile_money)
                            <div class="row mt-3">
                                @if($biz->mpesa_number)
                                    <div class="col-md-6"><strong>M-Pesa:</strong> {{ $biz->mpesa_number }}</div>
                                @endif
                                @if($biz->halopesa_number)
                                    <div class="col-md-6"><strong>HaloPesa:</strong> {{ $biz->halopesa_number }}</div>
                                @endif
                                @if($biz->airtel_number)
                                    <div class="col-md-6"><strong>Airtel Money:</strong> {{ $biz->airtel_number }}</div>
                                @endif
                                @if($biz->mixx_number)
                                    <div class="col-md-6"><strong>Mixx:</strong> {{ $biz->mixx_number }}</div>
                                @endif
                            </div>
                        @endif
                        
                        @if($biz->accept_bank)
                            <div class="row mt-3">
                                <div class="col-md-4"><strong>Benki:</strong> {{ strtoupper($biz->bank_name ?? 'N/A') }}</div>
                                <div class="col-md-4"><strong>Akaunti:</strong> {{ $biz->bank_account_number ?? 'N/A' }}</div>
                                <div class="col-md-4"><strong>Jina:</strong> {{ $biz->bank_account_name ?? 'N/A' }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Funga</button>
                        @if(!$biz->is_open)
                            <form method="POST" action="{{ route('admin.verify.approve', $biz->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-lg"></i> Idhinisha
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Reject Modal --}}
        <div class="modal fade" id="rejectModal{{ $biz->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.verify.reject', $biz->id) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Thibitisha Kukataa Biashara</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Una uhakika unataka kukataa biashara ya <strong>{{ $biz->business_name }}</strong>?</p>
                            <label for="rejection_reason{{ $biz->id }}" class="form-label">Sababu (Hiari):</label>
                            <select class="form-select" name="rejection_reason" id="rejection_reason{{ $biz->id }}">
                                <option value="">-- Chagua Sababu --</option>
                                <option value="incomplete">Taarifa hazijakamilika</option>
                                <option value="duplicate">Biashara imeshasajiliwa</option>
                                <option value="invalid">Taarifa si sahihi</option>
                                <option value="other">Nyingine</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ghairi</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-x-lg"></i> Ndiyo, Kataa Biashara
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        @empty
        <div class="text-center py-5">
            <i class="bi bi-check-circle fs-1 text-muted"></i>
            <h5 class="mt-3">Hakuna Maombi ya Biashara</h5>
            <p class="text-muted">Biashara zote zimeshughulikiwa kwa sasa.</p>
        </div>
        @endforelse
    </div>
    
    {{-- Pagination --}}
    @if($pending->hasPages())
    <div class="card-footer bg-white py-3">
        {{ $pending->links() }}
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
    }
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }
    .pagination {
        margin-bottom: 0;
    }
</style>
@endpush