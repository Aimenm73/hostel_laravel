@extends('layouts.student')
@section('title', 'Visitor Passes')
@section('page-title', 'Visitor Passes')
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <p style="color:var(--text-light);font-size:14px;">Request a gate pass for family or guests visiting the hostel.</p>
    <a href="{{ route('student.visitorPasses.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Request</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr><th>Visitor</th><th>Date</th><th>Arrival</th><th>Purpose</th><th>Status</th></tr>
            </thead>
            <tbody>
            @forelse($passes as $p)
                <tr>
                    <td><strong>{{ $p->visitor_name }}</strong><br><small>{{ $p->relationship }}</small></td>
                    <td>{{ $p->visit_date->format('M d, Y') }}</td>
                    <td>{{ $p->expected_arrival ?? '—' }}</td>
                    <td>{{ Str::limit($p->purpose, 50) }}</td>
                    <td><span class="badge badge-{{ $p->status }}">{{ $p->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-light);">No visitor pass requests yet</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper" style="padding:16px;">{{ $passes->links() }}</div>
</div>
@endsection
