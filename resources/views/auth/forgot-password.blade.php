@extends('layouts.guest')

@section('title', 'Umesahau Nywila')
@section('page-heading', 'Rudisha Nywila Yako')

@section('content')
<p class="text-muted mb-4">
    Usijali! Tuambie namba ya simu au barua pepe yako. Tutatuma OTP ya tarakimu 6 kwenye barua pepe iliyosajiliwa.
</p>

@if(session('status'))
    <div class="alert alert-success mb-4">
        <i class="bi bi-check-circle-fill"></i> {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="mb-4">
        <label for="login" class="form-label">Namba ya Simu au Barua Pepe</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" 
                   name="login" value="{{ old('login') }}" required autofocus 
                   placeholder="Mf: 0712345678 au jina@example.com">
        </div>
        @error('login')
            <span class="invalid-feedback d-block">{{ $message }}</span>
        @enderror
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-send"></i> Tuma OTP ya Kubadilisha Nywila
        </button>
    </div>
</form>
@endsection

@section('footer-links')
    <a href="{{ route('login') }}" class="text-decoration-none">
        <i class="bi bi-arrow-left"></i> Rudi kwenye Ingia
    </a>
    <br>
    <span class="text-muted">Huna akaunti?</span>
    <a href="{{ route('register') }}" class="text-decoration-none fw-bold">
        Jisajili
    </a>
    <hr>
    <p class="small text-muted">
        <i class="bi bi-phone"></i> Unaweza pia kupiga <strong>*150*99#</strong> kwa msaada.
    </p>
@endsection
