@extends('layouts.student')
@section('title', 'Complaint Details')
@section('page-title', 'Complaint Details')
@section('content')
<div class="card" style="max-width:750px;">
    <div class="card-header">
        <h3>{{ $complaint->title }}</h3>
        <span class="badge badge-{{ $complaint->status }}">{{ str_replace('_',' ',$complaint->status) }}</span>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;font-size:14px;">
            <p><strong>Category:</strong> {{ $complaint->category }}</p>
            <p><strong>Priority:</strong> <span class="badge badge-{{ $complaint->priority }}">{{ $complaint->priority }}</span></p>
            <p><strong>Date:</strong> {{ $complaint->created_at ? $complaint->created_at->format('M d, Y h:i A') : '' }}</p>
            <p><strong>Status:</strong> <span class="badge badge-{{ $complaint->status }}">{{ str_replace('_',' ',$complaint->status) }}</span></p>
        </div>
        <div style="margin-bottom:16px;"><strong>Description:</strong><p style="margin-top:6px;font-size:14px;line-height:1.6;">{{ $complaint->description }}</p></div>
        @if($complaint->image)
            <div style="margin-bottom:16px;"><img src="{{ asset('storage/complaints/' . $complaint->image) }}" style="max-width:100%;border-radius:8px;"></div>
        @endif

        <h4 style="margin-bottom:12px;">Messages</h4>
        <div class="message-thread">
            @forelse($complaint->messages as $m)
                <div class="message-bubble {{ $m->sender_id === auth()->id() ? 'mine' : '' }}">
                    <div class="sender">{{ $m->sender->name ?? '' }}</div>
                    {{ $m->message }}
                    <div class="time">{{ $m->created_at ? $m->created_at->format('M d, h:i A') : '' }}</div>
                </div>
            @empty
                <p style="text-align:center;color:var(--text-light);font-size:13px;padding:20px;">No messages yet</p>
            @endforelse
        </div>

        <form method="POST" action="{{ route('student.complaints.message', $complaint->id) }}">
            @csrf
            <div class="form-group"><textarea name="message" class="form-control" placeholder="Type your message..." required style="min-height:60px;"></textarea></div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane"></i> Send</button>
        </form>
    </div>
</div>
@endsection
