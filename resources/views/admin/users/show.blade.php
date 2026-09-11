@extends('layouts.admin')

@section('title', 'Maelezo ya Mtumiaji')
@section('page-title', 'Maelezo ya Mtumiaji: ' . $user->full_name)

@section('content')
<div class="row">
    {{-- User Profile Card --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body text-center p-4">
                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                    <span class="fw-bold text-primary" style="font-size: 2rem;">{{ strtoupper(substr($user->full_name, 0, 1)) }}</span>
                </div>
                <h5 class="fw-bold mb-1">{{ $user->full_name }}</h5>
                <p class="text-muted mb-3">
                    @if($user->user_type == 'consumer')
                        <span class="badge bg-primary">Mtumiaji</span>
                    @elseif($user->user_type == 'retailer')
                        <span class="badge bg-success">Muuza Rejareja</span>
                    @elseif($user->user_type == 'wholesaler')
                        <span class="badge bg-warning text-dark">Muuza Jumla</span>
                    @elseif($user->user_type == 'admin')
                        <span class="badge bg-danger">Admin</span>
                    @endif
                </p>
                
                <div class="d-flex justify-content-center gap-2 mb-3">
                    @if($user->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Imefungwa</span>
                    @endif
                    
                    @if($user->is_phone_verified)
                        <span class="badge bg-info">Simu Imethibitishwa</span>
                    @else
                        <span class="badge bg-warning text-dark">Simu Haijathibitishwa</span>
                    @endif
                </div>
                
                @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">
                        @csrf
                        @if($user->is_active)
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-lock"></i> Funga Akaunti
                            </button>
                        @else
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-unlock"></i> Fungua Akaunti
                            </button>
                        @endif
                    </form>
                @endif
            </div>
        </div>
        
        {{-- Contact Info --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h6 class="fw-bold">Maelezo ya Mawasiliano</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><i class="bi bi-telephone me-2"></i> {{ $user->phone_number }}</p>
                <p class="mb-2"><i class="bi bi-envelope me-2"></i> {{ $user->email ?? 'Haipo' }}</p>
                <p class="mb-2"><i class="bi bi-calendar me-2"></i> Alijiunga: {{ $user->created_at->format('d M Y') }}</p>
                <p class="mb-0"><i class="bi bi-clock me-2"></i> {{ $user->created_at->diffForHumans() }}</p>
            </div>
        </div>
        
        {{-- Business Info (kwa retailers na wholesalers) --}}
        @if($user->businessProfile)
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h6 class="fw-bold">Maelezo ya Biashara</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>{{ $user->businessProfile->business_name }}</strong></p>
                <p class="mb-2"><i class="bi bi-geo-alt me-2"></i> {{ $user->businessProfile->physical_address ?? 'Haipo' }}</p>
                <p class="mb-2"><i class="bi bi-file-text me-2"></i> TIN: {{ $user->businessProfile->tinn_number ?? 'Haipo' }}</p>
                <p class="mb-0">
                    @if($user->businessProfile->is_open)
                        <span class="badge bg-success">Duka Liko Wazi</span>
                    @else
                        <span class="badge bg-secondary">Duka Limefungwa</span>
                    @endif
                </p>
            </div>
        </div>
        @endif
    </div>
    
    {{-- User Statistics --}}
    <div class="col-lg-8">
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-primary">{{ number_format($totalOrders) }}</h4>
                        <small class="text-muted">Jumla ya Maagizo</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 bg-success bg-opacity-10">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-success">{{ number_format($completedOrders) }}</h4>
                        <small class="text-muted">Imekamilika</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 bg-warning bg-opacity-10">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-warning">{{ number_format($activeOrders) }}</h4>
                        <small class="text-muted">Inaendelea</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm rounded-4 bg-danger bg-opacity-10">
                    <div class="card-body text-center py-3">
                        <h4 class="mb-0 text-danger">{{ number_format($cancelledOrders) }}</h4>
                        <small class="text-muted">Imefutwa</small>
                    </div>
                </div>
            </div>
        </div>
        
        @if($user->isConsumer())
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h6 class="fw-bold">Jumla ya Matumizi</h6>
                </div>
                <div class="card-body">
                    <h3 class="text-success">TZS {{ number_format($totalSpent) }}</h3>
                </div>
            </div>
        @endif
        
        {{-- Recent Orders --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h6 class="fw-bold">Maagizo ya Hivi Karibuni</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Agizo #</th>
                                <th>Muuzaji</th>
                                <th>Jumla</th>
                                <th>Hali</th>
                                <th>Tarehe</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->retailer->business_name ?? 'N/A' }}</td>
                                <td>TZS {{ number_format($order->total_amount) }}</td>
                                <td>
                                    @if($order->status == 'delivered')
                                        <span class="badge bg-success">Imekamilika</span>
                                    @elseif($order->status == 'cancelled')
                                        <span class="badge bg-danger">Imefutwa</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Inaendelea</span>
                                    @endif
                                </td>
                                <td>{{ $order->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-3 text-muted">Hakuna maagizo bado.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Rudi kwenye Orodha
    </a>
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
</style>
@endpush