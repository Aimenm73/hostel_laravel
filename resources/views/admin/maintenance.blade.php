@extends('layouts.admin')
@section('title', 'Maintenance Calendar')
@section('page-title', 'Maintenance')
@section('content')
@include('partials.page-hero', [
    'icon' => 'fa-wrench',
    'title' => 'Maintenance Schedule',
    'subtitle' => 'Plan outages — students see them on their calendar',
    'actions' => '<button class="btn btn-primary" onclick="openModal(\'addMaintModal\')"><i class="fas fa-plus"></i> Schedule</button>'
])

@php
    $daysInMonth = $start->daysInMonth;
    $firstDow = $start->copy()->startOfMonth()->dayOfWeek;
    $colors = \App\Models\MaintenanceSchedule::typeColors();
@endphp

<div class="cal-nav">
    <a href="?month={{ $start->copy()->subMonth()->format('Y-m') }}" class="btn btn-outline btn-sm"><i class="fas fa-chevron-left"></i></a>
    <h3>{{ $start->format('F Y') }}</h3>
    <a href="?month={{ $start->copy()->addMonth()->format('Y-m') }}" class="btn btn-outline btn-sm"><i class="fas fa-chevron-right"></i></a>
</div>

<div class="glass-card" style="margin-bottom:28px;">
    <div class="cal-grid">
        @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)<div class="cal-dow">{{ $d }}</div>@endforeach
        @for($i = 0; $i < $firstDow; $i++)<div class="cal-day other-month"></div>@endfor
        @for($day = 1; $day <= $daysInMonth; $day++)
            @php
                $date = $start->copy()->day($day);
                $dayEvents = $schedules->filter(fn($e) => $e->starts_at->isSameDay($date));
            @endphp
            <div class="cal-day {{ $date->isToday() ? 'today' : '' }}">
                <div class="cal-day-num">{{ $day }}</div>
                @foreach($dayEvents->take(3) as $ev)
                    <span class="cal-event-dot {{ $ev->type }}" title="{{ $ev->title }}">{{ Str::limit($ev->title, 12) }}</span>
                @endforeach
            </div>
        @endfor
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Upcoming Schedules</h3></div>
    <div class="table-responsive">
        <table>
            <thead><tr><th>Title</th><th>Type</th><th>Floor</th><th>Start</th><th>End</th><th></th></tr></thead>
            <tbody>
            @forelse($allUpcoming as $s)
                <tr>
                    <td>{{ $s->title }}</td>
                    <td><span class="badge badge-general">{{ $s->type }}</span></td>
                    <td>{{ $s->floor ? 'Floor '.$s->floor : 'All' }}</td>
                    <td>{{ $s->starts_at->format('M d H:i') }}</td>
                    <td>{{ $s->ends_at->format('M d H:i') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.maintenance.destroy', $s) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--text-light);">No schedules</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="addMaintModal">
    <div class="modal" style="max-width:520px;">
        <div class="modal-header"><h3>Schedule Maintenance</h3><button class="modal-close" onclick="closeModal('addMaintModal')">&times;</button></div>
        <form method="POST" action="{{ route('admin.maintenance.store') }}">@csrf
            <div class="modal-body">
                <div class="form-group"><label>Title</label><input type="text" name="title" class="form-control" required placeholder="e.g. Water supply off"></div>
                <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                <div class="form-row">
                    <div class="form-group"><label>Type</label><select name="type" class="form-control"><option value="water">Water</option><option value="power">Power</option><option value="wifi">WiFi</option><option value="elevator">Elevator</option><option value="general">General</option></select></div>
                    <div class="form-group"><label>Floor (optional)</label><input type="number" name="floor" class="form-control" min="1" placeholder="All floors"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Starts</label><input type="datetime-local" name="starts_at" class="form-control" required></div>
                    <div class="form-group"><label>Ends</label><input type="datetime-local" name="ends_at" class="form-control" required></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal('addMaintModal')">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
        </form>
    </div>
</div>
@endsection
