@extends('layouts.admin')
@section('title', 'Events - Admin')
@section('page-title', 'Events')
@section('content')
<div class="card">
    <div class="card-header"><h3>All Events</h3><button class="btn btn-primary" onclick="openModal('addEventModal')"><i class="fas fa-plus"></i> Add Event</button></div>
    <div class="table-responsive">
        <table>
            <thead><tr><th>Title</th><th>Venue</th><th>Date</th><th>Time</th><th>Seats</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($events as $e)
                <tr>
                    <td>{{ $e->title }}</td>
                    <td>{{ $e->venue }}</td>
                    <td>{{ $e->date ? $e->date->format('M d, Y') : '' }}</td>
                    <td>{{ $e->time }}</td>
                    <td>{{ $e->booked }}/{{ $e->max_seats }}</td>
                    <td><span class="badge badge-{{ $e->status }}">{{ $e->status }}</span></td>
                    <td style="display:flex;gap:6px;">
                        <button class="btn btn-sm btn-primary" onclick="editEvent({{ json_encode($e) }})"><i class="fas fa-edit"></i></button>
                        <form method="POST" action="{{ route('admin.events.destroy', $e->id) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                        <button class="btn btn-sm btn-outline" onclick="viewRegistrations({{ $e->id }})"><i class="fas fa-users"></i></button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;color:var(--text-light);padding:40px;">No events</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper" style="padding:16px;">{{ $events->links() }}</div>
</div>

<div class="modal-overlay" id="addEventModal">
    <div class="modal">
        <div class="modal-header"><h3>Add Event</h3><button class="modal-close" onclick="closeModal('addEventModal')">&times;</button></div>
        <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="form-group"><label>Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="form-group"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
                <div class="form-row">
                    <div class="form-group"><label>Venue</label><input type="text" name="venue" class="form-control"></div>
                    <div class="form-group"><label>Max Seats</label><input type="number" name="max_seats" class="form-control" value="100" min="1"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Date</label><input type="date" name="date" class="form-control" required></div>
                    <div class="form-group"><label>Time</label><input type="time" name="time" class="form-control" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
                    <div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="upcoming">Upcoming</option><option value="ongoing">Ongoing</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal('addEventModal')">Cancel</button><button type="submit" class="btn btn-primary">Create</button></div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="editEventModal">
    <div class="modal">
        <div class="modal-header"><h3>Edit Event</h3><button class="modal-close" onclick="closeModal('editEventModal')">&times;</button></div>
        <form method="POST" id="editEventForm" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group"><label>Title</label><input type="text" name="title" id="eeTitle" class="form-control" required></div>
                <div class="form-group"><label>Description</label><textarea name="description" id="eeDesc" class="form-control"></textarea></div>
                <div class="form-row">
                    <div class="form-group"><label>Venue</label><input type="text" name="venue" id="eeVenue" class="form-control"></div>
                    <div class="form-group"><label>Max Seats</label><input type="number" name="max_seats" id="eeSeats" class="form-control" min="1"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Date</label><input type="date" name="date" id="eeDate" class="form-control" required></div>
                    <div class="form-group"><label>Time</label><input type="time" name="time" id="eeTime" class="form-control" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Image</label><input type="file" name="image" class="form-control" accept="image/*"></div>
                    <div class="form-group"><label>Status</label><select name="status" id="eeStatus" class="form-control"><option value="upcoming">Upcoming</option><option value="ongoing">Ongoing</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option></select></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal('editEventModal')">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="regsModal">
    <div class="modal"><div class="modal-header"><h3>Registrations</h3><button class="modal-close" onclick="closeModal('regsModal')">&times;</button></div><div class="modal-body" id="regsBody">Loading...</div></div>
</div>
@endsection
@section('scripts')
<script>
function editEvent(e){
    document.getElementById('editEventForm').action='/admin/events/'+e.id;
    document.getElementById('eeTitle').value=e.title;
    document.getElementById('eeDesc').value=e.description||'';
    document.getElementById('eeVenue').value=e.venue||'';
    document.getElementById('eeSeats').value=e.max_seats;
    document.getElementById('eeDate').value=e.date?e.date.split('T')[0]:'';
    document.getElementById('eeTime').value=e.time||'';
    document.getElementById('eeStatus').value=e.status;
    openModal('editEventModal');
}
function viewRegistrations(id){
    openModal('regsModal');
    document.getElementById('regsBody').innerHTML='Loading...';
    fetch('/admin/events/'+id+'/registrations').then(r=>r.json()).then(d=>{
        let html='<h4>'+d.event.title+'</h4><table><thead><tr><th>Name</th><th>Email</th><th>Registered At</th></tr></thead><tbody>';
        if(d.registrations.length){d.registrations.forEach(r=>{html+='<tr><td>'+(r.student?r.student.name:'')+'</td><td>'+(r.student?r.student.email:'')+'</td><td>'+(r.registered_at||'')+'</td></tr>';});}
        else{html+='<tr><td colspan="3" style="text-align:center;">No registrations</td></tr>';}
        html+='</tbody></table>';
        document.getElementById('regsBody').innerHTML=html;
    });
}
</script>
@endsection
