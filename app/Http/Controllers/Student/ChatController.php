<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    /**
     * Chat page — student can message any admin.
     */
    public function index()
    {
        $studentId = auth()->id();

        // Get admins
        $admins = User::where('role', 'admin')->get()->map(function ($admin) use ($studentId) {
            $lastMsg = ChatMessage::where(function ($q) use ($studentId, $admin) {
                $q->where('sender_id', $studentId)->where('receiver_id', $admin->id);
            })->orWhere(function ($q) use ($studentId, $admin) {
                $q->where('sender_id', $admin->id)->where('receiver_id', $studentId);
            })->orderBy('created_at', 'desc')->first();

            $unread = ChatMessage::where('sender_id', $admin->id)
                ->where('receiver_id', $studentId)
                ->where('is_read', false)
                ->count();

            $admin->last_message = $lastMsg;
            $admin->unread_count = $unread;
            return $admin;
        });

        return view('student.chat', compact('admins'));
    }

    /**
     * Get messages between student and a specific admin (AJAX).
     */
    public function messages(int $adminId): JsonResponse
    {
        $studentId = auth()->id();

        // Mark messages from admin as read
        ChatMessage::where('sender_id', $adminId)
            ->where('receiver_id', $studentId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = ChatMessage::where(function ($q) use ($studentId, $adminId) {
            $q->where('sender_id', $studentId)->where('receiver_id', $adminId);
        })->orWhere(function ($q) use ($studentId, $adminId) {
            $q->where('sender_id', $adminId)->where('receiver_id', $studentId);
        })->orderBy('created_at', 'asc')
          ->limit(100)
          ->get()
          ->map(fn($m) => [
              'id'        => $m->id,
              'message'   => $m->message,
              'is_mine'   => $m->sender_id == $studentId,
              'time'      => $m->created_at->format('h:i A'),
              'date'      => $m->created_at->format('M d'),
              'is_read'   => $m->is_read,
          ]);

        return response()->json($messages);
    }

    /**
     * Send a message from student to admin (AJAX).
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

        // Create notification for admin
        Notification::create([
            'user_id' => $request->receiver_id,
            'message' => 'New message from ' . auth()->user()->name . ': ' . \Illuminate\Support\Str::limit($request->message, 50),
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
     * Unread count.
     */
    public function unreadCount(): JsonResponse
    {
        $count = ChatMessage::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
