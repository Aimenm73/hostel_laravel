<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAllRead(Request $request)
    {
        auth()->user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
