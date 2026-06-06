@extends('layouts.admin')
@section('title', 'Visitor Passes - Admin')
@section('page-title', 'Visitor Passes')
@section('content')
@if($pendingCount > 0)
    <div class="alert alert-error" style="margin-bottom:20px;background:rgba(255,209,102,0.15);border-color:#ffd166;color:var(--text);">
        <i class="fas fa-id-card"></i> {{ $pendingCount }} visitor pass{{ $pendingCount > 1 ? 'es' : '' }} awaiting approval
    </div>
@endif

<div class="card">
    <div class="card-header"><h3><i class="fas fa-id-card"></i> All Visitor Pass Requests</h3></div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Visitor</th>
                    <th>Visit Date</th>
                    <th>Arrival</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($passes as $p)
                <tr>
                    <td>{{ $p->student->name ?? 'N/A' }}<br><small style="color:var(--text-light);">{{ $p->student->studentDetail->roll_no ?? '' }}</small></td>
                    <td><strong>{{ $p->visitor_name }}</strong><br><small>{{ $p->relationship }}</small></td>
                    <td>{{ $p->visit_date->format('M d, Y') }}</td>
                    <td>{{ $p->expected_arrival ?? '—' }}</td>
                    <td>{{ Str::limit($p->purpose, 40) }}</td>
                    <td><span class="badge badge-{{ $p->status }}">{{ $p->status }}</span></td>
                    <td>
                        @if($p->status === 'pending')
                            <form method="POST" action="{{ route('admin.visitorPasses.approve', $p->id) }}" style="display:inline;">@csrf @method('PATCH')<button class="btn btn-sm btn-success"><i class="fas fa-check"></i></button></form>
                            <form method="POST" action="{{ route('admin.visitorPasses.reject', $p->id) }}" style="display:inline;">@csrf @method('PATCH')<button class="btn btn-sm btn-danger"><i class="fas fa-times"></i></button></form>
                        @else
                            <small style="color:var(--text-light);">{{ $p->reviewed_at ? $p->reviewed_at->diffForHumans() : '' }}</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-light);">No visitor pass requests yet</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper" style="padding:16px;">{{ $passes->links() }}</div>
</div>
@endsection
