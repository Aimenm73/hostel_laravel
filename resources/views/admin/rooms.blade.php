@extends('layouts.admin')
@section('title', 'Rooms - Admin')
@section('page-title', 'Rooms')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <h3 style="font-size:16px;">All Rooms</h3>
    <button class="btn btn-primary" onclick="openModal('addRoomModal')"><i class="fas fa-plus"></i> Add Room</button>
</div>

@if(isset($roomsByFloor) && $roomsByFloor->isNotEmpty())
<div class="card floor-plan-card" style="margin-bottom:28px;">
    <div class="card-header"><h3><i class="fas fa-layer-group"></i> Interactive Floor Plan</h3></div>
    <div class="card-body">
        @foreach($roomsByFloor as $floor => $floorRooms)
            <div class="floor-section">
                <h4 class="floor-label">Floor {{ $floor }}</h4>
                <div class="floor-plan-grid">
                    @foreach($floorRooms as $room)
                        @php
                            $occupied = $room->studentDetails->count();
                            $pct = $room->capacity > 0 ? round(($occupied / $room->capacity) * 100) : 0;
                            $cellClass = $room->status === 'maintenance' ? 'maintenance' : ($pct >= 100 ? 'full' : ($pct > 0 ? 'partial' : 'empty'));
                        @endphp
                        <div class="floor-room-cell {{ $cellClass }}" title="Room {{ $room->number }} — {{ $occupied }}/{{ $room->capacity }} beds">
                            <span class="floor-room-num">{{ $room->number }}</span>
                            <span class="floor-room-occ">{{ $occupied }}/{{ $room->capacity }}</span>
                            <span class="floor-room-type">{{ ucfirst($room->type) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        <div class="floor-legend">
            <span><i class="legend-dot empty"></i> Available</span>
            <span><i class="legend-dot partial"></i> Partial</span>
            <span><i class="legend-dot full"></i> Full</span>
            <span><i class="legend-dot maintenance"></i> Maintenance</span>
        </div>
    </div>
</div>
@endif

<div class="rooms-grid">
    @forelse($rooms as $room)
        @php $occupied = $room->studentDetails->count(); $pct = $room->capacity > 0 ? round(($occupied / $room->capacity) * 100) : 0; @endphp
        <div class="room-card">
            <h3>Room {{ $room->number }} <span class="badge badge-{{ $room->status == 'available' ? 'approved' : ($room->status == 'maintenance' ? 'pending' : 'in_progress') }}">{{ $room->status }}</span></h3>
            <div class="room-info">
                <p>Floor <span>{{ $room->floor }}</span></p>
                <p>Type <span style="text-transform:capitalize;">{{ $room->type }}</span></p>
                <p>Capacity <span>{{ $occupied }}/{{ $room->capacity }}</span></p>
            </div>
            <div class="progress-bar"><div class="progress" style="width:{{ $pct }}%"></div></div>
            <div class="room-actions">
                <button class="btn btn-sm btn-primary" onclick="editRoom({{ json_encode($room) }})"><i class="fas fa-edit"></i> Edit</button>
                <form method="POST" action="{{ route('admin.rooms.destroy', $room->id) }}" onsubmit="return confirm('Delete this room?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button></form>
            </div>
        </div>
    @empty
        <p style="color:var(--text-light);grid-column:1/-1;text-align:center;padding:40px;">No rooms found</p>
    @endforelse
</div>

{{-- Add Room Modal --}}
<div class="modal-overlay" id="addRoomModal">
    <div class="modal" style="max-width:460px;">
        <div class="modal-header"><h3>Add Room</h3><button class="modal-close" onclick="closeModal('addRoomModal')">&times;</button></div>
        <form method="POST" action="{{ route('admin.rooms.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group"><label>Room Number</label><input type="text" name="number" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group"><label>Floor</label><input type="number" name="floor" class="form-control" value="1" min="1" required></div>
                    <div class="form-group"><label>Type</label><select name="type" class="form-control"><option value="single">Single</option><option value="double" selected>Double</option><option value="triple">Triple</option><option value="suite">Suite</option></select></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Capacity</label><input type="number" name="capacity" class="form-control" value="2" min="1" required></div>
                    <div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="available">Available</option><option value="occupied">Occupied</option><option value="maintenance">Maintenance</option></select></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal('addRoomModal')">Cancel</button><button type="submit" class="btn btn-primary">Add Room</button></div>
        </form>
    </div>
</div>

{{-- Edit Room Modal --}}
<div class="modal-overlay" id="editRoomModal">
    <div class="modal" style="max-width:460px;">
        <div class="modal-header"><h3>Edit Room</h3><button class="modal-close" onclick="closeModal('editRoomModal')">&times;</button></div>
        <form method="POST" id="editRoomForm">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group"><label>Room Number</label><input type="text" name="number" id="erNumber" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group"><label>Floor</label><input type="number" name="floor" id="erFloor" class="form-control" min="1" required></div>
                    <div class="form-group"><label>Type</label><select name="type" id="erType" class="form-control"><option value="single">Single</option><option value="double">Double</option><option value="triple">Triple</option><option value="suite">Suite</option></select></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Capacity</label><input type="number" name="capacity" id="erCapacity" class="form-control" min="1" required></div>
                    <div class="form-group"><label>Status</label><select name="status" id="erStatus" class="form-control"><option value="available">Available</option><option value="occupied">Occupied</option><option value="maintenance">Maintenance</option></select></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal('editRoomModal')">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<script>
function editRoom(r){
    document.getElementById('editRoomForm').action='/admin/rooms/'+r.id;
    document.getElementById('erNumber').value=r.number;
    document.getElementById('erFloor').value=r.floor;
    document.getElementById('erType').value=r.type;
    document.getElementById('erCapacity').value=r.capacity;
    document.getElementById('erStatus').value=r.status;
    openModal('editRoomModal');
}
</script>
@endsection
