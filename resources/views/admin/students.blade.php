@extends('layouts.admin')
@section('title', 'Students - Admin')
@section('page-title', 'Students')
@section('content')
<div class="card">
    <div class="card-header">
        <h3>All Students</h3>
        <button class="btn btn-primary" onclick="openModal('addStudentModal')"><i class="fas fa-plus"></i> Add Student</button>
    </div>
    <div class="table-responsive">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Roll No</th><th>Department</th><th>Year</th><th>Room</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($students as $s)
                <tr>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->email }}</td>
                    <td>{{ $s->studentDetail->roll_no ?? 'N/A' }}</td>
                    <td>{{ $s->studentDetail->department ?? 'N/A' }}</td>
                    <td>{{ $s->studentDetail->year ?? 'N/A' }}</td>
                    <td>{{ $s->studentDetail && $s->studentDetail->room ? $s->studentDetail->room->number : 'Not Assigned' }}</td>
                    <td style="display:flex;gap:6px;flex-wrap:wrap;">
                        <button class="btn btn-sm btn-primary" onclick="editStudent({{ json_encode($s) }})"><i class="fas fa-edit"></i></button>
                        <form method="POST" action="{{ route('admin.students.destroy', $s->id) }}" onsubmit="return confirm('Delete this student?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                        <button class="btn btn-sm btn-success" onclick="assignRoom({{ $s->id }})"><i class="fas fa-bed"></i></button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;color:var(--text-light);padding:40px;">No students found</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper" style="padding:16px;">{{ $students->links() }}</div>
</div>

{{-- Add Student Modal --}}
<div class="modal-overlay" id="addStudentModal">
    <div class="modal">
        <div class="modal-header"><h3>Add Student</h3><button class="modal-close" onclick="closeModal('addStudentModal')">&times;</button></div>
        <form method="POST" action="{{ route('admin.students.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group"><label>Name</label><input type="text" name="name" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control"></div>
                </div>
                <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group"><label>Roll No</label><input type="text" name="roll_no" class="form-control" required></div>
                    <div class="form-group"><label>Department</label><input type="text" name="department" class="form-control" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Year</label><select name="year" class="form-control"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                    <div class="form-group"><label>Room</label><select name="room_id" class="form-control"><option value="">Not Assigned</option>@foreach($rooms as $r)<option value="{{ $r->id }}">{{ $r->number }} ({{ $r->type }})</option>@endforeach</select></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal('addStudentModal')">Cancel</button><button type="submit" class="btn btn-primary">Add Student</button></div>
        </form>
    </div>
</div>

{{-- Edit Student Modal --}}
<div class="modal-overlay" id="editStudentModal">
    <div class="modal">
        <div class="modal-header"><h3>Edit Student</h3><button class="modal-close" onclick="closeModal('editStudentModal')">&times;</button></div>
        <form method="POST" id="editStudentForm">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group"><label>Name</label><input type="text" name="name" id="editName" class="form-control" required></div>
                <div class="form-row">
                    <div class="form-group"><label>Email</label><input type="email" name="email" id="editEmail" class="form-control" required></div>
                    <div class="form-group"><label>Phone</label><input type="text" name="phone" id="editPhone" class="form-control"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Roll No</label><input type="text" name="roll_no" id="editRollNo" class="form-control" required></div>
                    <div class="form-group"><label>Department</label><input type="text" name="department" id="editDept" class="form-control" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Year</label><select name="year" id="editYear" class="form-control"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div>
                    <div class="form-group"><label>Room</label><select name="room_id" id="editRoom" class="form-control"><option value="">Not Assigned</option>@foreach($rooms as $r)<option value="{{ $r->id }}">{{ $r->number }}</option>@endforeach</select></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal('editStudentModal')">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
        </form>
    </div>
</div>

{{-- Assign Room Modal --}}
<div class="modal-overlay" id="assignRoomModal">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header"><h3>Assign Room</h3><button class="modal-close" onclick="closeModal('assignRoomModal')">&times;</button></div>
        <form method="POST" id="assignRoomForm">
            @csrf
            <div class="modal-body">
                <div class="form-group"><label>Select Room</label><select name="room_id" class="form-control" required>@foreach($rooms as $r)<option value="{{ $r->id }}">{{ $r->number }} - {{ $r->type }} (Cap: {{ $r->capacity }})</option>@endforeach</select></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal('assignRoomModal')">Cancel</button><button type="submit" class="btn btn-success">Assign</button></div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<script>
function editStudent(s){
    document.getElementById('editStudentForm').action='/admin/students/'+s.id;
    document.getElementById('editName').value=s.name;
    document.getElementById('editEmail').value=s.email;
    document.getElementById('editPhone').value=s.phone||'';
    if(s.student_detail){
        document.getElementById('editRollNo').value=s.student_detail.roll_no||'';
        document.getElementById('editDept').value=s.student_detail.department||'';
        document.getElementById('editYear').value=s.student_detail.year||1;
        document.getElementById('editRoom').value=s.student_detail.room_id||'';
    }
    openModal('editStudentModal');
}
function assignRoom(id){
    document.getElementById('assignRoomForm').action='/admin/students/'+id+'/assign-room';
    openModal('assignRoomModal');
}
</script>
@endsection
