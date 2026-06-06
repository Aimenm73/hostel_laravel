<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostelFee;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class HostelFeeController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $query = HostelFee::with('student.studentDetail')->orderBy('due_date');

        if ($status) {
            $query->where('status', $status);
        }

        $fees = $query->paginate(15);
        $students = User::where('role', 'student')->orderBy('name')->get(['id', 'name', 'email']);

        $stats = [
            'pending' => HostelFee::where('status', 'pending')->sum('amount'),
            'paid' => HostelFee::where('status', 'paid')->sum('amount'),
            'overdue' => HostelFee::where('status', 'pending')->where('due_date', '<', today())->count(),
        ];

        return view('admin.fees', compact('fees', 'students', 'stats', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'title' => 'required|string|max:120',
            'category' => 'required|in:hostel,mess,security,other',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $fee = HostelFee::create([
            ...$request->only('student_id', 'title', 'category', 'amount', 'due_date', 'notes'),
            'created_by' => auth()->id(),
        ]);

        Notification::create([
            'user_id' => $fee->student_id,
            'message' => "New fee: {$fee->title} — Rs. " . number_format($fee->amount, 0) . " due " . $fee->due_date->format('M d'),
        ]);

        return back()->with('success', 'Fee added to student ledger.');
    }

    public function markPaid(HostelFee $fee)
    {
        $fee->update(['status' => 'paid', 'paid_at' => now()]);

        Notification::create([
            'user_id' => $fee->student_id,
            'message' => "Payment recorded: {$fee->title}",
        ]);

        return back()->with('success', 'Marked as paid.');
    }

    public function destroy(HostelFee $fee)
    {
        $fee->delete();
        return back()->with('success', 'Fee entry removed.');
    }
}
