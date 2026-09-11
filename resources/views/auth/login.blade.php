@extends('layouts.guest')

@section('title', 'Ingia')
@section('page-heading', 'Ingia kwenye Akaunti Yako')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf

    {{-- Namba ya Simu au Email --}}
    <div class="mb-3">
        <label for="login" class="form-label">Barua Pepe au Namba ya Simu</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" 
                   name="login" value="{{ old('login') }}" required autofocus 
                   placeholder=" Mf: jinalako@example.com | +255615004300">
        </div>
        @error('login')
            <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
        @enderror
        <small class="text-muted">Unaweza kutumia namba ya simu au barua pepe uliyojisajili nayo.</small>
    </div>

    {{-- Password --}}
    <div class="mb-3">
        <label for="password" class="form-label">Nywila</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required placeholder="••••••••">
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        @error('password')
            <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
        @enderror
    </div>

    {{-- Remember Me --}}
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
        <label class="form-check-label" for="remember">Nikumbuke</label>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-box-arrow-in-right"></i> Ingia
        </button>
    </div>
</form>

{{-- Footer Links (Zilizopo ndani ya fomu) --}}
<div class="mt-3 text-center auth-footer">
    <a href="{{ route('password.request') }}" class="text-decoration-none">
        <i class="bi bi-question-circle"></i> Umesahau Nywila?
    </a>
    <br>
    <span class="text-muted">Huna akaunti?</span>
    <a href="{{ route('register') }}" class="text-decoration-none fw-bold">
        Jisajili Bure
    </a>
</div>

{{-- Row ya Chini Kabisa: Nyumbani na USSD --}}
<div class="d-flex justify-content-between align-items-center mt-4 pt-2 border-top border-light">
    <a href="{{ route('home') }}" class="text-decoration-none small fw-semibold" style="color: #FF6B35;">
        <i class="bi bi-arrow-left"></i> Rudi Nyumbani
    </a>
    <span class="text-muted small">
        <i class="bi bi-phone"></i> *150*99#
    </span>
</div>
@endsection

@section('footer-links')
    {{-- Tumefuta yaliyomo hapa kwa sababu tumehamishia ndani ya content --}}
@endsection

@push('scripts')
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
</script>
@endpush