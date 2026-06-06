@extends('layouts.student')
@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>All Notifications</h3>
        <form method="POST" action="{{ route('student.notifications.readAll') }}">@csrf @method('PATCH')<button class="btn btn-sm btn-primary">Mark All Read</button></form>
    </div>
    <div class="card-body" style="padding:0;">
        @forelse($notifications as $n)
            <div class="notif-item {{ $n->is_read ? '' : 'unread' }}" style="padding:16px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-size:14px;">{{ $n->message }}</div>
                    <div style="font-size:12px;color:var(--text-light);margin-top:4px;">{{ $n->created_at ? $n->created_at->diffForHumans() : '' }}</div>
                </div>
                @if(!$n->is_read)
                    <form method="POST" action="{{ route('student.notifications.read', $n->id) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline">Read</button></form>
                @endif
            </div>
        @empty
            <div style="text-align:center;padding:40px;color:var(--text-light);">No notifications</div>
        @endforelse
    </div>
    <div class="pagination-wrapper" style="padding:16px;">{{ $notifications->links() }}</div>
</div>
@endsection
