<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Room;
use App\Models\Complaint;
use App\Models\Event;
use App\Models\LeaveRequest;
use App\Models\HostelFee;
use App\Models\PaymentReceipt;
use App\Models\Registration;
use App\Models\AttendanceRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiController extends Controller
{
    /**
     * Live dashboard stats — polled via AJAX every 30s.
     */
    public function stats(): JsonResponse
    {
        $totalStudents = User::where('role', 'student')->count();
        $totalRooms = Room::count();
        $pendingComplaints = Complaint::where('status', 'pending')->count();
        $upcomingEvents = Event::where('status', 'upcoming')->count();
        $pendingLeaves = LeaveRequest::where('status', 'pending')->count();

        // Revenue stats
        $totalRevenue = HostelFee::where('status', 'paid')->sum('amount');
        $pendingFees = HostelFee::where('status', 'pending')->sum('amount');
        $thisMonthRevenue = HostelFee::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        // Attendance rate (last 7 days)
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

        // 7-day trend sparklines
        $studentTrend = [];
        $complaintTrend = [];
        $revenueTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $studentTrend[] = User::where('role', 'student')
                ->whereDate('created_at', '<=', $date)->count();
            $complaintTrend[] = Complaint::where('status', 'pending')
                ->whereDate('created_at', '<=', $date)->count();
            $revenueTrend[] = (float) HostelFee::where('status', 'paid')
                ->whereDate('paid_at', $date)->sum('amount');
        }

        return response()->json([
            'totalStudents'     => $totalStudents,
            'totalRooms'        => $totalRooms,
            'pendingComplaints' => $pendingComplaints,
            'upcomingEvents'    => $upcomingEvents,
            'pendingLeaves'     => $pendingLeaves,
            'totalRevenue'      => $totalRevenue,
            'pendingFees'       => $pendingFees,
            'thisMonthRevenue'  => $thisMonthRevenue,
            'avgAttendance'     => $avgAttendance,
            'trends' => [
                'students'   => $studentTrend,
                'complaints' => $complaintTrend,
                'revenue'    => $revenueTrend,
            ],
            'timestamp' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Room heat map data for interactive floor plan.
     */
    public function roomHeatMap(): JsonResponse
    {
        $rooms = Room::all();
        $data = [];

        foreach ($rooms as $room) {
            $occupants = $room->occupantsCount();
            $occupantList = [];
            if ($room->students) {
                foreach ($room->students as $s) {
                    $occupantList[] = [
                        'id' => $s->user_id,
                        'name' => $s->user->name ?? 'Unknown',
                        'roll_no' => $s->roll_no,
                    ];
                }
            }

            $data[] = [
                'id'         => $room->id,
                'number'     => $room->number,
                'floor'      => $room->floor ?? 1,
                'type'       => $room->type ?? 'standard',
                'capacity'   => $room->capacity,
                'occupants'  => $occupants,
                'percentage' => $room->capacity > 0 ? round(($occupants / $room->capacity) * 100) : 0,
                'status'     => $room->status ?? 'active',
                'residents'  => $occupantList,
            ];
        }

        return response()->json($data);
    }
}
