@extends('layouts.admin')
@section('title', 'Room Heat Map - Admin')
@section('page-title', 'Room Heat Map')
@section('content')
<div class="page-hero">
    <div class="page-hero-glow"></div>
    <div class="page-hero-content">
        <div class="page-hero-icon"><i class="fas fa-map"></i></div>
        <div>
            <h2>Interactive Room Heat Map</h2>
            <p>Visual occupancy overview — click any room for details</p>
        </div>
    </div>
</div>

<div class="heatmap-legend-bar">
    <span><span class="hm-dot empty"></span> Empty (0%)</span>
    <span><span class="hm-dot low"></span> Low (1-50%)</span>
    <span><span class="hm-dot medium"></span> Medium (51-80%)</span>
    <span><span class="hm-dot high"></span> High (81-99%)</span>
    <span><span class="hm-dot full"></span> Full (100%)</span>
    <span><span class="hm-dot maintenance-dot"></span> Maintenance</span>
</div>

<div id="heatmapContainer" class="heatmap-container">
    <div class="chat-loading" style="text-align:center;padding:60px;"><i class="fas fa-spinner fa-spin"></i> Loading heat map data...</div>
</div>

{{-- Room Detail Modal --}}
<div class="modal-overlay" id="roomModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalRoomTitle">Room Details</h3>
            <button class="modal-close" onclick="closeModal('roomModal')">&times;</button>
        </div>
        <div class="modal-body" id="modalRoomBody">
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('/admin/api/room-heatmap', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
    })
    .then(r => r.json())
    .then(rooms => {
        // Group by floor
        const floors = {};
        rooms.forEach(r => {
            const f = r.floor || 1;
            if (!floors[f]) floors[f] = [];
            floors[f].push(r);
        });

        const container = document.getElementById('heatmapContainer');
        container.innerHTML = '';

        Object.keys(floors).sort((a,b) => a-b).forEach(floor => {
            const section = document.createElement('div');
            section.className = 'heatmap-floor';
            section.innerHTML = `<div class="heatmap-floor-label"><i class="fas fa-layer-group"></i> Floor ${floor}</div>`;

            const grid = document.createElement('div');
            grid.className = 'heatmap-grid';

            floors[floor].forEach(room => {
                const cell = document.createElement('div');
                cell.className = `heatmap-cell ${getHeatClass(room)}`;
                cell.onclick = () => showRoomDetail(room);

                const pct = room.percentage;
                cell.innerHTML = `
                    <div class="hm-room-num">${room.number}</div>
                    <div class="hm-bar-wrap"><div class="hm-bar" style="width:${pct}%"></div></div>
                    <div class="hm-occ">${room.occupants}/${room.capacity}</div>
                    <div class="hm-type">${room.type}</div>
                `;
                grid.appendChild(cell);
            });

            section.appendChild(grid);
            container.appendChild(section);
        });
    });
});

function getHeatClass(room) {
    if (room.status === 'maintenance') return 'heat-maintenance';
    if (room.percentage === 0) return 'heat-empty';
    if (room.percentage <= 50) return 'heat-low';
    if (room.percentage <= 80) return 'heat-medium';
    if (room.percentage < 100) return 'heat-high';
    return 'heat-full';
}

function showRoomDetail(room) {
    document.getElementById('modalRoomTitle').innerHTML = `<i class="fas fa-door-open"></i> Room ${room.number}`;
    let html = `
        <div class="room-detail-stats">
            <div class="rd-stat"><span class="rd-val">${room.occupants}</span><span class="rd-label">Occupants</span></div>
            <div class="rd-stat"><span class="rd-val">${room.capacity}</span><span class="rd-label">Capacity</span></div>
            <div class="rd-stat"><span class="rd-val">${room.percentage}%</span><span class="rd-label">Filled</span></div>
        </div>
        <div class="rd-progress"><div class="rd-fill" style="width:${room.percentage}%;background:${room.percentage >= 100 ? '#ef476f' : room.percentage > 80 ? '#ffd166' : '#06d6a0'}"></div></div>
        <div class="rd-info">
            <p><strong>Floor:</strong> ${room.floor}</p>
            <p><strong>Type:</strong> ${room.type}</p>
            <p><strong>Status:</strong> ${room.status}</p>
        </div>
    `;
    if (room.residents && room.residents.length > 0) {
        html += '<h4 style="margin:16px 0 8px;font-size:14px;">Current Residents</h4><div class="rd-residents">';
        room.residents.forEach(r => {
            html += `<div class="rd-resident"><i class="fas fa-user-graduate"></i> <strong>${r.name}</strong> <span>${r.roll_no}</span></div>`;
        });
        html += '</div>';
    } else {
        html += '<p style="text-align:center;color:var(--text-light);margin-top:16px;"><i class="fas fa-couch"></i> No residents assigned</p>';
    }
    document.getElementById('modalRoomBody').innerHTML = html;
    openModal('roomModal');
}
</script>
@endsection
