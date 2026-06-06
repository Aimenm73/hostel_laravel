@extends('layouts.admin')
@section('title', 'Notice Board')
@section('page-title', 'Notice Board')
@section('content')
@include('partials.page-hero', ['icon' => 'fa-thumbtack', 'title' => 'Community Notice Board', 'subtitle' => 'Moderate floor posts and discussions'])

<form method="GET" style="margin-bottom:20px;display:flex;gap:10px;">
    <select name="floor" class="form-control" style="max-width:160px;" onchange="this.form.submit()">
        <option value="">All floors</option>
        @for($f = 1; $f <= 6; $f++)
            <option value="{{ $f }}" {{ (string)$floor === (string)$f ? 'selected' : '' }}>Floor {{ $f }}</option>
        @endfor
    </select>
</form>

<div class="notice-grid">
    @forelse($posts as $post)
        <div class="notice-card glass-card {{ $post->is_pinned ? 'pinned' : '' }}">
            @if($post->is_pinned)<span class="notice-pin"><i class="fas fa-thumbtack"></i> Pinned</span>@endif
            <h4>{{ $post->title }}</h4>
            <div class="notice-meta">
                <span><i class="fas fa-user"></i> {{ $post->user->name }}</span>
                <span><i class="fas fa-layer-group"></i> {{ $post->floor ? 'Floor '.$post->floor : 'All' }}</span>
                <span>{{ $post->created_at->diffForHumans() }}</span>
            </div>
            <div class="notice-body">{{ $post->body }}</div>
            @if($post->comments_count > 0)
                <div class="notice-comments">
                    @foreach($post->comments->take(3) as $c)
                        <div class="notice-comment"><strong>{{ $c->user->name }}:</strong> {{ Str::limit($c->body, 80) }}</div>
                    @endforeach
                </div>
            @endif
            <div style="display:flex;gap:8px;margin-top:12px;">
                <form method="POST" action="{{ route('admin.noticeBoard.pin', $post) }}">@csrf @method('PATCH')
                    <button class="btn btn-sm btn-outline"><i class="fas fa-thumbtack"></i></button>
                </form>
                <form method="POST" action="{{ route('admin.noticeBoard.destroy', $post) }}" onsubmit="return confirm('Delete post?')">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
    @empty
        <p style="grid-column:1/-1;text-align:center;color:var(--text-light);padding:60px;">No posts yet</p>
    @endforelse
</div>
<div style="margin-top:20px;">{{ $posts->links() }}</div>
@endsection
