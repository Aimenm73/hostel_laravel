<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Notification;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $leaveRequests = LeaveRequest::with(['student.studentDetail', 'reviewer'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.leave_requests', compact('leaveRequests'));
    }

    public function approve($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        Notification::create([
            'user_id' => $leaveRequest->student_id,
            'message' => 'Your leave request has been approved.',
        ]);

        return back()->with('success', 'Leave request approved.');
    }

    public function reject($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        Notification::create([
            'user_id' => $leaveRequest->student_id,
            'message' => 'Your leave request has been rejected.',
        ]);

        return back()->with('success', 'Leave request rejected.');
    }
}
