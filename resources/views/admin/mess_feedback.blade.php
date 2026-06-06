@extends('layouts.admin')
@section('title', 'Mess Ratings')
@section('page-title', 'Mess Ratings')
@section('content')
@include('partials.page-hero', ['icon' => 'fa-star', 'title' => 'Mess Feedback Analytics', 'subtitle' => 'Student meal ratings and comments'])

<div class="feature-grid">
    <div class="feature-tile glass-card">
        <div class="tile-icon" style="background:linear-gradient(135deg,#ffd166,#f59e0b);"><i class="fas fa-star"></i></div>
        <div class="tile-stat">{{ number_format($avgRating, 1) }}</div>
        <h4>Average Rating</h4>
        <p>{{ $totalReviews }} reviews</p>
    </div>
    @foreach($byMeal as $m)
    <div class="feature-tile glass-card">
        <div class="tile-icon" style="background:linear-gradient(135deg,var(--primary),#764ba2);"><i class="fas fa-utensils"></i></div>
        <div class="tile-stat">{{ number_format($m->avg_rating, 1) }}</div>
        <h4>{{ ucfirst($m->meal_type) }}</h4>
        <p>{{ $m->total }} ratings</p>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;">
    <div class="glass-card">
        <h3 style="margin-bottom:16px;font-size:15px;">Rating Distribution</h3>
        @for($i = 5; $i >= 1; $i--)
            @php $count = $distribution[$i] ?? 0; $pct = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0; @endphp
            <div class="rating-bar-row">
                <span>{{ $i }} ★</span>
                <div class="rating-bar-track"><div class="rating-bar-fill" style="width:{{ $pct }}%"></div></div>
                <span>{{ $count }}</span>
            </div>
        @endfor
    </div>
    <div class="card">
        <div class="card-header"><h3>Recent Reviews</h3></div>
        <div class="table-responsive">
            <table>
                <thead><tr><th>Student</th><th>Meal</th><th>Rating</th><th>Comment</th><th>Date</th></tr></thead>
                <tbody>
                @forelse($recent as $r)
                    <tr>
                        <td>{{ $r->student->name ?? '' }}</td>
                        <td>{{ ucfirst($r->meal_type) }}</td>
                        <td>@for($s=1;$s<=5;$s++)<i class="fas fa-star" style="color:{{ $s <= $r->rating ? '#ffd166' : 'var(--border)' }};font-size:12px;"></i>@endfor</td>
                        <td>{{ Str::limit($r->comment, 40) }}</td>
                        <td>{{ $r->meal_date->format('M d') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--text-light);">No ratings yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:16px;">{{ $recent->links() }}</div>
    </div>
</div>
@endsection
