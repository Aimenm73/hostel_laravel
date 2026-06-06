<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PaymentReceipt;
use App\Models\HostelFee;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Show payment page for a specific fee.
     */
    public function create(int $feeId)
    {
        $fee = HostelFee::where('student_id', auth()->id())->findOrFail($feeId);

        if ($fee->status === 'paid') {
            return redirect()->route('student.fees.index')->with('error', 'This fee has already been paid.');
        }

        return view('student.payment', compact('fee'));
    }

    /**
     * Process payment (simulated gateway).
     */
    public function store(Request $request)
    {
        $request->validate([
            'fee_id' => 'required|integer',
            'card_number' => 'required|string|min:16',
            'card_expiry' => 'required|string',
            'card_cvv' => 'required|string|size:3',
            'card_name' => 'required|string|max:100',
        ]);

        $fee = HostelFee::where('student_id', auth()->id())->findOrFail($request->fee_id);

        if ($fee->status === 'paid') {
            return redirect()->route('student.fees.index')->with('error', 'Already paid.');
        }

        // Simulate payment processing
        $fee->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Generate receipt
        $receipt = PaymentReceipt::create([
            'student_id' => auth()->id(),
            'invoice_no' => PaymentReceipt::generateInvoiceNo(),
            'description' => $fee->title,
            'amount' => $fee->amount,
            'payment_method' => 'card',
            'status' => 'paid',
            'reference' => 'TXN-' . strtoupper(\Illuminate\Support\Str::random(10)),
            'hostel_fee_id' => $fee->id,
        ]);

        ActivityLogger::log(
            'payment_made',
            auth()->user()->name . ' paid Rs. ' . number_format($fee->amount) . ' for ' . $fee->title,
            'fa-credit-card',
            '#06d6a0'
        );

        return redirect()->route('student.receipt.show', $receipt->id)->with('success', 'Payment successful!');
    }

    /**
     * View receipt.
     */
    public function receipt(int $id)
    {
        $receipt = PaymentReceipt::where('student_id', auth()->id())
            ->with(['student.studentDetail', 'hostelFee'])
            ->findOrFail($id);

        return view('student.receipt', compact('receipt'));
    }
}
