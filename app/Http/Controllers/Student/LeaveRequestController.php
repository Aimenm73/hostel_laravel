<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $leaveRequests = LeaveRequest::where('student_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.leave_requests', compact('leaveRequests'));
    }

    public function create()
    {
        return view('student.leave_request_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        // Check for overlapping leave requests
        $overlap = LeaveRequest::where('student_id', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('start_date', '<=', $request->start_date)
                         ->where('end_date', '>=', $request->end_date);
                  });
            })
            ->exists();

        if ($overlap) {
            return back()->with('error', 'You already have a pending or approved leave request for overlapping dates.');
        }

        LeaveRequest::create([
            'student_id' => auth()->id(),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
        ]);

        ActivityLogger::log(
            'leave_requested',
            'Leave request: ' . $request->start_date . ' to ' . $request->end_date,
            'fa-calendar-minus',
            '#7c9cff'
        );

        return redirect()->route('student.leaveRequests.index')->with('success', 'Leave request submitted successfully.');
    }
}
