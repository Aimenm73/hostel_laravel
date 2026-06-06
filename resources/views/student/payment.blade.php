@extends('layouts.student')
@section('title', 'Make Payment')
@section('page-title', 'Pay Fee')
@section('content')
<div style="max-width:600px;margin:0 auto;">
    <div class="card" style="overflow:visible;">
        <div class="card-header">
            <h3><i class="fas fa-credit-card"></i> Secure Payment</h3>
            <span class="badge badge-pending">{{ $fee->category }}</span>
        </div>
        <div class="card-body">
            {{-- Fee Summary --}}
            <div class="payment-summary">
                <div class="payment-summary-row">
                    <span>Fee</span>
                    <strong>{{ $fee->title }}</strong>
                </div>
                <div class="payment-summary-row">
                    <span>Due Date</span>
                    <strong>{{ $fee->due_date ? \Carbon\Carbon::parse($fee->due_date)->format('M d, Y') : 'N/A' }}</strong>
                </div>
                <div class="payment-summary-row total">
                    <span>Amount to Pay</span>
                    <strong class="payment-amount">Rs. {{ number_format($fee->amount, 2) }}</strong>
                </div>
            </div>

            <form method="POST" action="{{ route('student.payment.store') }}" id="paymentForm">
                @csrf
                <input type="hidden" name="fee_id" value="{{ $fee->id }}">

                <div class="payment-card-visual">
                    <div class="credit-card-preview" id="cardPreview">
                        <div class="cc-chip"><i class="fas fa-sim-card"></i></div>
                        <div class="cc-number" id="ccDisplay">•••• •••• •••• ••••</div>
                        <div class="cc-bottom">
                            <div><span>Card Holder</span><strong id="ccName">YOUR NAME</strong></div>
                            <div><span>Expires</span><strong id="ccExpiry">MM/YY</strong></div>
                        </div>
                        <div class="cc-brand"><i class="fab fa-cc-visa"></i></div>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-user"></i> Card Holder Name</label>
                    <input type="text" name="card_name" class="form-control" placeholder="John Doe" required
                           oninput="document.getElementById('ccName').textContent=this.value||'YOUR NAME'">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-credit-card"></i> Card Number</label>
                    <input type="text" name="card_number" class="form-control" placeholder="4242 4242 4242 4242"
                           maxlength="19" required oninput="formatCardNumber(this)">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Expiry Date</label>
                        <input type="text" name="card_expiry" class="form-control" placeholder="MM/YY"
                               maxlength="5" required oninput="formatExpiry(this)">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> CVV</label>
                        <input type="password" name="card_cvv" class="form-control" placeholder="•••"
                               maxlength="3" required>
                    </div>
                </div>

                <div class="payment-security">
                    <i class="fas fa-shield-alt"></i> Your payment is secured with 256-bit SSL encryption
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;padding:14px;font-size:16px;border-radius:14px;margin-top:8px;" id="payBtn">
                    <i class="fas fa-lock"></i> Pay Rs. {{ number_format($fee->amount, 2) }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
function formatCardNumber(el) {
    let v = el.value.replace(/\D/g, '').substring(0, 16);
    el.value = v.replace(/(\d{4})(?=\d)/g, '$1 ');
    document.getElementById('ccDisplay').textContent = el.value || '•••• •••• •••• ••••';
    // Detect card brand
    const brand = document.querySelector('.cc-brand i');
    if (v.startsWith('4')) brand.className = 'fab fa-cc-visa';
    else if (v.startsWith('5')) brand.className = 'fab fa-cc-mastercard';
    else if (v.startsWith('3')) brand.className = 'fab fa-cc-amex';
    else brand.className = 'fab fa-cc-visa';
}
function formatExpiry(el) {
    let v = el.value.replace(/\D/g, '').substring(0, 4);
    if (v.length >= 2) v = v.substring(0, 2) + '/' + v.substring(2);
    el.value = v;
    document.getElementById('ccExpiry').textContent = v || 'MM/YY';
}
document.getElementById('paymentForm').addEventListener('submit', function() {
    const btn = document.getElementById('payBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
});
</script>
@endsection
