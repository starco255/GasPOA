@extends('layouts.admin')

@section('title', 'Usimamizi wa Watumiaji')
@section('page-title', 'Usimamizi wa Watumiaji')

@section('content')

{{-- Tabs Navigation --}}
<ul class="nav nav-tabs mb-4" id="userTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="all-users-tab" data-bs-toggle="tab" data-bs-target="#all-users" type="button" role="tab">
            <i class="bi bi-people me-1"></i> 
            Watumiaji Wote 
            <span class="badge bg-primary ms-1">{{ $totalUsers ?? 0 }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="verify-tab" data-bs-toggle="tab" data-bs-target="#verify-pending" type="button" role="tab">
            <i class="bi bi-check-circle me-1"></i> 
            Zinazosubiri Uidhinishaji 
            <span class="badge bg-warning text-dark ms-1">{{ $pendingVerifications ?? 0 }}</span>
        </button>
    </li>
</ul>

{{-- Tab Content --}}
<div class="tab-content" id="userTabsContent">
    
    {{-- ===================================================== --}}
    {{-- TAB 1: WATUMIAJI WOTE --}}
    {{-- ===================================================== --}}
    <div class="tab-pane fade show active" id="all-users" role="tabpanel">
        
        {{-- Users Table with Filters --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-4 pb-3">
                <div class="row align-items-center">
                    <div class="col-md-4 mb-2 mb-md-0">
                        <form id="filterForm" method="GET" action="{{ route('admin.users.index') }}">
                            <select class="form-select w-auto d-inline-block" name="type" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Aina Zote</option>
                                <option value="consumer" {{ request('type') == 'consumer' ? 'selected' : '' }}>Consumers</option>
                                <option value="retailer" {{ request('type') == 'retailer' ? 'selected' : '' }}>Retailers</option>
                                <option value="wholesaler" {{ request('type') == 'wholesaler' ? 'selected' : '' }}>Wholesalers</option>
                                <option value="admin" {{ request('type') == 'admin' ? 'selected' : '' }}>Admins</option>
                            </select>
                        
                            <select class="form-select w-auto d-inline-block ms-2" name="status" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Hali Zote</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Imefungwa</option>
                            </select>
                        </form>
                    </div>
                    <div class="col-md-8 text-md-end">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="d-inline-flex w-100 w-md-auto">
                            <input type="text" name="search" class="form-control" placeholder="Tafuta kwa jina, simu..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary ms-2">
                                <i class="bi bi-search"></i>
                            </button>
                            @if(request()->filled('type') || request()->filled('status') || request()->filled('search'))
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="bi bi-x-lg"></i> Futa
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Jina</th>
                                <th>Namba ya Simu</th>
                                <th>Barua Pepe</th>
                                <th>Aina</th>
                                <th>Hali</th>
                                <th>Tarehe ya Usajili</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users ?? [] as $user)
                            <tr data-type="{{ $user->user_type }}">
                                <td>#{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                            <span class="fw-bold text-primary">{{ strtoupper(substr($user->full_name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <strong>{{ $user->full_name }}</strong>
                                            @if($user->isBusinessUser())
                                                <br><small class="text-muted">{{ $user->businessProfile->business_name ?? 'Hakuna duka' }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->phone_number }}</td>
                                <td>{{ $user->email ?? 'Haipo' }}</td>
                                <td>
                                    @if($user->user_type == 'consumer')
                                        <span class="badge bg-primary">Mtumiaji</span>
                                    @elseif($user->user_type == 'retailer')
                                        <span class="badge bg-success">Muuza Rejareja</span>
                                    @elseif($user->user_type == 'wholesaler')
                                        <span class="badge bg-warning text-dark">Muuza Jumla</span>
                                    @elseif($user->user_type == 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($user->user_type) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Active</span>
                                        @if($user->is_phone_verified)
                                            <i class="bi bi-check-circle-fill text-success ms-1" title="Simu imethibitishwa"></i>
                                        @endif
                                    @else
                                        <span class="badge bg-danger">Imefungwa</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $user->created_at->format('d M Y') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.users.show', $user->id) }}">
                                                    <i class="bi bi-eye"></i> Tazama Maelezo
                                                </a>
                                            </li>
                                            @if($user->id !== auth()->id())
                                                <li>
                                                    <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">
                                                        @csrf
                                                        @if($user->is_active)
                                                            <button type="submit" class="dropdown-item text-warning">
                                                                <i class="bi bi-lock"></i> Funga Akaunti
                                                            </button>
                                                        @else
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i class="bi bi-unlock"></i> Fungua Akaunti
                                                            </button>
                                                        @endif
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                                          onsubmit="return confirm('Una uhakika? Akaunti na taarifa zake zote zitaondolewa kabisa. Hatua hii haiwezi kurejeshwa.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash3"></i> Futa Mtumiaji
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
                                    <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i>
                                    Hakuna watumiaji wanaolingana na vigezo vyako.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <span>Jumla: {{ $users->total() ?? 0 }} watumiaji</span>
                {{ $users->links() }}
            </div>
        </div>
    </div>
    
    {{-- ===================================================== --}}
    {{-- TAB 2: ZINAZOSUBIRI UIDHINISHAJI --}}
    {{-- ===================================================== --}}
    <div class="tab-pane fade" id="verify-pending" role="tabpanel">

        {{-- Filters --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 pt-4 pb-3">
                <form method="GET" action="{{ route('admin.users.index') }}" id="verifyFilterForm">
                    <input type="hidden" name="tab" value="verify">
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <select class="form-select" name="vtype" onchange="document.getElementById('verifyFilterForm').submit()">
                                <option value="">Aina Zote za Biashara</option>
                                <option value="retailer" {{ request('vtype') == 'retailer' ? 'selected' : '' }}>Wauzaji Rejareja</option>
                                <option value="wholesaler" {{ request('vtype') == 'wholesaler' ? 'selected' : '' }}>Wauzaji Jumla</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <select class="form-select" name="vstatus" onchange="document.getElementById('verifyFilterForm').submit()">
                                <option value="">Hali Zote</option>
                                <option value="unverified" {{ request('vstatus') == 'unverified' ? 'selected' : '' }}>Zisizoidhinishwa</option>
                                <option value="verified" {{ request('vstatus') == 'verified' ? 'selected' : '' }}>Zimeidhinishwa</option>
                            </select>
                        </div>
                        <div class="col-md-4 text-md-end">
                            @if(request()->filled('vtype') || request()->filled('vstatus'))
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-lg"></i> Futa Vichujio
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Pending Businesses List --}}
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
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-1"><i class="bi bi-geo-alt me-2"></i> {{ $biz->physical_address ?? 'Haipo' }}</p>
                                    <p class="mb-1"><i class="bi bi-file-text me-2"></i> TIN: {{ $biz->tinn_number ?? 'Haipo' }}</p>
                                </div>
                            </div>
                            
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i> Ilisajiliwa: {{ $biz->created_at->format('d M Y, H:i') }}
                            </small>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="d-grid gap-2">
                                {{-- Badilisha kuwa custom modal trigger --}}
                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                        onclick="openCustomModal('detailsModal{{ $biz->id }}')">
                                    <i class="bi bi-eye"></i> Tazama Maelezo
                                </button>
                                
                                @if(!$biz->is_open)
                                    <form method="POST" action="{{ route('admin.verify.approve', $biz->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Una uhakika unataka kuidhinisha biashara hii?')">
                                            <i class="bi bi-check-lg"></i> Idhinisha
                                        </button>
                                    </form>
                                @endif
                                
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="openCustomModal('rejectModal{{ $biz->id }}')">
                                    <i class="bi bi-x-lg"></i> Kataa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Custom Details Modal --}}
                <div class="custom-modal-overlay" id="detailsModal{{ $biz->id }}">
                    <div class="custom-modal">
                        <div class="custom-modal-header">
                            <h5 class="custom-modal-title">{{ $biz->business_name }}</h5>
                            <button type="button" class="custom-modal-close" onclick="closeCustomModal('detailsModal{{ $biz->id }}')">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div class="custom-modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Maelezo ya Biashara</h6>
                                    <p><strong>Jina:</strong> {{ $biz->business_name }}</p>
                                    <p><strong>Aina:</strong> {{ $biz->business_type == 'retailer' ? 'Muuza Rejareja' : 'Muuza Jumla' }}</p>
                                    <p><strong>TIN:</strong> {{ $biz->tinn_number ?? 'Haipo' }}</p>
                                    <p><strong>Anwani:</strong> {{ $biz->physical_address ?? 'Haipo' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Maelezo ya Mmiliki</h6>
                                    <p><strong>Jina:</strong> {{ $biz->user->full_name ?? 'Haijulikani' }}</p>
                                    <p><strong>Simu:</strong> {{ $biz->user->phone_number ?? 'Haipo' }}</p>
                                    <p><strong>Barua Pepe:</strong> {{ $biz->user->email ?? 'Haipo' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="custom-modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeCustomModal('detailsModal{{ $biz->id }}')">Funga</button>
                            @if(!$biz->is_open)
                                <form method="POST" action="{{ route('admin.verify.approve', $biz->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Idhinisha</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Custom Reject Modal --}}
                <div class="custom-modal-overlay" id="rejectModal{{ $biz->id }}">
                    <div class="custom-modal">
                        <form method="POST" action="{{ route('admin.verify.reject', $biz->id) }}">
                            @csrf
                            <div class="custom-modal-header">
                                <h5 class="custom-modal-title">Thibitisha Kukataa Biashara</h5>
                                <button type="button" class="custom-modal-close" onclick="closeCustomModal('rejectModal{{ $biz->id }}')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="custom-modal-body">
                                <p>Una uhakika unataka kukataa biashara ya <strong>{{ $biz->business_name }}</strong>?</p>
                                <label class="form-label">Sababu (Hiari):</label>
                                <select class="form-select" name="rejection_reason">
                                    <option value="">-- Chagua Sababu --</option>
                                    <option value="incomplete">Taarifa hazijakamilika</option>
                                    <option value="duplicate">Biashara imeshasajiliwa</option>
                                    <option value="invalid">Taarifa si sahihi</option>
                                    <option value="other">Nyingine</option>
                                </select>
                            </div>
                            <div class="custom-modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeCustomModal('rejectModal{{ $biz->id }}')">Ghairi</button>
                                <button type="submit" class="btn btn-danger">Kataa</button>
                            </div>
                        </form>
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
            @if($pending->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $pending->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Custom Modal Functions
    function openCustomModal(id) {
        document.getElementById(id).style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    function closeCustomModal(id) {
        document.getElementById(id).style.display = 'none';
        document.body.style.overflow = '';
    }
    // Funga unapobofya nje ya modal
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('custom-modal-overlay')) {
            e.target.style.display = 'none';
            document.body.style.overflow = '';
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Activate correct tab based on URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab === 'verify') {
            const verifyTab = document.getElementById('verify-tab');
            if (verifyTab) {
                new bootstrap.Tab(verifyTab).show();
            }
        }
        
        // Store active tab in localStorage
        document.querySelectorAll('#userTabs button').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(e) {
                localStorage.setItem('activeAdminTab', e.target.getAttribute('data-bs-target'));
            });
        });
        
        // Restore active tab
        const activeTab = localStorage.getItem('activeAdminTab');
        if (activeTab) {
            const tab = document.querySelector(`button[data-bs-target="${activeTab}"]`);
            if (tab) {
                new bootstrap.Tab(tab).show();
            }
        }
    });
</script>
@endpush

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
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        color: #dddddd;
    }
    .pagination {
        margin-bottom: 0;
    }
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        padding: 0.75rem 1.5rem;
    }
    .nav-tabs .nav-link:hover {
        color: #0d6efd;
        border: none;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: transparent;
        border-bottom: 3px solid #0d6efd;
    }

    /* ===== Custom Modal System (thabiti) ===== */
    .custom-modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        overflow-y: auto;
    }
    .custom-modal {
        position: relative;
        width: 90%;
        max-width: 500px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.25);
        margin: 50px auto;
        display: flex;
        flex-direction: column;
        max-height: 80vh;
    }
    .custom-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #eee;
    }
    .custom-modal-title {
        font-weight: 700;
        font-size: 1.25rem;
        margin: 0;
    }
    .custom-modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #666;
    }
    .custom-modal-body {
        padding: 1.25rem 1.5rem;
        overflow-y: auto;
        flex: 1;
    }
    .custom-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #eee;
        background-color: white;
    }
    .custom-modal, .custom-modal * {
        transition: none !important;
        animation: none !important;
    }
</style>
@endpush
