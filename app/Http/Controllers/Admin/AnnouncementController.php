<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.announcements', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'type' => 'required|in:general,urgent,maintenance',
        ]);

        Announcement::create($request->only('title', 'content', 'type'));

        // Notify all students
        $students = User::where('role', 'student')->get();
        foreach ($students as $student) {
            Notification::create([
                'user_id' => $student->id,
                'message' => "New announcement: {$request->title}",
            ]);
        }

        return back()->with('success', 'Announcement created successfully.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'type' => 'required|in:general,urgent,maintenance',
        ]);

        $announcement->update($request->only('title', 'content', 'type'));

        return back()->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted successfully.');
    }
}
