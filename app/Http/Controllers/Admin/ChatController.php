<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    /**
     * Chat page — lists all students who have messaged admin.
     */
    public function index()
    {
        $adminId = auth()->id();

        // Get unique students who have chatted with this admin
        $studentIds = ChatMessage::where('receiver_id', $adminId)
            ->orWhere('sender_id', $adminId)
            ->get()
            ->map(fn($m) => $m->sender_id == $adminId ? $m->receiver_id : $m->sender_id)
            ->unique()
            ->values();

        $conversations = User::whereIn('id', $studentIds)
            ->where('role', 'student')
            ->get()
            ->map(function ($student) use ($adminId) {
                $lastMsg = ChatMessage::where(function ($q) use ($adminId, $student) {
                    $q->where('sender_id', $adminId)->where('receiver_id', $student->id);
                })->orWhere(function ($q) use ($adminId, $student) {
                    $q->where('sender_id', $student->id)->where('receiver_id', $adminId);
                })->orderBy('created_at', 'desc')->first();

                $unread = ChatMessage::where('sender_id', $student->id)
                    ->where('receiver_id', $adminId)
                    ->where('is_read', false)
                    ->count();

                $student->last_message = $lastMsg;
                $student->unread_count = $unread;
                return $student;
            })
            ->sortByDesc(fn($s) => $s->last_message?->created_at);

        return view('admin.chat', compact('conversations'));
    }

    /**
     * Get messages between admin and a specific student (AJAX).
     */
    public function messages(int $studentId): JsonResponse
    {
        $adminId = auth()->id();

        // Mark messages from student as read
        ChatMessage::where('sender_id', $studentId)
            ->where('receiver_id', $adminId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = ChatMessage::where(function ($q) use ($adminId, $studentId) {
            $q->where('sender_id', $adminId)->where('receiver_id', $studentId);
        })->orWhere(function ($q) use ($adminId, $studentId) {
            $q->where('sender_id', $studentId)->where('receiver_id', $adminId);
        })->orderBy('created_at', 'asc')
          ->limit(100)
          ->get()
          ->map(fn($m) => [
              'id'        => $m->id,
              'message'   => $m->message,
              'is_mine'   => $m->sender_id == $adminId,
              'time'      => $m->created_at->format('h:i A'),
              'date'      => $m->created_at->format('M d'),
              'is_read'   => $m->is_read,
          ]);

        return response()->json($messages);
    }

    /**
     * Send a message from admin to student (AJAX).
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $msg = ChatMessage::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        // Also create a notification
        Notification::create([
            'user_id' => $request->receiver_id,
            'message' => 'New message from Admin: ' . \Illuminate\Support\Str::limit($request->message, 50),
        ]);

        return response()->json([
            'id'      => $msg->id,
            'message' => $msg->message,
            'is_mine' => true,
            'time'    => $msg->created_at->format('h:i A'),
            'date'    => $msg->created_at->format('M d'),
        ]);
    }

    /**
     * Unread count for topbar badge.
     */
    public function unreadCount(): JsonResponse
    {
        $count = ChatMessage::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
