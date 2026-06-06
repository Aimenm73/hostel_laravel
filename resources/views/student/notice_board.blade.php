@extends('layouts.student')
@section('title', 'Notice Board')
@section('page-title', 'Notice Board')
@section('content')
@include('partials.page-hero', [
    'icon' => 'fa-comments',
    'title' => 'Floor Notice Board',
    'subtitle' => 'Share updates with your floor mates',
    'actions' => '<button class="btn btn-primary" onclick="openModal(\'newPostModal\')"><i class="fas fa-pen"></i> New Post</button>'
])

<form method="GET" style="margin-bottom:20px;">
    <select name="floor" class="form-control" style="max-width:200px;" onchange="this.form.submit()">
        <option value="">All / Building</option>
        @for($f = 1; $f <= 6; $f++)
            <option value="{{ $f }}" {{ (string)$floorFilter === (string)$f ? 'selected' : '' }}>Floor {{ $f }}</option>
        @endfor
    </select>
</form>

<div class="notice-grid">
    @forelse($posts as $post)
        <div class="notice-card glass-card {{ $post->is_pinned ? 'pinned' : '' }}">
            @if($post->is_pinned)<span class="notice-pin"><i class="fas fa-thumbtack"></i></span>@endif
            <h4>{{ $post->title }}</h4>
            <div class="notice-meta">
                <span>{{ $post->user->name }}</span>
                <span>{{ $post->floor ? 'Floor '.$post->floor : 'All' }}</span>
                <span>{{ $post->created_at->diffForHumans() }}</span>
            </div>
            <div class="notice-body">{{ $post->body }}</div>
            <div class="notice-comments">
                @foreach($post->comments as $c)
                    <div class="notice-comment"><strong>{{ $c->user->name }}:</strong> {{ $c->body }}</div>
                @endforeach
                <form method="POST" action="{{ route('student.noticeBoard.comment', $post) }}" style="margin-top:10px;display:flex;gap:8px;">
                    @csrf
                    <input type="text" name="body" class="form-control" placeholder="Write a comment..." required style="flex:1;">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    @empty
        <p style="grid-column:1/-1;text-align:center;padding:60px;color:var(--text-light);">Be the first to post something!</p>
    @endforelse
</div>
<div style="margin-top:20px;">{{ $posts->links() }}</div>

<div class="modal-overlay" id="newPostModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header"><h3>New Post</h3><button class="modal-close" onclick="closeModal('newPostModal')">&times;</button></div>
        <form method="POST" action="{{ route('student.noticeBoard.store') }}">@csrf
            <div class="modal-body">
                <div class="form-group"><label>Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="form-group"><label>Message</label><textarea name="body" class="form-control" rows="4" required></textarea></div>
                <div class="form-group"><label>Floor (optional)</label>
                    <select name="floor" class="form-control">
                        <option value="">All residents</option>
                        @if($myFloor)<option value="{{ $myFloor }}" selected>My floor ({{ $myFloor }})</option>@endif
                        @for($f = 1; $f <= 6; $f++)<option value="{{ $f }}">Floor {{ $f }}</option>@endfor
                    </select>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal('newPostModal')">Cancel</button><button type="submit" class="btn btn-primary">Post</button></div>
        </form>
    </div>
</div>
@endsection
