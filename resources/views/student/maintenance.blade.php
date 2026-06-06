@extends('layouts.student')
@section('title', 'Maintenance')
@section('page-title', 'Maintenance')
@section('content')
@include('partials.page-hero', ['icon' => 'fa-wrench', 'title' => 'Maintenance Calendar', 'subtitle' => 'Planned outages for your floor and building'])

@if($activeNow->isNotEmpty())
    @foreach($activeNow as $active)
    <div class="active-maintenance-banner">
        <i class="fas fa-exclamation-triangle"></i>
        <div>
            <strong style="font-size:16px;">Active now: {{ $active->title }}</strong>
            <p style="margin:4px 0 0;opacity:0.9;font-size:13px;">{{ $active->description }} — until {{ $active->ends_at->format('M d, H:i') }}</p>
        </div>
    </div>
    @endforeach
@endif

@php $daysInMonth = $start->daysInMonth; $firstDow = $start->copy()->startOfMonth()->dayOfWeek; @endphp
<div class="cal-nav">
    <a href="?month={{ $start->copy()->subMonth()->format('Y-m') }}" class="btn btn-outline btn-sm"><i class="fas fa-chevron-left"></i></a>
    <h3>{{ $start->format('F Y') }}</h3>
    <a href="?month={{ $start->copy()->addMonth()->format('Y-m') }}" class="btn btn-outline btn-sm"><i class="fas fa-chevron-right"></i></a>
</div>
<div class="glass-card">
    <div class="cal-grid">
        @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)<div class="cal-dow">{{ $d }}</div>@endforeach
        @for($i = 0; $i < $firstDow; $i++)<div class="cal-day other-month"></div>@endfor
        @for($day = 1; $day <= $daysInMonth; $day++)
            @php $date = $start->copy()->day($day); $dayEvents = $schedules->filter(fn($e) => $e->starts_at->isSameDay($date)); @endphp
            <div class="cal-day {{ $date->isToday() ? 'today' : '' }}">
                <div class="cal-day-num">{{ $day }}</div>
                @foreach($dayEvents as $ev)<span class="cal-event-dot {{ $ev->type }}" title="{{ $ev->title }}">{{ Str::limit($ev->title, 10) }}</span>@endforeach
            </div>
        @endfor
    </div>
</div>

<div class="card" style="margin-top:24px;">
    <div class="card-header"><h3>Schedule Details</h3></div>
    <div class="card-body">
        @forelse($schedules as $s)
            <div style="padding:14px 0;border-bottom:1px solid var(--border);display:flex;gap:16px;align-items:flex-start;">
                <div style="width:4px;height:48px;border-radius:2px;background:{{ \App\Models\MaintenanceSchedule::typeColors()[$s->type] ?? '#4361ee' }};"></div>
                <div>
                    <strong>{{ $s->title }}</strong>
                    <p style="font-size:13px;color:var(--text-light);margin:4px 0;">{{ $s->starts_at->format('M d, H:i') }} — {{ $s->ends_at->format('M d, H:i') }}</p>
                    @if($s->description)<p style="font-size:13px;">{{ $s->description }}</p>@endif
                </div>
            </div>
        @empty
            <p style="color:var(--text-light);text-align:center;padding:24px;">No maintenance scheduled this month</p>
        @endforelse
    </div>
</div>
@endsection
