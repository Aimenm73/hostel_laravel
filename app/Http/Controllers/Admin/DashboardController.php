<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Room;
use App\Models\Complaint;
use App\Models\Event;
use App\Models\LeaveRequest;
use App\Models\Registration;
use App\Models\Broadcast;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Models\HostelFee;
use App\Models\AttendanceRecord;
use App\Helpers\ActivityLogger;
use App\Mail\UrgentBroadcastMail;
use App\Services\SmsNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = User::where('role', 'student')->count();
        $totalRooms = Room::count();
        $pendingComplaints = Complaint::where('status', 'pending')->count();
        $upcomingEvents = Event::where('status', 'upcoming')->count();

        $recentComplaints = Complaint::where('status', 'pending')
            ->with('student')
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentStudents = User::where('role', 'student')
            ->with('studentDetail')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ── Chart 1: Complaints by Status (Doughnut) ──
        $complaintsByStatus = Complaint::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Chart 2: Monthly Registrations - last 6 months (Bar) ──
        $monthlyRegistrations = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Registration::whereYear('registered_at', $date->year)
                ->whereMonth('registered_at', $date->month)
                ->count();
            $monthlyRegistrations[$date->format('M Y')] = $count;
        }

        // ── Chart 3: Room Occupancy (Pie) ──
        $rooms = Room::all();
        $occupiedBeds = 0;
        $totalBeds = 0;
        foreach ($rooms as $room) {
            $totalBeds += $room->capacity;
            $occupiedBeds += $room->occupantsCount();
        }
        $roomOccupancy = [
            'occupied' => $occupiedBeds,
            'available' => max(0, $totalBeds - $occupiedBeds),
        ];

        // ── Chart 4: Leave Requests by Status (Bar) ──
        $leavesByStatus = LeaveRequest::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ── Feature 4: Recent Broadcasts ──
        $recentBroadcasts = Broadcast::with('admin')
            ->orderBy('sent_at', 'desc')
            ->limit(5)
            ->get();

        // ── Feature 5: Activity Log Timeline ──
        $activityLogs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // ── Revenue / Analytics Data ──
        $totalRevenue = HostelFee::where('status', 'paid')->sum('amount');
        $pendingFees = HostelFee::where('status', 'pending')->sum('amount');
        $thisMonthRevenue = HostelFee::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        // Attendance Rate (7 days)
        $totalSessions = DB::table('roll_call_sessions')
            ->where('session_date', '>=', now()->subDays(7))
            ->count();
        $avgAttendance = 0;
        if ($totalSessions > 0 && $totalStudents > 0) {
            $totalMarked = AttendanceRecord::whereHas('rollCallSession', function ($q) {
                $q->where('session_date', '>=', now()->subDays(7));
            })->count();
            $avgAttendance = round(($totalMarked / ($totalSessions * $totalStudents)) * 100, 1);
        }

        // ── 7-Day Trend Sparklines ──
        $trends = ['students' => [], 'complaints' => [], 'revenue' => []];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $trends['students'][] = User::where('role', 'student')
                ->whereDate('created_at', '<=', $date)->count();
            $trends['complaints'][] = Complaint::where('status', 'pending')
                ->whereDate('created_at', '<=', $date)->count();
            $trends['revenue'][] = (float) HostelFee::where('status', 'paid')
                ->whereDate('paid_at', $date)->sum('amount');
        }

        return view('admin.dashboard', compact(
            'totalStudents', 'totalRooms', 'pendingComplaints', 'upcomingEvents',
            'recentComplaints', 'recentStudents',
            'complaintsByStatus', 'monthlyRegistrations', 'roomOccupancy', 'leavesByStatus',
            'recentBroadcasts', 'activityLogs',
            'totalRevenue', 'pendingFees', 'thisMonthRevenue', 'avgAttendance', 'trends'
        ));
    }

    public function broadcast(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
            'type' => 'required|in:general,urgent,maintenance',
        ]);

        $students = User::where('role', 'student')->get();

        // Create broadcast record
        Broadcast::create([
            'admin_id' => auth()->id(),
            'message' => $request->message,
            'type' => $request->type,
            'recipients_count' => $students->count(),
        ]);

        $sms = app(SmsNotifier::class);
        $smsPrefix = $request->type === 'urgent' ? '[URGENT] ' : '';

        foreach ($students as $student) {
            Notification::create([
                'user_id' => $student->id,
                'message' => "[Broadcast] {$request->message}",
            ]);

            if (in_array($request->type, ['urgent', 'maintenance'], true)) {
                try {
                    Mail::to($student->email)->send(new UrgentBroadcastMail($request->message, $request->type));
                } catch (\Throwable $e) {
                    // Mail may use log driver in dev
                }
                if ($request->type === 'urgent') {
                    $sms->send($student, $smsPrefix . Str::limit($request->message, 140));
                }
            }
        }

        // Log the activity
        ActivityLogger::log(
            'broadcast_sent',
            "Broadcast sent to {$students->count()} students: " . Str::limit($request->message, 50),
            'fa-bullhorn',
            '#7c9cff'
        );

        return back()->with('success', "Broadcast sent to {$students->count()} students.");
    }
}
