@extends('layouts.admin')

@section('title', 'Ripoti za Fedha')
@section('page-title', 'Ripoti ya Mauzo, Malipo na Tume')

@section('content')

{{-- Date Range Filter --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.reports.finance') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-muted">
                    <i class="bi bi-calendar3 me-1"></i> Kuanzia
                </label>
                <input type="date" name="from_date" class="form-control" 
                       value="{{ $fromDate ?? '' }}" placeholder="Ondoa chujio">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-muted">
                    <i class="bi bi-calendar3 me-1"></i> Hadi
                </label>
                <input type="date" name="to_date" class="form-control" 
                       value="{{ $toDate ?? '' }}" placeholder="Ondoa chujio">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 rounded-pill">
                    <i class="bi bi-funnel me-2"></i> Chuja
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.reports.finance') }}" class="btn btn-outline-secondary w-100 rounded-pill">
                    <i class="bi bi-arrow-counterclockwise me-2"></i> Reset
                </a>
            </div>
        </form>
        {{-- Onyesha kama filters ziko active --}}
        @if(isset($isFiltered) && $isFiltered)
            <div class="mt-3">
                <span  class="badge bg-success px-3 py-2 rounded-pill">
                    <i class="bi bi-funnel-fill me-1"></i> 
                    Kipindi: {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                </span>
            </div>
        @else
            <div class="mt-3">
                <span class="badge bg-success px-3 py-2 rounded-pill">
                    <i class="bi bi-infinity me-1"></i> Inaonyesha data zote (hakuna chujio)
                </span>
            </div>
        @endif
    </div>
</div>

{{-- Statistics Cards --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-body p-0">
                <div class="row g-0 h-100">
                    <div class="col-4 bg-primary d-flex align-items-center justify-content-center" style="min-height: 100px;">
                        <i class="bi bi-cash-stack text-white" style="font-size: 2.5rem;"></i>
                    </div>
                    <div class="col-8 p-3 d-flex flex-column justify-content-center">
                        <h6 class="text-muted mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;">Mauzo Yaliyolipwa</h6>
                        <h4 class="fw-bold mb-1">TZS {{ number_format($totalSales ?? 0) }}</h4>
                        <small class="text-muted">{{ $paidOrders ?? 0 }} malipo yaliyofanikiwa</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-body p-0">
                <div class="row g-0 h-100">
                    <div class="col-4 bg-success d-flex align-items-center justify-content-center" style="min-height: 100px;">
                        <i class="bi bi-graph-up text-white" style="font-size: 2.5rem;"></i>
                    </div>
                    <div class="col-8 p-3 d-flex flex-column justify-content-center">
                        <h6 class="text-muted mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;">Tume Iliyorekodiwa</h6>
                        <h4 class="fw-bold mb-1">TZS {{ number_format($totalCommission ?? 0) }}</h4>
                        <small class="text-muted">Kutoka payout halisi • {{ $pendingPayouts }} pending</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-body p-0">
                <div class="row g-0 h-100">
                    <div class="col-4 bg-info d-flex align-items-center justify-content-center" style="min-height: 100px;">
                        <i class="bi bi-check-circle text-white" style="font-size: 2.5rem;"></i>
                    </div>
                    <div class="col-8 p-3 d-flex flex-column justify-content-center">
                        <h6 class="text-muted mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;">Ufanisi wa Utekelezaji</h6>
                        <h4 class="fw-bold mb-1">{{ $deliveryRate ?? 0 }}%</h4>
                        <small class="text-muted">{{ $completedOrders ?? 0 }} imekamilika</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-body p-0">
                <div class="row g-0 h-100">
                    <div class="col-4 bg-warning d-flex align-items-center justify-content-center" style="min-height: 100px;">
                        <i class="bi bi-cart-check text-white" style="font-size: 2.5rem;"></i>
                    </div>
                    <div class="col-8 p-3 d-flex flex-column justify-content-center">
                        <h6 class="text-muted mb-1" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px;">Malipo ya Kidijitali</h6>
                        <h4 class="fw-bold mb-1">{{ $paymentSuccessRate ?? 0 }}%</h4>
                        <small class="text-muted">{{ $successfulPayments ?? 0 }}/{{ $paymentAttempts ?? 0 }} yamefanikiwa</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main Content --}}
<div class="row g-4">
    {{-- Daily Breakdown Table --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-calendar-check fs-5 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">Mwenendo wa Mauzo kwa Siku</h5>
                            @if($isFiltered)
                                <p class="text-muted small mb-0">{{ $fromDate }} - {{ $toDate }}</p>
                            @else
                                <p class="text-muted small mb-0">Data zote</p>
                            @endif
                        </div>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                        {{ $dailyReports->count() ?? 0 }} Siku
                    </span>
                </div>
            </div>
            <div class="card-body p-0 d-flex flex-column" style="min-height: 0;">
                <div class="table-scrollable flex-grow-1" style="max-height: 480px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="position: sticky; top: 0; z-index: 10; background: #f8f9fa;">
                            <tr>
                                <th style="padding-left: 1.5rem;">Tarehe</th>
                                <th class="text-center">Maagizo</th>
                                <th class="text-end">Mauzo (TZS)</th>
                                <th class="text-end">Tume Halisi (TZS)</th>
                                <th class="text-center" style="padding-right: 1.5rem;">Hali</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailyReports ?? [] as $report)
                            <tr>
                                <td style="padding-left: 1.5rem;">
                                    <strong>{{ \Carbon\Carbon::parse($report->date)->format('d M Y') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($report->date)->format('l') }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold">{{ number_format($report->total_orders) }}</span>
                                    <br>
                                    <small class="text-success">{{ $report->delivered_count }} imekamilika</small>
                                </td>
                                <td class="text-end fw-bold">TZS {{ number_format($report->total_sales) }}</td>
                                <td class="text-end text-success fw-bold">TZS {{ number_format($report->commission) }}</td>
                                <td class="text-center" style="padding-right: 1.5rem;">
                                    @if($report->total_orders > 0 && $report->total_orders == $report->delivered_count)
                                        <span class="badge bg-success rounded-pill px-2">Yote Imekamilika</span>
                                    @elseif($report->cancelled_count > 0)
                                        <span class="badge bg-warning text-dark rounded-pill px-2">{{ $report->cancelled_count }} Imefutwa</span>
                                    @else
                                        <span class="badge bg-info rounded-pill px-2">Inaendelea</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="text-center py-5">
                                        <i class="bi bi-graph-up display-1 text-muted opacity-50"></i>
                                        <h6 class="text-muted mt-3">Hakuna Data</h6>
                                        <p class="text-muted small">Hakuna maagizo kwenye kipindi hiki.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light" style="position: sticky; bottom: 0; z-index: 10;">
                            <tr>
                                <th style="padding-left: 1.5rem;">Jumla</th>
                                <th class="text-center">{{ number_format($dailyReports->sum('total_orders') ?? 0) }}</th>
                                <th class="text-end">TZS {{ number_format($dailyReports->sum('total_sales') ?? 0) }}</th>
                                <th class="text-end">TZS {{ number_format($dailyReports->sum('commission') ?? 0) }}</th>
                                <th style="padding-right: 1.5rem;"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Sidebar --}}
    <div class="col-lg-4">
        <div class="d-flex flex-column h-100 gap-4">
            {{-- Payment Methods --}}
            <div class="card border-0 shadow-sm rounded-4 flex-grow-1">
                <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-credit-card fs-5 text-success"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Njia za Malipo</h5>
                    </div>
                </div>
                <div class="card-body px-4 d-flex flex-column justify-content-center">
                    @forelse($paymentMethods ?? [] as $method)
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-3 @if(!$loop->last) border-bottom @endif">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; flex-shrink: 0;">
                                @if($method->payment_method == 'cash')
                                    <i class="bi bi-cash text-success fs-5"></i>
                                @elseif($method->payment_method == 'mobile_money')
                                    <i class="bi bi-phone text-primary fs-5"></i>
                                @else
                                    <i class="bi bi-credit-card text-info fs-5"></i>
                                @endif
                            </div>
                            <div>
                                <strong style="font-size: 0.9rem;">
                                    @if($method->payment_method == 'cash')
                                        Pesa Taslimu
                                    @elseif($method->payment_method == 'mobile_money')
                                        Simu (M-Pesa/TigoPesa)
                                    @else
                                        Kadi
                                    @endif
                                </strong>
                                <br>
                                <small class="text-muted">{{ $method->count }} maagizo</small>
                            </div>
                        </div>
                        <span class="fw-bold text-end" style="font-size: 0.9rem;">TZS {{ number_format($method->total) }}</span>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="bi bi-credit-card display-1 text-muted opacity-50"></i>
                        <p class="text-muted mt-2">Hakuna data ya malipo.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            {{-- Top Retailers --}}
            <div class="card border-0 shadow-sm rounded-4 flex-grow-1">
                <div class="card-header bg-white border-0 pt-4 pb-3 px-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-trophy fs-5 text-warning"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Wauzaji Bora</h5>
                    </div>
                </div>
                <div class="card-body px-4 d-flex flex-column justify-content-center">
                    @forelse($topRetailers ?? [] as $index => $retailer)
                    <div class="d-flex align-items-center mb-3 pb-3 @if(!$loop->last) border-bottom @endif">
                        <div class="fw-bold me-3 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width: 36px; height: 36px; font-size: 0.85rem;
                                    @if($index == 0) background: linear-gradient(135deg, #FFD700, #FFA500); color: white;
                                    @elseif($index == 1) background: linear-gradient(135deg, #C0C0C0, #A9A9A9); color: white;
                                    @elseif($index == 2) background: linear-gradient(135deg, #CD7F32, #8B4513); color: white;
                                    @else background: #e9ecef; color: #6c757d; @endif">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <strong style="font-size: 0.9rem;" class="d-block text-truncate">{{ $retailer->business_name }}</strong>
                            <small class="text-muted">{{ $retailer->total_orders }} maagizo</small>
                        </div>
                        <span class="fw-bold text-success flex-shrink-0 ms-2" style="font-size: 0.85rem;">TZS {{ number_format($retailer->total_sales) }}</span>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="bi bi-trophy display-1 text-muted opacity-50"></i>
                        <p class="text-muted mt-2">Hakuna data ya wauzaji.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.03);
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
    }
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        color: #dddddd;
        border-bottom: 2px solid #e2e8f0;
        padding: 0.85rem 0.75rem;
        white-space: nowrap;
    }
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    .table tbody tr:hover {
        background-color: #f8fafc;
    }
    .table td {
        padding: 0.85rem 0.75rem;
        vertical-align: middle;
    }
    .table tfoot th {
        font-weight: 700;
        color: #1a1a2e;
        white-space: nowrap;
    }
    .table-scrollable {
        scrollbar-width: thin;
        scrollbar-color: #c1c1c1 #f1f1f1;
    }
    .table-scrollable::-webkit-scrollbar {
        width: 6px;
    }
    .table-scrollable::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 10px;
    }
    .table-scrollable::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #4a6cf7, #3651d5);
        border-radius: 10px;
    }
    .btn-primary {
        background: linear-gradient(135deg, #4a6cf7 0%, #3651d5 100%);
        border: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #3651d5 0%, #2a40b0 100%);
        transform: translateY(-1px);
    }
    .rounded-pill {
        border-radius: 50px !important;
    }
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    @media (max-width: 768px) {
        .card-body { padding: 1rem !important; }
        .table td, .table th { padding: 0.5rem; font-size: 0.8rem; }
        .col-4.bg-primary, .col-4.bg-success, .col-4.bg-info, .col-4.bg-warning { min-height: 80px; }
    }
</style>
@endpush
