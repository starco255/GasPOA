<!DOCTYPE html>
<html lang="{{ auth()->user()?->interface_language === 'en' ? 'en' : 'sw' }}" data-theme="{{ auth()->user()?->interface_theme === 'dark' ? 'dark' : 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="GasPOA Market - Pata gesi papo kwa hapo kutoka kwa wauzaji wa karibu yako.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.interface-preferences-initializer')
    <title>Admin Panel - @yield('title', 'GasPOA Market')</title>
    
    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Custom GasPOA Styles --}}
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    {{-- DataTables CSS (Hiari) --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <style>
    body {
       background: linear-gradient(145deg, #a3a3e4 0%, #b3b896 100%);
       font-family: 'Inter', sans-serif;
    }
    
    /* Top Navbar - Imeboreshwa */
    .navbar-dark {
        background: linear-gradient(180deg, #1A1A2E 0%, #16213E 100%) !important;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        padding: 0.6rem 1rem;
    }
    
    .navbar-brand {
        font-weight: 700;
        font-size: 1.3rem;
        color: white !important;
    }
    
    .navbar-brand i {
        color: #FF6B35;
        margin-right: 8px;
    }
    
    /* Sidebar Styling - Imerekebishwa kusogeza maudhui */
    .sidebar {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: 100;
        padding: 80px 8px 20px 12px; /* padding-bottom imeongezwa */
        box-shadow: 5px 0 20px rgba(0, 0, 0, 0.05);
        background: linear-gradient(180deg, #1A1A2E 0%, #16213E 100%);
        color: white;
        
        /* Fanya sidebar iweze kusogea (scroll) ndani yake */
        overflow-y: auto;
        overflow-x: hidden;
        
        /* Skrini ndogo: hakikisha ina scroll */
        max-height: 100vh;
    }
    
    /* Kuficha scrollbar lakini bado iweze kusogea (hiari) */
    .sidebar::-webkit-scrollbar {
        width: 4px;
    }
    .sidebar::-webkit-scrollbar-thumb {
       background: linear-gradient(145deg, #7575b9 0%, #b3b896 100%);
        border-radius: 10px;
    }
    
    /* Profaili ya Admin kwenye Sidebar */
    .sidebar-profile {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(10px);
        flex-shrink: 0;
    }
    
    .sidebar-avatar {
        width: 50px;
        height: 50px;
        background: linear-gradient(145deg, #7575b9 0%, #b3b896 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.3rem;
        box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
    }
    
    .sidebar-user-name {
        font-weight: 700;
        color: white;
        font-size: 1rem;
        line-height: 1.2;
    }
    
    .sidebar-user-role {
        display: inline-block;
        background: rgba(255, 255, 255, 0.15);
        padding: 0.2rem 0.8rem;
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: rgba(255, 255, 255, 0.9);
        margin-top: 5px;
    }
    
    .sidebar .nav-link {
        font-weight: 500;
        color: rgba(255, 255, 255, 0.7);
        padding: 0.8rem 1rem;
        border-radius: 12px;
        margin-bottom: 6px;
        transition: all 0.3s ease;
    }
    
    .sidebar .nav-link i {
        width: 24px;
        margin-right: 8px;
    }
    
    .sidebar .nav-link:hover {
    background: linear-gradient(145deg, #7575b9 0%, #b3b896 100%);
       color: white;
    }
    
    .sidebar .nav-link.active {
            background: linear-gradient(145deg, #7575b9, #E85D2C);
            color: white;
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
    }
    
    .sidebar-heading {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.4);
        padding: 0.5rem 1rem;
        margin-top: 0.5rem;
    }
    
    /* Kuhakikisha kipengele cha mwisho hakijafichwa */
    .sidebar .nav:last-of-type {
        margin-bottom: 30px; /* nafasi ya ziada chini */
    }
    
    /* Main Content */
    main {
        padding-top: 80px;
        padding-bottom: 30px;
    }
    
    /* Responsive */
    @media (max-width: 767.98px) {
        .sidebar {
            padding-top: 70px;
        }
        .sidebar.collapse.show {
            display: block;
            width: min(86vw, 320px);
            z-index: 1035;
            border-right: 1px solid rgba(255, 255, 255, 0.12);
        }
        main {
            padding-top: 70px;
            padding-bottom: 2rem;
        }
    }
    
    /* Vitufe na Vipengele vya ziada */
    .btn-outline-light {
        border-color: rgba(255,255,255,0.3);
        color: white;
    }
    
    .btn-outline-light:hover {
        background: rgba(255,255,255,0.1);
        color: white;
    }
    
    .alert {
        border-radius: 16px;
        border: none;
    }
</style>
    @stack('styles')
</head>
<body>
    {{-- Top Navbar --}}
    <nav class="navbar navbar-dark fixed-top px-3">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-shield-lock-fill"></i> GasPOA
        </a>
        <button class="navbar-toggler d-md-none collapsed" type="button" data-bs-toggle="collapse" 
                data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav ms-auto">
            <div class="nav-item text-nowrap d-flex align-items-center text-white">
                @include('components.interface-preferences')
                <i class="bi bi-person-circle me-2"></i> {{ Auth::user()->full_name }}
                <form method="POST" action="{{ route('logout') }}" class="ms-3">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-box-arrow-right"></i> Toka
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            {{-- Sidebar --}}
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                {{-- Profaili ya Admin (Imeongezwa) --}}
                <div class="sidebar-profile d-flex align-items-center gap-3">
                    <div class="sidebar-avatar">
                        {{ strtoupper(substr(Auth::user()->full_name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="sidebar-user-name">{{ Auth::user()->full_name ?? 'Admin' }}</div>
                        <span class="sidebar-user-role">ADMIN</span>
                    </div>
                </div>
                
                <div class="position-sticky">
                    <ul class="nav flex-column">
           <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
           href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
          </a>
          </li>
          <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.show') ? 'active' : '' }}" 
           href="{{ route('admin.users.index') }}">
            <i class="bi bi-people"></i> Watumiaji
        </a>
          </li>
              <li class="nav-item">
                 <a class="nav-link {{ request()->routeIs('admin.pricing.*') ? 'active' : '' }}" 
                     href="{{ route('admin.pricing.index') }}">
                  <i class="bi bi-currency-dollar"></i> Bei & Uchumi
                 </a>
                 </li>
                 <li class="nav-item">
                  <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" 
                   href="{{ route('admin.reports.finance') }}">
                   <i class="bi bi-bar-chart"></i> Ripoti za Fedha
                  </a>
                 </li>
                </ul>

                 <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1">
                 <span>Mipangilio</span>
                </h6>
                   <ul class="nav flex-column mb-2">
                     <li class="nav-item">
                      <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
                       href="{{ route('admin.settings.profile') }}">
                       <i class="bi bi-gear"></i> Mipangilio
                        </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}" href="{{ route('admin.logs.index') }}">
                              <i class="bi bi-journal me-2"></i> Kumbukumbu (Logs)
                            </a>
                        </li>
                      </ul>

                </div>
            </nav>

            {{-- Main Content Area --}}
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                {{-- Alerts --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/interface-preferences.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    @stack('scripts')
</body>
</html>
