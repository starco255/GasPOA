@extends('layouts.guest')

@section('title', 'Thibitisha OTP')
@section('page-heading', 'Thibitisha OTP ya Email')

@section('content')
<p class="text-muted mb-3">Tumepeleka OTP ya tarakimu 6 kwenye email yako.</p>

<div class="alert alert-light border d-flex align-items-center gap-2 mb-4 py-2 px-3">
    <i class="bi bi-hourglass-split text-warning"></i>
    <span class="small">OTP inaisha ndani ya <strong id="otpCountdown">--:--</strong></span>
</div>

@if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<form method="POST" action="{{ route('password.otp.verify') }}" id="otpForm">
    @csrf
    <div class="mb-4">
        <label for="otp" class="form-label">OTP</label>
        <input id="otp" name="otp" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6"
               class="form-control text-center fs-4 @error('otp') is-invalid @enderror" required autofocus
               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        @error('otp')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
    </div>
    <div class="d-grid"><button class="btn btn-primary btn-lg" type="submit" id="verifyBtn"><i class="bi bi-shield-check"></i> Thibitisha OTP</button></div>
</form>

<div class="mt-3 text-center">
    <form method="POST" action="{{ route('password.otp.resend') }}" id="resendForm">
        @csrf
        <button type="submit" class="btn btn-link text-decoration-none" id="resendBtn">
            <i class="bi bi-arrow-repeat"></i> Tuma OTP mpya
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const otpInput = document.getElementById('otp');
        const otpForm = document.getElementById('otpForm');
        const verifyBtn = document.getElementById('verifyBtn');
        const resendForm = document.getElementById('resendForm');
        const resendBtn = document.getElementById('resendBtn');
        const countdown = document.getElementById('otpCountdown');
        const expiresAt = {{ $expiresAt ?? 'null' }};

        function updateCountdown() {
            const remaining = expiresAt ? expiresAt - Math.floor(Date.now() / 1000) : 0;
            if (remaining <= 0) {
                countdown.textContent = 'imeisha';
                countdown.classList.add('text-danger');
                return false;
            }

            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;
            countdown.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            return true;
        }

        updateCountdown();
        const countdownInterval = setInterval(() => {
            if (!updateCountdown()) clearInterval(countdownInterval);
        }, 1000);

        otpInput.addEventListener('input', () => {
            if (otpInput.value.length === 6) {
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Inathibitisha...';
                otpForm.requestSubmit();
            }
        });

        resendForm.addEventListener('submit', () => {
            resendBtn.disabled = true;
            resendBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Inatuma...';
        });
    });
</script>
@endpush
