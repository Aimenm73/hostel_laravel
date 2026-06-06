<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\RollCallSession;
use App\Models\AttendanceRecord;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class RollCallController extends Controller
{
    public function index()
    {
        $activeSession = RollCallSession::where('status', 'open')
            ->where('session_date', today())
            ->first();

        $myRecord = null;
        if ($activeSession) {
            $myRecord = AttendanceRecord::where('roll_call_session_id', $activeSession->id)
                ->where('student_id', auth()->id())
                ->first();
        }

        $history = AttendanceRecord::where('student_id', auth()->id())
            ->with('session')
            ->orderByDesc('marked_at')
            ->limit(15)
            ->get();

        return view('student.attendance', compact('activeSession', 'myRecord', 'history'));
    }

    public function checkIn(Request $request, string $token)
    {
        $session = RollCallSession::where('qr_token', $token)->where('status', 'open')->first();

        if (!$session) {
            return redirect()->route('student.attendance.index')
                ->with('error', 'This roll call session is closed or invalid.');
        }

        return $this->markPresent($session, 'qr');
    }

    public function checkInActive()
    {
        $session = RollCallSession::where('status', 'open')->where('session_date', today())->first();

        if (!$session) {
            return back()->with('error', 'No active roll call right now.');
        }

        return $this->markPresent($session, 'app');
    }

    private function markPresent(RollCallSession $session, string $method)
    {
        $existing = AttendanceRecord::where('roll_call_session_id', $session->id)
            ->where('student_id', auth()->id())
            ->first();

        if ($existing) {
            return redirect()->route('student.attendance.index')
                ->with('success', 'You are already marked present for today.');
        }

        AttendanceRecord::create([
            'roll_call_session_id' => $session->id,
            'student_id' => auth()->id(),
            'method' => $method,
            'marked_at' => now(),
        ]);

        ActivityLogger::log('attendance_marked', 'Present for ' . $session->title, 'fa-clipboard-check', '#06d6a0');

        return redirect()->route('student.attendance.index')
            ->with('success', 'Attendance recorded. Welcome back!');
    }
}
