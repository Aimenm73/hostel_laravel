@extends('layouts.student')
@section('title', 'Mess Menu')
@section('page-title', 'Mess Menu')
@section('content')
@if($todayMenu)
    <div class="today-menu-banner">
        <div class="today-menu-label"><i class="fas fa-star"></i> Today's Menu — {{ $todayMenu->day_label }}</div>
        <div class="today-menu-meals">
            <div class="today-meal"><span>Breakfast</span><p>{{ $todayMenu->breakfast ?: 'Not set yet' }}</p></div>
            <div class="today-meal"><span>Lunch</span><p>{{ $todayMenu->lunch ?: 'Not set yet' }}</p></div>
            <div class="today-meal"><span>Dinner</span><p>{{ $todayMenu->dinner ?: 'Not set yet' }}</p></div>
        </div>
    </div>
@endif

<div class="feedback-card glass-card">
    <h3 style="margin-bottom:16px;font-size:16px;"><i class="fas fa-star" style="color:#ffd166;"></i> Rate Today's Meal</h3>
    <form method="POST" action="{{ route('student.messFeedback.store') }}" id="mealFeedbackForm">
        @csrf
        <input type="hidden" name="meal_date" value="{{ date('Y-m-d') }}">
        <div class="form-group" style="margin-bottom:16px;">
            <label>Which meal?</label>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <label class="broadcast-type active meal-tab" data-meal="breakfast"><input type="radio" name="meal_type" value="breakfast" checked> Breakfast</label>
                <label class="broadcast-type meal-tab" data-meal="lunch"><input type="radio" name="meal_type" value="lunch"> Lunch</label>
                <label class="broadcast-type meal-tab" data-meal="dinner"><input type="radio" name="meal_type" value="dinner"> Dinner</label>
            </div>
        </div>
        <div class="star-rating" id="starRating">
            @for($i = 1; $i <= 5; $i++)
                <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" {{ $i === 5 ? 'checked' : '' }}>
                <label for="star{{ $i }}" title="{{ $i }} stars" data-value="{{ $i }}"><i class="fas fa-star"></i></label>
            @endfor
        </div>
        <div class="form-group" style="margin-top:16px;">
            <textarea name="comment" class="form-control" rows="2" placeholder="Optional comment (what did you like?)"></textarea>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Rating</button>
    </form>
</div>

<div class="mess-week-grid">
    @foreach($menus as $menu)
        @php $isToday = $menu->day_of_week === $todayKey; @endphp
        <div class="mess-day-card {{ $isToday ? 'mess-today' : '' }}">
            <div class="mess-day-header">
                <h3>{{ $menu->day_label }}</h3>
                @if($isToday)<span class="badge badge-approved">Today</span>@endif
            </div>
            <div class="mess-meal-readonly">
                <label><i class="fas fa-sun"></i> Breakfast</label>
                <p>{{ $menu->breakfast ?: '—' }}</p>
            </div>
            <div class="mess-meal-readonly">
                <label><i class="fas fa-cloud-sun"></i> Lunch</label>
                <p>{{ $menu->lunch ?: '—' }}</p>
            </div>
            <div class="mess-meal-readonly">
                <label><i class="fas fa-moon"></i> Dinner</label>
                <p>{{ $menu->dinner ?: '—' }}</p>
            </div>
        </div>
    @endforeach
</div>
@endsection
@section('scripts')
<script>
document.querySelectorAll('.meal-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.meal-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
    });
});
function paintStars(upTo) {
    document.querySelectorAll('#starRating label').forEach(l => {
        l.classList.toggle('active', parseInt(l.dataset.value) <= upTo);
    });
}
document.querySelectorAll('#starRating label').forEach(label => {
    label.addEventListener('mouseenter', () => paintStars(parseInt(label.dataset.value)));
    label.addEventListener('click', () => paintStars(parseInt(label.dataset.value)));
});
document.getElementById('starRating').addEventListener('mouseleave', () => {
    const checked = document.querySelector('#starRating input:checked');
    paintStars(checked ? parseInt(checked.value) : 0);
});
paintStars(5);
</script>
@endsection
