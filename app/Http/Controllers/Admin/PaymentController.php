<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $pendingPayments = Registration::where('payment_status', 'pending')
            ->with(['student', 'event'])
            ->get();

        $approvedPayments = Registration::where('payment_status', 'approved')
            ->with(['student', 'event'])
            ->orderBy('payment_date', 'desc')
            ->limit(20)
            ->get();

        return view('admin.payments', compact('pendingPayments', 'approvedPayments'));
    }

    public function approve($id)
    {
        $registration = Registration::findOrFail($id);
        $registration->update([
            'payment_status' => 'approved',
            'payment_date' => now(),
        ]);

        return back()->with('success', 'Payment approved.');
    }

    public function reject($id)
    {
        $registration = Registration::findOrFail($id);
        $registration->update([
            'payment_status' => 'rejected',
        ]);

        return back()->with('success', 'Payment rejected.');
    }
}
