<!DOCTYPE html>
<html lang="{{ auth()->user()?->interface_language === 'en' ? 'en' : 'sw' }}" data-theme="{{ auth()->user()?->interface_theme === 'dark' ? 'dark' : 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="GasPOA Market - Pata gesi papo kwa hapo kutoka kwa wauzaji wa karibu yako.">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.interface-preferences-initializer')
    <title>Dashboard - @yield('title', 'GasPOA Market')</title>
    
    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Custom GasPOA Styles --}}
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    
    <style>
     body {
       background: linear-gradient(145deg, #a3a3e4 0%, #b3b896 100%);
       font-family: 'Inter', sans-serif;
    }
        /* Top Navbar - Imeboreshwa kufanana na Admin */
        .navbar-top {
            background: linear-gradient(180deg, #1A1A2E 0%, #16213E 100%);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 0.6rem 1.5rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            color: white !important;
        }
        
        .navbar-brand i {
            color: #FF6B35;
            margin-right: 8px;
        }
        
        .navbar-top .btn-outline-light {
            border-color: rgba(255,255,255,0.3);
            color: white;
        }
        
        .navbar-top .btn-outline-light:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }


        /* Hakikisha kitufe cha Akaunti hakibadiliki rangi kuwa nyeupe kinapoelezwa */
.navbar-top .btn-outline-light:hover,
.navbar-top .btn-outline-light:focus,
.navbar-top .btn-outline-light:active {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: white !important;
    border-color: rgba(255, 255, 255, 0.5) !important;
}

/* Pia hakikisha dropdown menu yenyewe ina muonekano mzuri */
.dropdown-menu {
    background-color: #1A1A2E;
    border: 1px solid rgba(255,255,255,0.1);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.dropdown-menu .dropdown-item {
    color: rgba(255,255,255,0.8);
}

.dropdown-menu .dropdown-item:hover {
    background: linear-gradient(145deg, #7575b9 0%, #b3b896 100%);
    color: white;
}

.dropdown-divider {
    border-top: 1px solid rgba(255,255,255,0.1);
}
        
        /* Sidebar Styling - Sasa inaanza chini ya navbar */
        .sidebar {
            position: fixed;
            top: 60px; /* Urefu wa navbar */
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 20px 12px;
            box-shadow: 5px 0 20px rgba(0, 0, 0, 0.05);
            background: linear-gradient(180deg, #1A1A2E 0%, #16213E 100%);
            color: white;
            overflow-y: auto;
            overflow-x: hidden;
            width: 260px;
        }
        
        /* Skroli ndani ya sidebar */
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar::-webkit-scrollbar-thumb {
        background: linear-gradient(145deg, #7575b9 0%, #b3b896 100%);
        border-radius: 10px;
        }
        
        /* Profaili ya Mtumiaji kwenye Sidebar */
        .sidebar-profile {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
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
            color: #ffffff;
            background: linear-gradient(145deg, #7575b9 0%, #b3b896 100%);
        }
        
        .sidebar .nav-link.active {
            color: #ffffff;
            background: linear-gradient(145deg, #7575b9, #E85D2C);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin: 1rem 0;
        }
        
        /* Main Content - inaanza baada ya sidebar na navbar */
        .main-content {
            margin-left: 260px; /* Upana wa sidebar */
            margin-top: 60px; /* Urefu wa navbar */
            padding: 24px 30px;
            min-height: calc(100vh - 60px);
        }
        
        /* Kwa vifaa vidogo */
        @media (max-width: 767.98px) {
            .sidebar {
                display: none;
            }
            .sidebar.collapse.show {
                display: block;
                width: min(86vw, 320px);
                z-index: 1025;
                border-right: 1px solid rgba(255, 255, 255, 0.12);
            }
            .main-content {
                margin-left: 0;
                padding: 16px 12px calc(6.5rem + env(safe-area-inset-bottom));
            }
            .navbar-top .navbar-toggler {
                display: inline-block;
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
        
        /* Hakikisha kichwa cha ukurasa kinaonekana vizuri */
        .page-title {
            color: #1A1A2E;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Top Navbar (Fixed) --}}
    <nav class="navbar-top d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <button class="btn btn-outline-light d-md-none me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-fire"></i> GasPOA
            </a>
        </div>
        
        <div class="d-flex align-items-center">
            @include('components.interface-preferences')
            <span class="text-white d-none d-md-block me-3">
                <i class="bi bi-person-circle"></i> {{ Auth::user()->full_name }}
            </span>
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-gear"></i> <span class="d-none d-sm-inline">Akaunti</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    {{-- Profaili link inabadilika kulingana na user type --}}
                    @if(Auth::user()->user_type === 'retailer')
                        <li><a class="dropdown-item" href="{{ route('retailer.profile.edit') }}"><i class="bi bi-person"></i> Profaili</a></li>
                    @elseif(Auth::user()->user_type === 'wholesaler')
                        <li><a class="dropdown-item" href="{{ route('wholesaler.account.edit') }}"><i class="bi bi-person"></i> Profaili</a></li>
                    @else
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person"></i> Profaili</a></li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Toka</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="d-flex">
        {{-- Sidebar (Fixed, inaanza chini ya navbar) --}}
        <nav id="sidebarMenu" class="sidebar collapse d-md-block">
            {{-- Profaili ya Mtumiaji --}}
            <div class="sidebar-profile d-flex align-items-center gap-3">
                <div class="sidebar-avatar">
                    {{ strtoupper(substr(Auth::user()->full_name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-grow-1">
                    <div class="sidebar-user-name">{{ Auth::user()->full_name ?? 'Mtumiaji' }}</div>
                    <span class="sidebar-user-role">{{ ucfirst(Auth::user()->user_type) }}</span>
                </div>
            </div>
            
            <ul class="nav flex-column">
                {{-- Links za kawaida kwa wote --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                       href="{{ route('dashboard') }}">
                        <i class="bi bi-house-door"></i> Nyumbani
                    </a>
                </li>
                
                         {{-- Consumer Links --}}
@if(Auth::user()->user_type === 'consumer')
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('consumer.order.create') || request()->routeIs('consumer.order.select-retailer') ? 'active' : '' }}" 
           href="{{ route('consumer.order.create') }}">
            <i class="bi bi-plus-circle"></i> Agiza Sasa
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('consumer.order.tracking*') || request()->routeIs('consumer.order.details*') ? 'active' : '' }}" 
           href="{{ route('consumer.order.tracking') }}">
            <i class="bi bi-geo-alt"></i> Fuatilia Agizo
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('consumer.history*') || request()->routeIs('consumer.order.receipt*') ? 'active' : '' }}" 
           href="{{ route('consumer.history') }}">
            <i class="bi bi-clock-history"></i> Historia
        </a>
    </li>
@endif
                
                {{-- Retailer Links --}}
                @if(Auth::user()->user_type === 'retailer')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('retailer.orders.incoming') ? 'active' : '' }}" 
                           href="{{ route('retailer.orders.incoming') }}">
                            <i class="bi bi-bell"></i> Maagizo Wateja
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('retailer.procurement.browse') ? 'active' : '' }}" 
                           href="{{ route('retailer.procurement.browse') }}">
                            <i class="bi bi-cart-plus"></i> Agiza Gesi Jumla
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('retailer.settings.shop') ? 'active' : '' }}" 
                           href="{{ route('retailer.settings.shop') }}">
                            <i class="bi bi-shop"></i> Duka Langu
                        </a>
                    </li>
                @endif
                
                {{-- Wholesaler Links --}}
                @if(Auth::user()->user_type === 'wholesaler')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('wholesaler.orders.incoming') ? 'active' : '' }}" 
                           href="{{ route('wholesaler.orders.incoming') }}">
                            <i class="bi bi-truck"></i> Maagizo ya Jumla
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('wholesaler.products.index') ? 'active' : '' }}" 
                           href="{{ route('wholesaler.products.index') }}">
                            <i class="bi bi-tags"></i> Bidhaa Zangu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('wholesaler.settings.shop') ? 'active' : '' }}" 
                           href="{{ route('wholesaler.settings.shop') }}">
                            <i class="bi bi-building"></i> Ghala Langu
                        </a>
                    </li>
                @endif
                
                <div class="sidebar-divider"></div>
                
                {{-- Mipangilio ya Akaunti - Inabadilika kulingana na user type --}}
                @if(Auth::user()->user_type === 'retailer')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('retailer.profile.edit') ? 'active' : '' }}" 
                           href="{{ route('retailer.profile.edit') }}">
                            <i class="bi bi-person-gear"></i> Mipangilio ya Akaunti
                        </a>
                    </li>
                @elseif(Auth::user()->user_type === 'wholesaler')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('wholesaler.account.edit') ? 'active' : '' }}" 
                           href="{{ route('wholesaler.account.edit') }}">
                            <i class="bi bi-person-gear"></i> Mipangilio ya Akaunti
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" 
                           href="{{ route('profile.edit') }}">
                            <i class="bi bi-person-gear"></i> Mipangilio ya Akaunti
                        </a>
                    </li>
                @endif
                
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link text-danger w-100 text-start border-0 bg-transparent">
                            <i class="bi bi-box-arrow-right"></i> Toka
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        {{-- Main Content Area --}}
        <div class="main-content flex-grow-1">
            {{-- Alerts --}}
            @include('partials.alerts')
            
            {{-- Page Title (Inaonekana vizuri chini ya navbar) --}}
            @hasSection('page-title')
                <h4 class="page-title">@yield('page-title')</h4>
            @endif
            
            @yield('content')
        </div>
    </div>

    {{-- Mobile Bottom Navigation --}}
    @include('partials.mobile-menu')

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/interface-preferences.js') }}"></script>
    @stack('scripts')
</body>
</html>
