@extends('layouts.admin')
@section('title', 'Complaints - Admin')
@section('page-title', 'Complaints')
@section('content')
<div class="filter-tabs">
    <a href="{{ route('admin.complaints.index') }}" class="filter-tab {{ !$status || $status=='all' ? 'active' : '' }}">All</a>
    <a href="{{ route('admin.complaints.index', ['status'=>'pending']) }}" class="filter-tab {{ $status=='pending' ? 'active' : '' }}">Pending</a>
    <a href="{{ route('admin.complaints.index', ['status'=>'in_progress']) }}" class="filter-tab {{ $status=='in_progress' ? 'active' : '' }}">In Progress</a>
    <a href="{{ route('admin.complaints.index', ['status'=>'resolved']) }}" class="filter-tab {{ $status=='resolved' ? 'active' : '' }}">Resolved</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead><tr><th>Student</th><th>Title</th><th>Category</th><th>Priority</th><th>Status</th><th>Date</th><th></th></tr></thead>
            <tbody>
            @forelse($complaints as $c)
                <tr>
                    <td>{{ $c->student->name ?? 'N/A' }}</td>
                    <td>{{ Str::limit($c->title, 40) }}</td>
                    <td>{{ $c->category }}</td>
                    <td><span class="badge badge-{{ $c->priority }}">{{ $c->priority }}</span></td>
                    <td><span class="badge badge-{{ $c->status }}">{{ str_replace('_',' ',$c->status) }}</span></td>
                    <td>{{ $c->created_at ? $c->created_at->format('M d, Y') : '' }}</td>
                    <td><button class="btn btn-sm btn-primary" onclick="viewComplaint({{ $c->id }})">View</button></td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;color:var(--text-light);padding:40px;">No complaints found</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper" style="padding:16px;">{{ $complaints->appends(request()->query())->links() }}</div>
</div>

{{-- Complaint Detail Modal --}}
<div class="modal-overlay" id="complaintModal">
    <div class="modal" style="max-width:680px;">
        <div class="modal-header"><h3 id="modalTitle">Complaint Details</h3><button class="modal-close" onclick="closeModal('complaintModal')">&times;</button></div>
        <div class="modal-body" id="modalBody">Loading...</div>
    </div>
</div>

@endsection
@section('scripts')
<script>
function viewComplaint(id){
    openModal('complaintModal');
    document.getElementById('modalBody').innerHTML='<p style="text-align:center;padding:20px;">Loading...</p>';
    fetch('/admin/complaints/'+id)
        .then(r=>r.json())
        .then(c=>{
            let img=c.image?'<div style="margin:12px 0;"><img src="/storage/complaints/'+c.image+'" style="max-width:100%;border-radius:8px;"></div>':'';
            let msgs='';
            if(c.messages&&c.messages.length){
                msgs='<div class="message-thread">';
                c.messages.forEach(m=>{
                    let mine=m.sender&&m.sender.role==='admin'?'':'mine';
                    msgs+='<div class="message-bubble '+mine+'"><div class="sender">'+(m.sender?m.sender.name:'')+'</div>'+m.message+'<div class="time">'+(m.created_at?new Date(m.created_at).toLocaleString():'')+'</div></div>';
                });
                msgs+='</div>';
            }
            document.getElementById('modalTitle').textContent=c.title;
            document.getElementById('modalBody').innerHTML=`
                <p style="margin-bottom:8px;"><strong>Student:</strong> ${c.student?c.student.name:'N/A'}</p>
                <p style="margin-bottom:8px;"><strong>Category:</strong> ${c.category||'N/A'}</p>
                <p style="margin-bottom:8px;"><strong>Priority:</strong> <span class="badge badge-${c.priority}">${c.priority}</span></p>
                <p style="margin-bottom:8px;"><strong>Status:</strong> <span class="badge badge-${c.status}">${c.status.replace('_',' ')}</span></p>
                <p style="margin-bottom:12px;"><strong>Description:</strong><br>${c.description}</p>
                ${img}
                <h4 style="margin:16px 0 8px;">Messages</h4>
                ${msgs||'<p style="color:var(--text-light);font-size:13px;">No messages yet</p>'}
                <form method="POST" action="/admin/complaints/${c.id}/status" style="margin-top:16px;">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                    <input type="hidden" name="_method" value="PATCH">
                    <div class="form-group">
                        <label>Update Status</label>
                        <select name="status" class="form-control">
                            <option value="pending" ${c.status==='pending'?'selected':''}>Pending</option>
                            <option value="in_progress" ${c.status==='in_progress'?'selected':''}>In Progress</option>
                            <option value="resolved" ${c.status==='resolved'?'selected':''}>Resolved</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
                </form>
                <form method="POST" action="/admin/complaints/${c.id}/reply" style="margin-top:16px;">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                    <div class="form-group">
                        <label>Reply</label>
                        <textarea name="message" class="form-control" placeholder="Type your reply..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane"></i> Send</button>
                </form>`;
        });
}
</script>
@endsection
