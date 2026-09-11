@extends('layouts.guest')

@section('title', 'Weka Nywila Mpya')
@section('page-heading', 'Weka Nywila Mpya')

@section('content')
<p class="text-muted mb-4">OTP yako imethibitishwa. Weka nywila mpya yenye usalama.</p>
<form method="POST" action="{{ route('password.store') }}">
    @csrf

    <div class="mb-3">
        <label for="password" class="form-label">Nywila Mpya</label>
        <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autofocus>
        @error('password')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
    </div>
    <div class="mb-4">
        <label for="password_confirmation" class="form-label">Thibitisha Nywila</label>
        <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required>
    </div>
    <div class="d-grid"><button class="btn btn-primary btn-lg" type="submit">Weka Nywila Mpya</button></div>
</form>
@endsection
