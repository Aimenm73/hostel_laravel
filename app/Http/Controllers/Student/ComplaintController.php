<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintMessage;
use App\Models\Notification;
use App\Models\User;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::where('student_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.complaints', compact('complaints'));
    }

    public function create()
    {
        return view('student.complaint_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'category' => 'required|in:maintenance,noise,food,security,other',
            'priority' => 'required|in:low,medium,high',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120',
        ]);

        $data = $request->only('title', 'description', 'category', 'priority');
        $data['student_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/complaints', $filename);
            $data['image'] = $filename;
        }

        $complaint = Complaint::create($data);

        ActivityLogger::log(
            'complaint_submitted',
            'Filed complaint: ' . $request->title,
            'fa-exclamation-triangle',
            '#ffd166'
        );

        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'message' => "New complaint from " . auth()->user()->name . ": {$request->title}",
            ]);
        }

        return redirect()->route('student.complaints.index')->with('success', 'Complaint submitted successfully.');
    }

    public function show(Complaint $complaint)
    {
        if ($complaint->student_id !== auth()->id()) {
            abort(403);
        }

        $complaint->load('messages.sender');
        return view('student.complaint_show', compact('complaint'));
    }

    public function addMessage(Request $request, Complaint $complaint)
    {
        if ($complaint->student_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        ComplaintMessage::create([
            'complaint_id' => $complaint->id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return back()->with('success', 'Message sent.');
    }
}
