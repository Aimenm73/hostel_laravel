@extends('layouts.student')
@section('title', 'Leave Requests')
@section('page-title', 'Leave Requests')
@section('content')
<div class="card">
    <div class="card-header"><h3>My Leave Requests</h3><a href="{{ route('student.leaveRequests.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Request</a></div>
    <div class="table-responsive">
        <table>
            <thead><tr><th>Start Date</th><th>End Date</th><th>Reason</th><th>Status</th><th>Submitted</th><th>Reviewed At</th></tr></thead>
            <tbody>
            @forelse($leaveRequests as $lr)
                <tr>
                    <td>{{ $lr->start_date ? $lr->start_date->format('M d, Y') : '' }}</td>
                    <td>{{ $lr->end_date ? $lr->end_date->format('M d, Y') : '' }}</td>
                    <td>{{ Str::limit($lr->reason, 50) }}</td>
                    <td><span class="badge badge-{{ $lr->status }}">{{ $lr->status }}</span></td>
                    <td>{{ $lr->created_at ? $lr->created_at->format('M d, Y') : '' }}</td>
                    <td>{{ $lr->reviewed_at ? $lr->reviewed_at->format('M d, Y') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center;color:var(--text-light);padding:40px;">No leave requests</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper" style="padding:16px;">{{ $leaveRequests->links() }}</div>
</div>
@endsection
