@extends('layouts.student')
@section('title', 'My Fees')
@section('page-title', 'My Fees')
@section('content')
@include('partials.page-hero', ['icon' => 'fa-wallet', 'title' => 'Fee Ledger', 'subtitle' => 'Hostel fees and event payment history'])

<div class="fee-summary-grid">
    <div class="fee-summary-card pending"><h4>Amount Due</h4><div class="amount">Rs. {{ number_format($totalPending, 0) }}</div></div>
    <div class="fee-summary-card paid"><h4>Total Paid</h4><div class="amount">Rs. {{ number_format($totalPaid, 0) }}</div></div>
    <div class="fee-summary-card" style="background:linear-gradient(135deg,#4361ee,#764ba2);"><h4>Entries</h4><div class="amount">{{ $fees->count() }}</div></div>
</div>

<div class="card glass-card" style="margin-bottom:28px;">
    <div class="card-header"><h3><i class="fas fa-building"></i> Hostel Fees</h3></div>
    @forelse($fees as $fee)
        <div class="fee-row">
            <div style="flex:1;">
                <strong>{{ $fee->title }}</strong>
                <div style="font-size:12px;color:var(--text-light);">{{ ucfirst($fee->category) }} · Due {{ $fee->due_date->format('M d, Y') }}</div>
            </div>
            <strong>Rs. {{ number_format($fee->amount, 0) }}</strong>
            <span class="badge badge-{{ $fee->status === 'paid' ? 'approved' : ($fee->due_date->isPast() ? 'urgent' : 'pending') }}">{{ $fee->status }}</span>
            @if($fee->status === 'paid' && $fee->paid_at)
                <small style="color:var(--text-light);">Paid {{ $fee->paid_at->format('M d') }}</small>
                @php $receipt = \App\Models\PaymentReceipt::where('hostel_fee_id', $fee->id)->first(); @endphp
                @if($receipt)
                    <a href="{{ route('student.receipt.show', $receipt->id) }}" class="btn btn-sm btn-outline"><i class="fas fa-receipt"></i> Receipt</a>
                @endif
            @elseif($fee->status === 'pending')
                <a href="{{ route('student.payment.create', $fee->id) }}" class="btn btn-sm btn-primary pulse-btn"><i class="fas fa-credit-card"></i> Pay Now</a>
            @endif
        </div>
    @empty
        <p style="text-align:center;padding:32px;color:var(--text-light);">No hostel fees on your account</p>
    @endforelse
</div>

<div class="card">
    <div class="card-header"><h3><i class="fas fa-calendar-alt"></i> Event Payments</h3></div>
    <div class="table-responsive">
        <table>
            <thead><tr><th>Event</th><th>Registered</th><th>Payment</th></tr></thead>
            <tbody>
            @forelse($eventPayments as $r)
                <tr>
                    <td>{{ $r->event->title ?? 'N/A' }}</td>
                    <td>{{ $r->registered_at?->format('M d, Y') }}</td>
                    <td><span class="badge badge-{{ $r->payment_status === 'approved' ? 'approved' : 'pending' }}">{{ $r->payment_status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--text-light);">No event registrations</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
