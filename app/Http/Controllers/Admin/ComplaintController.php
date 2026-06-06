<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintMessage;
use App\Models\Notification;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        $query = Complaint::with('student')
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->orderBy('created_at', 'desc');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $complaints = $query->paginate(10);

        return view('admin.complaints', compact('complaints', 'status'));
    }

    public function show(Complaint $complaint)
    {
        $complaint->load(['student', 'messages.sender']);
        return response()->json($complaint);
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved',
        ]);

        $complaint->update([
            'status' => $request->status,
            'resolved_at' => $request->status === 'resolved' ? now() : null,
        ]);

        Notification::create([
            'user_id' => $complaint->student_id,
            'message' => "Your complaint '{$complaint->title}' status updated to {$request->status}",
        ]);

        return back()->with('success', 'Status updated successfully.');
    }

    public function reply(Request $request, Complaint $complaint)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        ComplaintMessage::create([
            'complaint_id' => $complaint->id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return back()->with('success', 'Reply sent successfully.');
    }
}
