@extends('layouts.guest')
@section('title', 'Verify OTP - COMSATS Hostel')
@section('content')
<div class="login-page">
    <div class="otp-container">
        <div class="otp-card">
            <div class="otp-header">
                <div class="otp-shield">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2>Two-Factor Verification</h2>
                <p>We've sent a 6-digit code to <strong>{{ $maskedEmail }}</strong></p>
            </div>

            @if(session('error'))
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i>{{ session('error') }}<button class="alert-close" onclick="this.parentElement.remove()">&times;</button></div>
            @endif
            @if(session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}<button class="alert-close" onclick="this.parentElement.remove()">&times;</button></div>
            @endif

            <form method="POST" action="{{ route('otp.verify') }}" id="otpForm">
                @csrf
                <input type="hidden" name="user_id" value="{{ $userId }}">
                <div class="otp-inputs">
                    <input type="text" maxlength="1" class="otp-digit" data-index="0" inputmode="numeric" autofocus>
                    <input type="text" maxlength="1" class="otp-digit" data-index="1" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-digit" data-index="2" inputmode="numeric">
                    <div class="otp-separator">—</div>
                    <input type="text" maxlength="1" class="otp-digit" data-index="3" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-digit" data-index="4" inputmode="numeric">
                    <input type="text" maxlength="1" class="otp-digit" data-index="5" inputmode="numeric">
                </div>
                <input type="hidden" name="otp" id="otpHidden">

                <div class="otp-remember">
                    <label class="otp-checkbox-wrap">
                        <input type="checkbox" name="trust_device" value="1">
                        <span class="checkmark"></span>
                        Trust this device for 30 days
                    </label>
                </div>

                <button type="submit" class="btn btn-primary otp-submit" id="otpSubmitBtn" disabled>
                    <i class="fas fa-lock-open"></i> Verify & Login
                </button>
            </form>

            <div class="otp-footer">
                <div class="otp-timer">
                    <i class="fas fa-clock"></i> Code expires in <span id="otpTimer">5:00</span>
                </div>
                <form method="POST" action="{{ route('otp.resend') }}" style="display:inline;">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $userId }}">
                    <button type="submit" class="otp-resend-btn" id="resendBtn">
                        <i class="fas fa-redo"></i> Resend Code
                    </button>
                </form>
                <a href="{{ route('login') }}" class="otp-back">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function(){if(localStorage.getItem('theme')==='dark'){document.body.setAttribute('data-theme','dark');}})();

// OTP digit boxes logic
const digits = document.querySelectorAll('.otp-digit');
const hidden = document.getElementById('otpHidden');
const submitBtn = document.getElementById('otpSubmitBtn');

function updateHidden() {
    let code = '';
    digits.forEach(d => code += d.value);
    hidden.value = code;
    submitBtn.disabled = code.length < 6;
    if (code.length === 6) {
        submitBtn.classList.add('ready');
    } else {
        submitBtn.classList.remove('ready');
    }
}

digits.forEach((input, idx) => {
    input.addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '');
        if (this.value && idx < 5) {
            digits[idx + 1].focus();
        }
        updateHidden();
        // Auto-submit when all 6 digits entered
        if (hidden.value.length === 6) {
            setTimeout(() => document.getElementById('otpForm').submit(), 300);
        }
    });
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !this.value && idx > 0) {
            digits[idx - 1].focus();
        }
    });
    // Handle paste
    input.addEventListener('paste', function(e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
        pasted.split('').forEach((char, i) => {
            if (digits[i]) digits[i].value = char;
        });
        if (pasted.length > 0) digits[Math.min(pasted.length, 5)].focus();
        updateHidden();
    });
});

// Countdown timer
let totalSeconds = 300; // 5 minutes
const timerEl = document.getElementById('otpTimer');
const countdown = setInterval(() => {
    totalSeconds--;
    const m = Math.floor(totalSeconds / 60);
    const s = totalSeconds % 60;
    timerEl.textContent = m + ':' + String(s).padStart(2, '0');
    if (totalSeconds <= 60) timerEl.style.color = '#ef476f';
    if (totalSeconds <= 0) {
        clearInterval(countdown);
        timerEl.textContent = 'Expired';
    }
}, 1000);
</script>
@endsection
