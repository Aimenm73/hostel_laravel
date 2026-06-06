@extends('layouts.student')
@section('title', 'My Complaints')
@section('page-title', 'My Complaints')
@section('content')
<div class="card">
    <div class="card-header"><h3>My Complaints</h3><a href="{{ route('student.complaints.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Complaint</a></div>
    <div class="table-responsive">
        <table>
            <thead><tr><th>Title</th><th>Category</th><th>Priority</th><th>Status</th><th>Date</th><th></th></tr></thead>
            <tbody>
            @forelse($complaints as $c)
                <tr>
                    <td>{{ Str::limit($c->title, 40) }}</td>
                    <td>{{ $c->category }}</td>
                    <td><span class="badge badge-{{ $c->priority }}">{{ $c->priority }}</span></td>
                    <td><span class="badge badge-{{ $c->status }}">{{ str_replace('_',' ',$c->status) }}</span></td>
                    <td>{{ $c->created_at ? $c->created_at->format('M d, Y') : '' }}</td>
                    <td><a href="{{ route('student.complaints.show', $c->id) }}" class="btn btn-sm btn-primary">View</a></td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;color:var(--text-light);padding:40px;">No complaints yet</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper" style="padding:16px;">{{ $complaints->links() }}</div>
</div>
@endsection
