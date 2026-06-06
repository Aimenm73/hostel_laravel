<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Event;
use App\Models\LeaveRequest;
use App\Models\Registration;
use App\Models\ActivityLog;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->load('studentDetail.room');

        $totalComplaints = Complaint::where('student_id', $user->id)->count();
        $pendingComplaints = Complaint::where('student_id', $user->id)->where('status', 'pending')->count();
        $eventsRegistered = Registration::where('student_id', $user->id)->count();
        $roomNumber = $user->studentDetail && $user->studentDetail->room
            ? $user->studentDetail->room->number : 'Not Assigned';

        $recentComplaints = Complaint::where('student_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $upcomingEvents = Event::where('status', 'upcoming')
            ->where('date', '>=', now())
            ->orderBy('date')
            ->limit(3)
            ->get();

        // Check registration status for each event
        $registeredEventIds = Registration::where('student_id', $user->id)
            ->pluck('event_id')
            ->toArray();

        // ── Chart 1: My Complaints by Status (Doughnut) ──
        $myComplaintsByStatus = Complaint::where('student_id', $user->id)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Chart 2: My Leave Requests by Status (Bar) ──
        $myLeavesByStatus = LeaveRequest::where('student_id', $user->id)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Feature 5: Activity Log Timeline (student's own actions) ──
        $activityLogs = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $announcementTicker = Announcement::orderBy('created_at', 'desc')->limit(5)->get();
        $unreadNotifications = $user->unreadNotificationsCount();

        return view('student.dashboard', compact(
            'user', 'totalComplaints', 'pendingComplaints', 'eventsRegistered',
            'roomNumber', 'recentComplaints', 'upcomingEvents', 'registeredEventIds',
            'myComplaintsByStatus', 'myLeavesByStatus', 'activityLogs',
            'announcementTicker', 'unreadNotifications'
        ));
    }
}
