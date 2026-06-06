@extends('layouts.student')
@section('title', 'Payment Receipt')
@section('page-title', 'Payment Receipt')
@section('content')
<div style="max-width:700px;margin:0 auto;">
    <div class="receipt-wrapper" id="receiptArea">
        <div class="receipt-card">
            <div class="receipt-header">
                <div class="receipt-logo">
                    <i class="fas fa-building"></i>
                    <div>
                        <h2>COMSATS Hostel</h2>
                        <p>Management System</p>
                    </div>
                </div>
                <div class="receipt-stamp">
                    <i class="fas fa-check-circle"></i>
                    <span>PAID</span>
                </div>
            </div>

            <div class="receipt-divider">
                <div class="receipt-title">PAYMENT RECEIPT</div>
            </div>

            <div class="receipt-details">
                <div class="receipt-row">
                    <span>Invoice No</span>
                    <strong>{{ $receipt->invoice_no }}</strong>
                </div>
                <div class="receipt-row">
                    <span>Date</span>
                    <strong>{{ $receipt->created_at->format('F d, Y — h:i A') }}</strong>
                </div>
                <div class="receipt-row">
                    <span>Transaction Ref</span>
                    <strong>{{ $receipt->reference }}</strong>
                </div>
                <div class="receipt-row">
                    <span>Payment Method</span>
                    <strong style="text-transform:capitalize"><i class="fas fa-credit-card"></i> {{ $receipt->payment_method }}</strong>
                </div>
            </div>

            <div class="receipt-divider dashed"></div>

            <div class="receipt-details">
                <div class="receipt-row">
                    <span>Student Name</span>
                    <strong>{{ $receipt->student->name }}</strong>
                </div>
                <div class="receipt-row">
                    <span>Roll Number</span>
                    <strong>{{ $receipt->student->studentDetail->roll_no ?? 'N/A' }}</strong>
                </div>
                <div class="receipt-row">
                    <span>Department</span>
                    <strong>{{ $receipt->student->studentDetail->department ?? 'N/A' }}</strong>
                </div>
            </div>

            <div class="receipt-divider dashed"></div>

            <div class="receipt-details">
                <div class="receipt-row">
                    <span>Description</span>
                    <strong>{{ $receipt->description }}</strong>
                </div>
                <div class="receipt-row receipt-total">
                    <span>Amount Paid</span>
                    <strong>Rs. {{ number_format($receipt->amount, 2) }}</strong>
                </div>
            </div>

            <div class="receipt-footer">
                <p>This is a computer-generated receipt and does not require a signature.</p>
                <p>For queries, contact the hostel administration office.</p>
            </div>
        </div>
    </div>

    <div class="receipt-actions">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print"></i> Print Receipt
        </button>
        <a href="{{ route('student.fees.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Fees
        </a>
    </div>
</div>
@endsection
