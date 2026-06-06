<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NoticePost;
use Illuminate\Http\Request;

class NoticeBoardController extends Controller
{
    public function index(Request $request)
    {
        $floor = $request->get('floor');
        $query = NoticePost::with(['user', 'comments.user'])->withCount('comments');

        if ($floor !== null && $floor !== '') {
            $query->where(function ($q) use ($floor) {
                $q->where('floor', $floor)->orWhereNull('floor');
            });
        }

        $posts = $query->orderByDesc('is_pinned')->orderByDesc('created_at')->paginate(12);

        return view('admin.notice_board', compact('posts', 'floor'));
    }

    public function destroy(NoticePost $notice)
    {
        $notice->delete();
        return back()->with('success', 'Post removed.');
    }

    public function togglePin(NoticePost $notice)
    {
        $notice->update(['is_pinned' => !$notice->is_pinned]);
        return back()->with('success', 'Pin status updated.');
    }
}
