<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RollCallSession;
use App\Models\AttendanceRecord;
use App\Models\User;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class RollCallController extends Controller
{
    public function index()
    {
        $sessions = RollCallSession::withCount('records')
            ->orderByDesc('session_date')
            ->paginate(10);

        $activeSession = RollCallSession::where('status', 'open')
            ->where('session_date', today())
            ->with(['records.student.studentDetail'])
            ->first();

        $totalStudents = User::where('role', 'student')->count();

        return view('admin.roll_call', compact('sessions', 'activeSession', 'totalStudents'));
    }

    public function start(Request $request)
    {
        $request->validate(['title' => 'nullable|string|max:100']);

        RollCallSession::where('status', 'open')->where('session_date', today())->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        $session = RollCallSession::create([
            'session_date' => today(),
            'title' => $request->title ?? 'Night Roll Call',
            'qr_token' => RollCallSession::generateToken(),
            'status' => 'open',
            'opened_by' => auth()->id(),
        ]);

        ActivityLogger::log('roll_call_started', $session->title, 'fa-clipboard-check', '#4361ee');

        return back()->with('success', 'Roll call session started. Display the QR for students.');
    }

    public function close(RollCallSession $session)
    {
        $session->update(['status' => 'closed', 'closed_at' => now()]);
        return back()->with('success', 'Roll call session closed.');
    }

    public function manualMark(Request $request, RollCallSession $session)
    {
        $request->validate(['student_id' => 'required|exists:users,id']);

        AttendanceRecord::firstOrCreate(
            ['roll_call_session_id' => $session->id, 'student_id' => $request->student_id],
            ['method' => 'manual', 'marked_at' => now()]
        );

        return back()->with('success', 'Student marked present.');
    }
}
