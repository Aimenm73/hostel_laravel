<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitorPass;
use App\Models\Notification;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class VisitorPassController extends Controller
{
    public function index()
    {
        $passes = VisitorPass::with(['student.studentDetail'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('visit_date', 'desc')
            ->paginate(12);

        $pendingCount = VisitorPass::where('status', 'pending')->count();

        return view('admin.visitor_passes', compact('passes', 'pendingCount'));
    }

    public function approve($id)
    {
        $pass = VisitorPass::findOrFail($id);
        $pass->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        Notification::create([
            'user_id' => $pass->student_id,
            'message' => "Visitor pass approved for {$pass->visitor_name} on {$pass->visit_date->format('M d, Y')}.",
        ]);

        ActivityLogger::log('visitor_pass_approved', "Approved pass for {$pass->visitor_name}", 'fa-id-card', '#06d6a0');

        return back()->with('success', 'Visitor pass approved.');
    }

    public function reject($id)
    {
        $pass = VisitorPass::findOrFail($id);
        $pass->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        Notification::create([
            'user_id' => $pass->student_id,
            'message' => "Visitor pass rejected for {$pass->visitor_name}.",
        ]);

        ActivityLogger::log('visitor_pass_rejected', "Rejected pass for {$pass->visitor_name}", 'fa-id-card', '#ef476f');

        return back()->with('success', 'Visitor pass rejected.');
    }
}
