<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentReceipt;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    /**
     * View receipt as printable page / PDF-style.
     */
    public function show(int $id)
    {
        $receipt = PaymentReceipt::with(['student.studentDetail', 'hostelFee'])->findOrFail($id);
        return view('admin.receipt', compact('receipt'));
    }
}
