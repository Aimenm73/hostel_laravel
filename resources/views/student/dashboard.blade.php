@extends('layouts.student')
@section('title', 'Dashboard - Student')
@section('page-title', 'Dashboard')
@section('content')
@if($announcementTicker->isNotEmpty())
<div class="announcement-ticker">
    <span class="ticker-label"><i class="fas fa-bullhorn"></i> Latest</span>
    <div class="ticker-track">
        <div class="ticker-content">
            @foreach($announcementTicker as $ann)
                <span class="ticker-item"><strong>{{ $ann->title }}</strong> — {{ Str::limit($ann->content, 60) }}</span>
            @endforeach
            @foreach($announcementTicker as $ann)
                <span class="ticker-item"><strong>{{ $ann->title }}</strong> — {{ Str::limit($ann->content, 60) }}</span>
            @endforeach
        </div>
    </div>
    <a href="{{ route('student.announcements.index') }}" class="ticker-link">View all</a>
</div>
@endif

<div class="welcome-card">
    <h2>Welcome back, {{ $user->name }}</h2>
    <p>Here's your hostel overview</p>
    <div class="welcome-info">
        <span><i class="fas fa-id-card"></i> {{ $user->studentDetail->roll_no ?? 'N/A' }}</span>
        <span><i class="fas fa-building"></i> {{ $user->studentDetail->department ?? 'N/A' }}</span>
        <span><i class="fas fa-door-open"></i> Room: {{ $roomNumber }}</span>
    </div>
</div>

<div class="quick-actions">
    <a href="{{ route('student.attendance.index') }}" class="quick-action-btn"><i class="fas fa-clipboard-check"></i><span>Attendance</span></a>
    <a href="{{ route('student.complaints.create') }}" class="quick-action-btn"><i class="fas fa-plus-circle"></i><span>Complaint</span></a>
    <a href="{{ route('student.leaveRequests.create') }}" class="quick-action-btn"><i class="fas fa-calendar-minus"></i><span>Leave</span></a>
    <a href="{{ route('student.visitorPasses.create') }}" class="quick-action-btn"><i class="fas fa-id-card"></i><span>Visitor</span></a>
    <a href="{{ route('student.messMenu.index') }}" class="quick-action-btn"><i class="fas fa-utensils"></i><span>Mess Menu</span></a>
    <a href="{{ route('student.chat.index') }}" class="quick-action-btn"><i class="fas fa-headset"></i><span>Live Chat</span></a>
    <a href="{{ route('student.noticeBoard.index') }}" class="quick-action-btn"><i class="fas fa-comments"></i><span>Notice Board</span></a>
    <a href="{{ route('student.maintenance.index') }}" class="quick-action-btn"><i class="fas fa-wrench"></i><span>Maintenance</span></a>
    <a href="{{ route('student.fees.index') }}" class="quick-action-btn"><i class="fas fa-wallet"></i><span>My Fees</span></a>
    <a href="{{ route('student.profile.edit') }}" class="quick-action-btn"><i class="fas fa-qrcode"></i><span>Hostel ID</span></a>
</div>

<div class="stats-grid">
    <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-exclamation-triangle"></i></div><div class="stat-info"><h3>{{ $totalComplaints }}</h3><p>Total Complaints</p></div></div>
    <div class="stat-card"><div class="stat-icon orange"><i class="fas fa-clock"></i></div><div class="stat-info"><h3>{{ $pendingComplaints }}</h3><p>Pending Complaints</p></div></div>
    <div class="stat-card"><div class="stat-icon green"><i class="fas fa-calendar-check"></i></div><div class="stat-info"><h3>{{ $eventsRegistered }}</h3><p>Events Registered</p></div></div>
    <div class="stat-card"><div class="stat-icon red"><i class="fas fa-door-open"></i></div><div class="stat-info"><h3>{{ $roomNumber }}</h3><p>Room Number</p></div></div>
</div>

{{-- ===== CHARTS ROW ===== --}}
<div class="charts-grid charts-grid-2">
    <div class="card chart-card">
        <div class="card-header"><h3><i class="fas fa-chart-pie"></i> My Complaints</h3></div>
        <div class="card-body chart-body">
            <canvas id="myComplaintsChart"></canvas>
        </div>
    </div>
    <div class="card chart-card">
        <div class="card-header"><h3><i class="fas fa-chart-bar"></i> My Leave Requests</h3></div>
        <div class="card-body chart-body">
            <canvas id="myLeavesChart"></canvas>
        </div>
    </div>
</div>

{{-- ===== ACTIVITY LOG + TABLES ===== --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
    {{-- Activity Log Timeline --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-stream"></i> My Activity</h3>
        </div>
        <div class="card-body" style="padding:16px 20px;">
            <div class="activity-timeline">
                @forelse($activityLogs as $log)
                    <div class="timeline-item" style="animation-delay:{{ $loop->index * 0.06 }}s">
                        <div class="timeline-dot" style="background:{{ $log->color ?? 'var(--primary)' }}">
                            <i class="fas {{ $log->icon ?? 'fa-circle' }}"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-action">{{ str_replace('_', ' ', ucwords($log->action, '_')) }}</div>
                            <div class="timeline-desc">{{ $log->description ?? '' }}</div>
                            <div class="timeline-meta">
                                <span><i class="fas fa-clock"></i> {{ $log->created_at ? $log->created_at->diffForHumans() : '' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="timeline-empty">
                        <i class="fas fa-stream"></i>
                        <p>No activity yet. Your actions will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Upcoming Events --}}
    <div class="card">
        <div class="card-header"><h3>Upcoming Events</h3><a href="{{ route('student.events.index') }}" class="btn btn-sm btn-outline">View All</a></div>
        <div class="table-responsive">
            <table>
                <thead><tr><th>Event</th><th>Date</th><th>Venue</th><th>Status</th></tr></thead>
                <tbody>
                @forelse($upcomingEvents as $e)
                    <tr>
                        <td>{{ $e->title }}</td>
                        <td>{{ $e->date ? $e->date->format('M d') : '' }}</td>
                        <td>{{ $e->venue }}</td>
                        <td>
                            @if(in_array($e->id, $registeredEventIds))
                                <span class="badge badge-approved">Registered</span>
                            @else
                                <span class="badge badge-pending">Not Registered</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--text-light);">No upcoming events</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr;gap:20px;">
    <div class="card">
        <div class="card-header"><h3>Recent Complaints</h3><a href="{{ route('student.complaints.index') }}" class="btn btn-sm btn-outline">View All</a></div>
        <div class="table-responsive">
            <table>
                <thead><tr><th>Title</th><th>Priority</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                @forelse($recentComplaints as $c)
                    <tr>
                        <td>{{ Str::limit($c->title, 25) }}</td>
                        <td><span class="badge badge-{{ $c->priority }}">{{ $c->priority }}</span></td>
                        <td><span class="badge badge-{{ $c->status }}">{{ str_replace('_',' ',$c->status) }}</span></td>
                        <td>{{ $c->created_at ? $c->created_at->format('M d') : '' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--text-light);">No complaints yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.body.getAttribute('data-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    const textColor = isDark ? '#aab3c5' : '#666';

    Chart.defaults.color = textColor;
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";

    // ── Chart 1: My Complaints by Status (Doughnut) ──
    const complaintsData = @json($myComplaintsByStatus);
    const hasComplaints = Object.values(complaintsData).some(v => v > 0);
    const statusLabels = hasComplaints
        ? Object.keys(complaintsData).map(s => s.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()))
        : ['No Data'];
    const statusValues = hasComplaints ? Object.values(complaintsData) : [1];
    const statusColors = hasComplaints
        ? Object.keys(complaintsData).map(s => {
            const map = { pending: '#ffd166', in_progress: '#4361ee', resolved: '#06d6a0' };
            return map[s] || '#aab3c5';
        })
        : ['#e9ecef'];

    new Chart(document.getElementById('myComplaintsChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: statusColors,
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyle: 'circle' } }
            }
        }
    });

    // ── Chart 2: My Leave Requests by Status (Bar) ──
    const leavesData = @json($myLeavesByStatus);
    const leaveLabels = Object.keys(leavesData).length > 0
        ? Object.keys(leavesData).map(s => s.replace(/\b\w/g, l => l.toUpperCase()))
        : ['Pending', 'Approved', 'Rejected'];
    const leaveValues = Object.keys(leavesData).length > 0 ? Object.values(leavesData) : [0, 0, 0];
    const leaveColors = Object.keys(leavesData).length > 0
        ? Object.keys(leavesData).map(s => {
            const map = { pending: '#ffd166', approved: '#06d6a0', rejected: '#ef476f' };
            return map[s] || '#aab3c5';
        })
        : ['#ffd166', '#06d6a0', '#ef476f'];

    new Chart(document.getElementById('myLeavesChart'), {
        type: 'bar',
        data: {
            labels: leaveLabels,
            datasets: [{
                label: 'Count',
                data: leaveValues,
                backgroundColor: leaveColors,
                borderWidth: 0,
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: gridColor } },
                x: { grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endsection
