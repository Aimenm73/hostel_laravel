@extends('layouts.admin')
@section('title', 'Announcements - Admin')
@section('page-title', 'Announcements')
@section('content')
<div class="card">
    <div class="card-header"><h3>All Announcements</h3><button class="btn btn-primary" onclick="openModal('addAnnModal')"><i class="fas fa-plus"></i> Add</button></div>
    <div class="table-responsive">
        <table>
            <thead><tr><th>Title</th><th>Type</th><th>Content</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($announcements as $a)
                <tr>
                    <td>{{ $a->title }}</td>
                    <td><span class="badge badge-{{ $a->type }}">{{ $a->type }}</span></td>
                    <td>{{ Str::limit($a->content, 80) }}</td>
                    <td>{{ $a->created_at ? $a->created_at->format('M d, Y') : '' }}</td>
                    <td style="display:flex;gap:6px;">
                        <button class="btn btn-sm btn-primary" onclick="editAnn({{ json_encode($a) }})"><i class="fas fa-edit"></i></button>
                        <form method="POST" action="{{ route('admin.announcements.destroy', $a->id) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;color:var(--text-light);padding:40px;">No announcements</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper" style="padding:16px;">{{ $announcements->links() }}</div>
</div>

<div class="modal-overlay" id="addAnnModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header"><h3>New Announcement</h3><button class="modal-close" onclick="closeModal('addAnnModal')">&times;</button></div>
        <form method="POST" action="{{ route('admin.announcements.store') }}">@csrf
            <div class="modal-body">
                <div class="form-group"><label>Title</label><input type="text" name="title" class="form-control" required></div>
                <div class="form-group"><label>Content</label><textarea name="content" class="form-control" required></textarea></div>
                <div class="form-group"><label>Type</label><select name="type" class="form-control"><option value="general">General</option><option value="urgent">Urgent</option><option value="maintenance">Maintenance</option></select></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal('addAnnModal')">Cancel</button><button type="submit" class="btn btn-primary">Create</button></div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="editAnnModal">
    <div class="modal" style="max-width:500px;">
        <div class="modal-header"><h3>Edit Announcement</h3><button class="modal-close" onclick="closeModal('editAnnModal')">&times;</button></div>
        <form method="POST" id="editAnnForm">@csrf @method('PUT')
            <div class="modal-body">
                <div class="form-group"><label>Title</label><input type="text" name="title" id="eaTitle" class="form-control" required></div>
                <div class="form-group"><label>Content</label><textarea name="content" id="eaContent" class="form-control" required></textarea></div>
                <div class="form-group"><label>Type</label><select name="type" id="eaType" class="form-control"><option value="general">General</option><option value="urgent">Urgent</option><option value="maintenance">Maintenance</option></select></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal('editAnnModal')">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
<script>
function editAnn(a){
    document.getElementById('editAnnForm').action='/admin/announcements/'+a.id;
    document.getElementById('eaTitle').value=a.title;
    document.getElementById('eaContent').value=a.content;
    document.getElementById('eaType').value=a.type;
    openModal('editAnnModal');
}
</script>
@endsection
