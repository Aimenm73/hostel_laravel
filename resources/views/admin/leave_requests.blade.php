@extends('layouts.admin')
@section('title', 'Leave Requests - Admin')
@section('page-title', 'Leave Requests')
@section('content')
<div class="card">
    <div class="card-header"><h3>All Leave Requests</h3></div>
    <div class="table-responsive">
        <table>
            <thead><tr><th>Student</th><th>Roll No</th><th>Start</th><th>End</th><th>Reason</th><th>Status</th><th>Submitted</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($leaveRequests as $lr)
                <tr>
                    <td>{{ $lr->student->name ?? 'N/A' }}</td>
                    <td>{{ $lr->student->studentDetail->roll_no ?? 'N/A' }}</td>
                    <td>{{ $lr->start_date ? $lr->start_date->format('M d, Y') : '' }}</td>
                    <td>{{ $lr->end_date ? $lr->end_date->format('M d, Y') : '' }}</td>
                    <td>{{ Str::limit($lr->reason, 40) }}</td>
                    <td><span class="badge badge-{{ $lr->status }}">{{ $lr->status }}</span></td>
                    <td>{{ $lr->created_at ? $lr->created_at->format('M d, Y') : '' }}</td>
                    <td>
                        @if($lr->status === 'pending')
                            <div style="display:flex;gap:6px;">
                                <form method="POST" action="{{ route('admin.leaveRequests.approve', $lr->id) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-success"><i class="fas fa-check"></i></button></form>
                                <form method="POST" action="{{ route('admin.leaveRequests.reject', $lr->id) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-danger"><i class="fas fa-times"></i></button></form>
                            </div>
                        @else
                            <span style="font-size:12px;color:var(--text-light);">{{ $lr->reviewed_at ? $lr->reviewed_at->format('M d') : '-' }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;color:var(--text-light);padding:40px;">No leave requests</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper" style="padding:16px;">{{ $leaveRequests->links() }}</div>
</div>
@endsection
