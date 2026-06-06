@extends('layouts.admin')
@section('title', 'Dashboard - Admin')
@section('page-title', 'Dashboard')
@section('content')

{{-- Live Status Indicator --}}
<div class="live-indicator">
    <span class="live-dot"></span> Live — Last updated: <span id="liveTimestamp">just now</span>
</div>

{{-- Stats Cards with Animated Counters + Sparklines --}}
<div class="stats-grid stats-grid-5">
    <div class="stat-card stat-card-enhanced" data-stat="totalStudents">
        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3 class="counter" data-target="{{ $totalStudents }}">0</h3>
            <p>Total Students</p>
        </div>
        <canvas class="sparkline" id="sparkStudents" width="60" height="30"></canvas>
    </div>
    <div class="stat-card stat-card-enhanced" data-stat="totalRooms">
        <div class="stat-icon green"><i class="fas fa-door-open"></i></div>
        <div class="stat-info">
            <h3 class="counter" data-target="{{ $totalRooms }}">0</h3>
            <p>Total Rooms</p>
        </div>
    </div>
    <div class="stat-card stat-card-enhanced" data-stat="pendingComplaints">
        <div class="stat-icon orange"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-info">
            <h3 class="counter" data-target="{{ $pendingComplaints }}">0</h3>
            <p>Pending Complaints</p>
        </div>
        <canvas class="sparkline" id="sparkComplaints" width="60" height="30"></canvas>
    </div>
    <div class="stat-card stat-card-enhanced" data-stat="upcomingEvents">
        <div class="stat-icon red"><i class="fas fa-calendar-alt"></i></div>
        <div class="stat-info">
            <h3 class="counter" data-target="{{ $upcomingEvents }}">0</h3>
            <p>Upcoming Events</p>
        </div>
    </div>
    <div class="stat-card stat-card-enhanced revenue-stat" data-stat="totalRevenue">
        <div class="stat-icon" style="background:linear-gradient(135deg,#06d6a0,#059669)"><i class="fas fa-coins"></i></div>
        <div class="stat-info">
            <h3>Rs. <span class="counter" data-target="{{ $totalRevenue }}">0</span></h3>
            <p>Total Revenue</p>
        </div>
        <canvas class="sparkline" id="sparkRevenue" width="60" height="30"></canvas>
    </div>
</div>

{{-- Revenue Row --}}
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:28px;">
    <div class="fee-summary-card paid">
        <h4><i class="fas fa-check-circle"></i> Collected This Month</h4>
        <div class="amount">Rs. <span class="counter" data-target="{{ $thisMonthRevenue }}">0</span></div>
    </div>
    <div class="fee-summary-card pending">
        <h4><i class="fas fa-clock"></i> Pending Fees</h4>
        <div class="amount">Rs. <span class="counter" data-target="{{ $pendingFees }}">0</span></div>
    </div>
    <div class="fee-summary-card overdue">
        <h4><i class="fas fa-chart-line"></i> Attendance Avg (7d)</h4>
        <div class="amount"><span class="counter" data-target="{{ $avgAttendance }}">0</span>%</div>
    </div>
</div>

{{-- ===== BROADCAST + ACTIVITY LOG ROW ===== --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:28px;">
    {{-- Broadcast Message Panel --}}
    <div class="card broadcast-card">
        <div class="card-header">
            <h3><i class="fas fa-bullhorn"></i> Broadcast Message</h3>
            <span class="broadcast-badge">{{ $totalStudents }} students</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.dashboard.broadcast') }}" id="broadcastForm">
                @csrf
                <div class="broadcast-type-selector">
                    <label class="broadcast-type active" data-type="general">
                        <input type="radio" name="type" value="general" checked>
                        <i class="fas fa-info-circle"></i> General
                    </label>
                    <label class="broadcast-type" data-type="urgent">
                        <input type="radio" name="type" value="urgent">
                        <i class="fas fa-exclamation-circle"></i> Urgent
                    </label>
                    <label class="broadcast-type" data-type="maintenance">
                        <input type="radio" name="type" value="maintenance">
                        <i class="fas fa-tools"></i> Maintenance
                    </label>
                </div>
                <div class="form-group" style="margin-bottom:12px;">
                    <textarea name="message" class="form-control" rows="3" placeholder="Type your broadcast message to all students..." required maxlength="500" id="broadcastMsg"></textarea>
                    <div class="broadcast-char-count"><span id="charCount">0</span>/500</div>
                </div>
                <p style="font-size:11px;color:var(--text-light);margin-bottom:12px;"><i class="fas fa-info-circle"></i> Urgent broadcasts also send email & SMS (if phone on file). Maintenance sends email.</p>
                <div class="broadcast-actions">
                    <button type="submit" class="btn btn-primary" id="broadcastBtn">
                        <i class="fas fa-paper-plane"></i> Send Broadcast
                    </button>
                </div>
            </form>

            {{-- Recent Broadcasts --}}
            @if($recentBroadcasts->count() > 0)
                <div class="broadcast-history">
                    <h4><i class="fas fa-history"></i> Recent Broadcasts</h4>
                    @foreach($recentBroadcasts as $b)
                        <div class="broadcast-item">
                            <div class="broadcast-item-icon {{ $b->type }}">
                                <i class="fas fa-{{ $b->type === 'urgent' ? 'exclamation-circle' : ($b->type === 'maintenance' ? 'tools' : 'info-circle') }}"></i>
                            </div>
                            <div class="broadcast-item-content">
                                <p>{{ Str::limit($b->message, 80) }}</p>
                                <small>{{ $b->sent_at ? $b->sent_at->diffForHumans() : '' }} · {{ $b->recipients_count }} recipients</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Activity Log Timeline --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-stream"></i> Activity Log</h3>
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
                                <span><i class="fas fa-user"></i> {{ $log->user->name ?? 'System' }}</span>
                                <span><i class="fas fa-clock"></i> {{ $log->created_at ? $log->created_at->diffForHumans() : '' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="timeline-empty">
                        <i class="fas fa-stream"></i>
                        <p>No activity yet. Actions will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ===== CHARTS ROW ===== --}}
<div class="charts-grid">
    <div class="card chart-card">
        <div class="card-header"><h3><i class="fas fa-chart-pie"></i> Complaints by Status</h3></div>
        <div class="card-body chart-body">
            <canvas id="complaintsChart"></canvas>
        </div>
    </div>
    <div class="card chart-card">
        <div class="card-header"><h3><i class="fas fa-chart-bar"></i> Monthly Event Registrations</h3></div>
        <div class="card-body chart-body">
            <canvas id="registrationsChart"></canvas>
        </div>
    </div>
    <div class="card chart-card">
        <div class="card-header"><h3><i class="fas fa-bed"></i> Room Occupancy</h3></div>
        <div class="card-body chart-body">
            <canvas id="roomOccupancyChart"></canvas>
        </div>
    </div>
    <div class="card chart-card">
        <div class="card-header"><h3><i class="fas fa-calendar-minus"></i> Leave Requests</h3></div>
        <div class="card-body chart-body">
            <canvas id="leavesChart"></canvas>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <div class="card">
        <div class="card-header"><h3>Recent Complaints</h3><a href="{{ route('admin.complaints.index') }}" class="btn btn-sm btn-outline">View All</a></div>
        <div class="table-responsive">
            <table>
                <thead><tr><th>Student</th><th>Title</th><th>Priority</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @forelse($recentComplaints as $c)
                    <tr>
                        <td>{{ $c->student->name ?? 'N/A' }}</td>
                        <td>{{ Str::limit($c->title, 30) }}</td>
                        <td><span class="badge badge-{{ $c->priority }}">{{ $c->priority }}</span></td>
                        <td><span class="badge badge-{{ $c->status }}">{{ $c->status }}</span></td>
                        <td><a href="{{ route('admin.complaints.index') }}" class="btn btn-sm btn-primary">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;color:var(--text-light);">No pending complaints</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Recent Students</h3><a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline">View All</a></div>
        <div class="table-responsive">
            <table>
                <thead><tr><th>Name</th><th>Email</th><th>Roll No</th></tr></thead>
                <tbody>
                @forelse($recentStudents as $s)
                    <tr>
                        <td>{{ $s->name }}</td>
                        <td>{{ $s->email }}</td>
                        <td>{{ $s->studentDetail->roll_no ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center;color:var(--text-light);">No students yet</td></tr>
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
// ── Animated Counters ──
function animateCounters() {
    document.querySelectorAll('.counter').forEach(counter => {
        const target = parseFloat(counter.dataset.target) || 0;
        const duration = 1500;
        const startTime = performance.now();
        const isInt = Number.isInteger(target);

        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            const current = target * eased;
            counter.textContent = isInt ? Math.floor(current).toLocaleString() : current.toFixed(1);
            if (progress < 1) requestAnimationFrame(update);
            else counter.textContent = isInt ? Math.floor(target).toLocaleString() : target.toFixed(1);
        }
        requestAnimationFrame(update);
    });
}

// ── Sparkline Drawing ──
function drawSparkline(canvasId, data, color = '#4361ee') {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !data || data.length === 0) return;
    const ctx = canvas.getContext('2d');
    const w = canvas.width, h = canvas.height;
    ctx.clearRect(0, 0, w, h);

    const max = Math.max(...data, 1);
    const min = Math.min(...data, 0);
    const range = max - min || 1;
    const step = w / (data.length - 1 || 1);

    // Gradient fill
    const gradient = ctx.createLinearGradient(0, 0, 0, h);
    gradient.addColorStop(0, color + '40');
    gradient.addColorStop(1, color + '05');

    ctx.beginPath();
    ctx.moveTo(0, h);
    data.forEach((val, i) => {
        const x = i * step;
        const y = h - ((val - min) / range) * (h * 0.8) - 2;
        if (i === 0) ctx.moveTo(x, y);
        else ctx.lineTo(x, y);
    });
    ctx.lineTo(w, h);
    ctx.closePath();
    ctx.fillStyle = gradient;
    ctx.fill();

    // Line
    ctx.beginPath();
    data.forEach((val, i) => {
        const x = i * step;
        const y = h - ((val - min) / range) * (h * 0.8) - 2;
        if (i === 0) ctx.moveTo(x, y);
        else ctx.lineTo(x, y);
    });
    ctx.strokeStyle = color;
    ctx.lineWidth = 2;
    ctx.stroke();
}

// ── Live Stats Polling ──
function refreshStats() {
    fetch('{{ route("admin.api.stats") }}', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
    })
    .then(r => r.json())
    .then(data => {
        // Update stat values
        document.querySelector('[data-stat="totalStudents"] .counter').dataset.target = data.totalStudents;
        document.querySelector('[data-stat="totalStudents"] .counter').textContent = data.totalStudents;
        document.querySelector('[data-stat="pendingComplaints"] .counter').dataset.target = data.pendingComplaints;
        document.querySelector('[data-stat="pendingComplaints"] .counter').textContent = data.pendingComplaints;
        document.querySelector('[data-stat="upcomingEvents"] .counter').dataset.target = data.upcomingEvents;
        document.querySelector('[data-stat="upcomingEvents"] .counter').textContent = data.upcomingEvents;

        // Update sparklines
        if (data.trends) {
            drawSparkline('sparkStudents', data.trends.students, '#4361ee');
            drawSparkline('sparkComplaints', data.trends.complaints, '#ffd166');
            drawSparkline('sparkRevenue', data.trends.revenue, '#06d6a0');
        }

        // Update timestamp
        document.getElementById('liveTimestamp').textContent = data.timestamp || 'just now';

        // Pulse animation
        document.querySelector('.live-dot').classList.add('pulse');
        setTimeout(() => document.querySelector('.live-dot').classList.remove('pulse'), 1000);
    })
    .catch(() => {});
}

// ── Broadcast Form ──
document.querySelectorAll('.broadcast-type').forEach(label => {
    label.addEventListener('click', function() {
        document.querySelectorAll('.broadcast-type').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
    });
});
const msgInput = document.getElementById('broadcastMsg');
const charCount = document.getElementById('charCount');
if (msgInput) {
    msgInput.addEventListener('input', () => { charCount.textContent = msgInput.value.length; });
}

document.addEventListener('DOMContentLoaded', function() {
    // Animate counters on load
    animateCounters();

    // Draw initial sparklines
    const initialTrends = @json($trends ?? ['students' => [], 'complaints' => [], 'revenue' => []]);
    drawSparkline('sparkStudents', initialTrends.students, '#4361ee');
    drawSparkline('sparkComplaints', initialTrends.complaints, '#ffd166');
    drawSparkline('sparkRevenue', initialTrends.revenue, '#06d6a0');

    // Start live polling every 30 seconds
    setInterval(refreshStats, 30000);

    const isDark = document.body.getAttribute('data-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
    const textColor = isDark ? '#aab3c5' : '#666';

    Chart.defaults.color = textColor;
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";

    // ── Chart 1: Complaints by Status (Doughnut) ──
    const complaintsData = @json($complaintsByStatus);
    const statusLabels = Object.keys(complaintsData).map(s => s.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()));
    const statusColors = Object.keys(complaintsData).map(s => {
        const map = { pending: '#ffd166', in_progress: '#4361ee', resolved: '#06d6a0' };
        return map[s] || '#aab3c5';
    });

    new Chart(document.getElementById('complaintsChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: Object.values(complaintsData),
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

    // ── Chart 2: Monthly Registrations (Bar) ──
    const regData = @json($monthlyRegistrations);

    new Chart(document.getElementById('registrationsChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(regData),
            datasets: [{
                label: 'Registrations',
                data: Object.values(regData),
                backgroundColor: 'rgba(67, 97, 238, 0.75)',
                borderColor: '#4361ee',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.6
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

    // ── Chart 3: Room Occupancy (Pie) ──
    const occData = @json($roomOccupancy);

    new Chart(document.getElementById('roomOccupancyChart'), {
        type: 'pie',
        data: {
            labels: ['Occupied', 'Available'],
            datasets: [{
                data: [occData.occupied, occData.available],
                backgroundColor: ['#ef476f', '#06d6a0'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyle: 'circle' } }
            }
        }
    });

    // ── Chart 4: Leave Requests by Status (Bar - horizontal) ──
    const leavesData = @json($leavesByStatus);
    const leaveLabels = Object.keys(leavesData).map(s => s.replace(/\b\w/g, l => l.toUpperCase()));
    const leaveColors = Object.keys(leavesData).map(s => {
        const map = { pending: '#ffd166', approved: '#06d6a0', rejected: '#ef476f' };
        return map[s] || '#aab3c5';
    });

    new Chart(document.getElementById('leavesChart'), {
        type: 'bar',
        data: {
            labels: leaveLabels,
            datasets: [{
                label: 'Count',
                data: Object.values(leavesData),
                backgroundColor: leaveColors,
                borderWidth: 0,
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.5
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: gridColor } },
                y: { grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endsection
