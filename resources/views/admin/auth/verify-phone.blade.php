@extends('layouts.admin')

@section('title', 'Thibitisha Namba')
@section('page-title', 'Thibitisha Namba ya Simu')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 text-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            </div>
        @endif

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            {{-- Header --}}
            <div class="card-header border-0 text-center py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-shield-lock-fill text-white" style="font-size: 2.5rem;"></i>
                </div>
                <h4 class="fw-bold text-white mb-2">Thibitisha Namba ya Simu</h4>
                <p class="text-white-50 mb-0">OTP imetumwa kwenye barua pepe yako</p>
            </div>
            
            <div class="card-body p-5 text-center">
                
                {{-- OTP Form --}}
                <form method="POST" action="{{ route('admin.phone.verify.submit') }}" id="otpForm">
                    @csrf
                    <input type="hidden" name="otp" id="completeOtp">

                    {{-- OTP Input Boxes - KUBWA NA WAZI --}}
                    <div class="d-flex justify-content-center gap-3 mb-5" id="otpBoxes">
                        @for($i = 0; $i < 6; $i++)
                        <input type="text" 
                               class="form-control text-center otp-input" 
                               maxlength="1" 
                               data-index="{{ $i }}"
                               inputmode="numeric"
                               pattern="[0-9]"
                               autocomplete="one-time-code"
                               style="width: 65px; height: 80px; font-size: 2.2rem; font-weight: 700; border-radius: 16px; border: 2.5px solid #d1d5db;">
                        @endfor
                    </div>

                    {{-- Timer --}}
                    <div class="mb-4">
                        <div class="bg-light rounded-pill d-inline-flex align-items-center px-4 py-2">
                            <i class="bi bi-hourglass-split text-warning me-2"></i>
                            <span id="timer" class="fw-semibold">--:--</span>
                            <span class="text-muted ms-1 small">zimebaki</span>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-primary w-100 btn-lg shadow-sm" id="verifyBtn" disabled>
                        <i class="bi bi-check-circle me-2"></i> Thibitisha Sasa
                    </button>
                </form>

                {{-- Resend --}}
                <div class="mt-4">
                    <form method="POST" action="{{ route('admin.phone.resend') }}" id="resendForm">
                        @csrf
                        <button type="submit" class="btn btn-link text-muted text-decoration-none p-0" id="resendLink">
                            <i class="bi bi-arrow-repeat me-1"></i> Hukupokea OTP? Tuma Tena
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Back --}}
        <div class="text-center mt-3">
            <a href="{{ route('admin.settings.profile') }}" class="text-muted text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i> Rudi kwenye Mipangilio
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const otpInputs = document.querySelectorAll('.otp-input');
        const completeOtp = document.getElementById('completeOtp');
        const verifyBtn = document.getElementById('verifyBtn');
        const form = document.getElementById('otpForm');
        
        // Auto-focus first input
        otpInputs[0]?.focus();
        
        // Handle input & auto-verify
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                // Allow only one digit
                if (!/^\d$/.test(this.value)) { 
                    this.value = ''; 
                    return; 
                }
                
                // Move to next input
                if (this.value && index < 5) {
                    otpInputs[index + 1].focus();
                }
                
                // Update hidden input
                let otp = '';
                otpInputs.forEach(inp => otp += inp.value);
                completeOtp.value = otp;
                
                // 🚀 AUTO-VERIFY when all 6 digits are filled
                if (otp.length === 6) {
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Inathibitisha...';
                    setTimeout(() => form.submit(), 300); // Small delay for user to see
                } else {
                    verifyBtn.disabled = true;
                    verifyBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i> Thibitisha Sasa';
                }
            });
            
            // Handle backspace
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    otpInputs[index - 1].value = '';
                    otpInputs[index - 1].focus();
                }
            });
            
            // Handle paste
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text');
                const digits = text.replace(/\D/g, '').substring(0, 6);
                
                digits.split('').forEach((char, i) => {
                    if (otpInputs[i]) otpInputs[i].value = char;
                });
                
                // Update and auto-verify
                let otp = '';
                otpInputs.forEach(inp => otp += inp.value);
                completeOtp.value = otp;
                
                if (otp.length === 6) {
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Inathibitisha...';
                    setTimeout(() => form.submit(), 300);
                }
                
                // Focus next empty or last filled
                const nextEmpty = Array.from(otpInputs).find(inp => !inp.value);
                if (nextEmpty) nextEmpty.focus();
                else otpInputs[5].focus();
            });
        });
        
        // Timer hutumia expiry ya server, ili refresh ya ukurasa isianzishe dakika tano upya.
        const expiresAt = {{ $expiresAt ?? 'null' }};
        const timer = document.getElementById('timer');
        const resendForm = document.getElementById('resendForm');
        const resendLink = document.getElementById('resendLink');
        const updateTimer = () => {
            const time = expiresAt ? expiresAt - Math.floor(Date.now() / 1000) : 0;
            if (time <= 0) {
                timer.textContent = '0:00';
                timer.classList.add('text-danger');
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i> OTP Imeisha';
                return false;
            }
            const m = Math.floor(time / 60);
            const s = time % 60;
            timer.textContent = `${m}:${s.toString().padStart(2, '0')}`;
            if (time < 60) timer.classList.add('text-danger');
            return true;
        };
        updateTimer();
        const countdown = setInterval(() => {
            if (!updateTimer()) clearInterval(countdown);
        }, 1000);

        resendForm.addEventListener('submit', () => {
            resendLink.disabled = true;
            resendLink.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Inatuma...';
        });
    });
</script>
@endpush

@push('styles')
<style>
    .otp-input {
        transition: all 0.2s ease;
        background-color: #f9fafb;
        caret-color: #667eea;
    }
    
    .otp-input:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 5px rgba(102, 126, 234, 0.2);
        outline: none;
        background-color: #ffffff;
        transform: translateY(-3px);
    }
    
    .otp-input:not(:placeholder-shown) {
        border-color: #667eea !important;
        background-color: #eef2ff;
        color: #4338ca;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover:not(:disabled) {
        background: linear-gradient(135deg, #5a6fd6 0%, #6a4190 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }
    
    .btn-primary:disabled {
        background: #6a4d6d;
        transform: none;
        box-shadow: none;
        cursor: not-allowed;
    }
    
    /* Remove number input arrows */
    .otp-input::-webkit-outer-spin-button,
    .otp-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .otp-input[type=text] {
        -moz-appearance: textfield;
    }
</style>
@endpush
