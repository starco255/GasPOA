@extends('layouts.guest')

@section('title', 'Jisajili')
@section('page-heading', 'Fungua Akaunti Mpya')

@section('content')
<form method="POST" action="{{ route('register') }}" id="registerForm">
    @csrf

    {{-- UUID - Imefichwa, itajazwa kiotomatiki na JavaScript --}}
    <input type="hidden" name="uuid" id="uuid" value="">

    {{-- Jina Kamili --}}
    <div class="mb-3">
        <label for="full_name" class="form-label">Jina Kamili</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
            <input id="full_name" type="text" class="form-control @error('full_name') is-invalid @enderror" 
                   name="full_name" value="{{ old('full_name') }}" required placeholder="Mf: Starco Macos">
        </div>
        @error('full_name')
            <span class="invalid-feedback d-block">{{ $message }}</span>
        @enderror
    </div>

    {{-- Namba ya Simu --}}
    <div class="mb-3">
        <label for="phone_number" class="form-label">Namba ya Simu</label>
        <div class="input-group">
            <span class="input-group-text">+255</span>
            <input id="phone_number" type="tel" 
                   class="form-control @error('phone_number') is-invalid @enderror" 
                   name="phone_number" 
                   value="{{ old('phone_number') }}" 
                   required 
                   placeholder="615004300"
                   oninput="validatePhoneInput(this)">
        </div>
        {{-- Error message - inaonekana tu kukiwa na kosa --}}
        <div id="phoneError" class="text-danger small mt-1" style="display: none;">
            <i class="bi bi-exclamation-circle"></i> 
            <span id="phoneErrorMessage"></span>
        </div>
        @error('phone_number')
            <span class="invalid-feedback d-block">{{ $message }}</span>
        @enderror
        <small class="text-muted">
            <i class="bi bi-info-circle"></i> 
            Weka namba ya simu BILA "0" mwanzoni. Mfano: 615004300
        </small>
    </div>

    {{-- Email --}}
    <div class="mb-3">
        <label for="email" class="form-label">Barua Pepe <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" placeholder="jinalako@example.com" required>
        </div>
        @error('email')
            <span class="invalid-feedback d-block">{{ $message }}</span>
        @enderror
    </div>

    {{-- Aina ya Mtumiaji --}}
    <div class="mb-3">
        <label for="user_type" class="form-label">Ungependa Kujisajili Kama Nani?</label>
        <select id="user_type" name="user_type" class="form-select @error('user_type') is-invalid @enderror" required>
            <option value="">-- Chagua --</option>
            <option value="consumer" {{ old('user_type') == 'consumer' ? 'selected' : '' }}>Mtumiaji wa Kawaida (Nahitaji Gesi)</option>
            <option value="retailer" {{ old('user_type') == 'retailer' ? 'selected' : '' }}>Mfanyabiashara Rejareja (Duka la Gesi)</option>
            <option value="wholesaler" {{ old('user_type') == 'wholesaler' ? 'selected' : '' }}>Muuza Jumla (Ghala)</option>
        </select>
        @error('user_type')
            <span class="invalid-feedback d-block">{{ $message }}</span>
        @enderror
    </div>

    {{-- Nywila --}}
    <div class="mb-3">
        <label for="password" class="form-label">Nywila</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock"></i></span>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required placeholder="Angalau vibambo 8">
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        @error('password')
            <span class="invalid-feedback d-block">{{ $message }}</span>
        @enderror
    </div>

    {{-- Thibitisha Nywila --}}
    <div class="mb-4">
        <label for="password_confirmation" class="form-label">Thibitisha Nywila</label>
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
            <input id="password_confirmation" type="password" class="form-control" 
                   name="password_confirmation" required placeholder="Rudia Nywila">
        </div>
    </div>

    {{-- Masharti --}}
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
        <label class="form-check-label" for="terms">
            Nakubali <a href="#" target="_blank">Masharti na Vigezo</a> vya GasPOA Market.
        </label>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
            <i class="bi bi-person-plus"></i> Jisajili
        </button>
    </div>
</form>

{{-- Footer Links (Zilizopo ndani ya fomu) --}}
<div class="mt-3 text-center auth-footer">
    <span class="text-muted">Tayari una akaunti?</span>
    <a href="{{ route('login') }}" class="text-decoration-none fw-bold">
        Ingia Hapa
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
    // Generate UUID on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Generate a v4 UUID
        const uuid = crypto.randomUUID ? crypto.randomUUID() : generateUUID();
        document.getElementById('uuid').value = uuid;
        
        // Initial validation check on page load (if there's old value)
        const phoneInput = document.getElementById('phone_number');
        if (phoneInput.value) {
            validatePhoneInput(phoneInput);
        }
        
        // Phone number validation before submit
        const form = document.getElementById('registerForm');
        
        form.addEventListener('submit', function(e) {
            let phone = phoneInput.value.trim();
            
            // Remove any non-digit characters
            phone = phone.replace(/\D/g, '');
            
            // Check if it starts with '0'
            if (phone.startsWith('0')) {
                e.preventDefault();
                showPhoneError('Ondoa "0" mwanzoni. Mfano: 615004300');
                phoneInput.focus();
                return false;
            }
            
            // Check if phone is empty
            if (phone.length === 0) {
                e.preventDefault();
                showPhoneError('Tafadhali weka namba ya simu.');
                phoneInput.focus();
                return false;
            }
            
            // Check minimum length (9 digits for Tanzania numbers)
            if (phone.length < 9) {
                e.preventDefault();
                showPhoneError('Namba ya simu ni fupi sana. Weka namba kamili.');
                phoneInput.focus();
                return false;
            }
            
            // Check if starts with valid prefix (Tanzania numbers start with 6, 7, or 8)
            const firstDigit = phone.charAt(0);
            if (!['6', '7', '8'].includes(firstDigit)) {
                e.preventDefault();
                showPhoneError('Namba ya simu si sahihi. Namba za Tanzania huanza na 6, 7, au 8.');
                phoneInput.focus();
                return false;
            }
            
            // Update the input value to cleaned version
            phoneInput.value = phone;
            
            // Hide any error message
            hidePhoneError();
        });
    });
    
    // Validate phone input in real-time
    function validatePhoneInput(input) {
        let phone = input.value;
        const errorDiv = document.getElementById('phoneError');
        const errorMessage = document.getElementById('phoneErrorMessage');
        
        // Remove any non-digit characters
        phone = phone.replace(/\D/g, '');
        input.value = phone;
        
        // Check for errors
        let error = null;
        
        if (phone.length === 0) {
            // No error yet, user just started typing
            hidePhoneError();
            input.classList.remove('is-invalid');
            return true;
        }
        
        if (phone.startsWith('0')) {
            error = 'Ondoa "0" mwanzoni. Mfano: 615004300';
        } else if (phone.length > 0 && phone.length < 9) {
            error = 'Namba fupi.........';
        } else if (phone.length >= 9) {
            const firstDigit = phone.charAt(0);
            if (!['6', '7', '8'].includes(firstDigit)) {
                error = 'Namba za Tanzania huanza na 6, 7, au 8.';
            }
        }
        
        if (error) {
            showPhoneError(error);
            input.classList.add('is-invalid');
            return false;
        } else {
            hidePhoneError();
            input.classList.remove('is-invalid');
            return true;
        }
    }
    
    function showPhoneError(message) {
        const errorDiv = document.getElementById('phoneError');
        const errorMessage = document.getElementById('phoneErrorMessage');
        errorMessage.textContent = message;
        errorDiv.style.display = 'block';
    }
    
    function hidePhoneError() {
        const errorDiv = document.getElementById('phoneError');
        errorDiv.style.display = 'none';
    }
    
    // Fallback UUID generator for older browsers
    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            const r = Math.random() * 16 | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
    
    // Toggle password visibility
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
