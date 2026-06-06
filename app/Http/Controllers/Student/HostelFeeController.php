<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\HostelFee;
use App\Models\Registration;

class HostelFeeController extends Controller
{
    public function index()
    {
        $fees = HostelFee::where('student_id', auth()->id())
            ->orderByRaw("FIELD(status, 'pending', 'paid')")
            ->orderBy('due_date')
            ->get();

        $eventPayments = Registration::where('student_id', auth()->id())
            ->with('event')
            ->orderByDesc('registered_at')
            ->get();

        $totalPending = $fees->where('status', 'pending')->sum('amount');
        $totalPaid = $fees->where('status', 'paid')->sum('amount');

        return view('student.fees', compact('fees', 'eventPayments', 'totalPending', 'totalPaid'));
    }
}
