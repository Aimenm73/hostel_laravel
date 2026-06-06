<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\VisitorPass;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class VisitorPassController extends Controller
{
    public function index()
    {
        $passes = VisitorPass::where('student_id', auth()->id())
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        return view('student.visitor_passes', compact('passes'));
    }

    public function create()
    {
        return view('student.visitor_pass_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'visitor_name' => 'required|string|max:100',
            'relationship' => 'nullable|string|max:80',
            'visit_date' => 'required|date|after_or_equal:today',
            'expected_arrival' => 'nullable|string|max:20',
            'purpose' => 'required|string|max:500',
        ]);

        VisitorPass::create([
            'student_id' => auth()->id(),
            'visitor_name' => $request->visitor_name,
            'relationship' => $request->relationship,
            'visit_date' => $request->visit_date,
            'expected_arrival' => $request->expected_arrival,
            'purpose' => $request->purpose,
        ]);

        ActivityLogger::log(
            'visitor_pass_requested',
            "Visitor pass: {$request->visitor_name} on {$request->visit_date}",
            'fa-id-card',
            '#7c9cff'
        );

        return redirect()->route('student.visitorPasses.index')->with('success', 'Visitor pass request submitted.');
    }
}
