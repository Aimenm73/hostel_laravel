<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\NoticePost;
use App\Models\NoticeComment;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class NoticeBoardController extends Controller
{
    public function index(Request $request)
    {
        $myFloor = auth()->user()->studentDetail?->room?->floor;
        $floorFilter = $request->get('floor', $myFloor);

        $posts = NoticePost::with(['user', 'comments.user'])
            ->withCount('comments')
            ->where(function ($q) use ($floorFilter) {
                $q->whereNull('floor');
                if ($floorFilter !== null && $floorFilter !== '') {
                    $q->orWhere('floor', $floorFilter);
                }
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('student.notice_board', compact('posts', 'myFloor', 'floorFilter'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'body' => 'required|string|max:2000',
            'floor' => 'nullable|integer|min:1',
        ]);

        NoticePost::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'body' => $request->body,
            'floor' => $request->floor,
        ]);

        ActivityLogger::log('notice_posted', $request->title, 'fa-thumbtack', '#8b5cf6');

        return back()->with('success', 'Posted to the notice board.');
    }

    public function comment(Request $request, NoticePost $notice)
    {
        $request->validate(['body' => 'required|string|max:1000']);

        NoticeComment::create([
            'notice_post_id' => $notice->id,
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        return back()->with('success', 'Comment added.');
    }
}
