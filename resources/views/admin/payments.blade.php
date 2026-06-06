@extends('layouts.admin')
@section('title', 'Payments - Admin')
@section('page-title', 'Payments')
@section('content')
<div class="card" style="margin-bottom:24px;">
    <div class="card-header"><h3>Pending Payments</h3></div>
    <div class="table-responsive">
        <table>
            <thead><tr><th>Student</th><th>Email</th><th>Event</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse($pendingPayments as $p)
                <tr>
                    <td>{{ $p->student->name ?? 'N/A' }}</td>
                    <td>{{ $p->student->email ?? '' }}</td>
                    <td>{{ $p->event->title ?? 'N/A' }}</td>
                    <td style="display:flex;gap:6px;">
                        <form method="POST" action="{{ route('admin.payments.approve', $p->id) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-success">Approve</button></form>
                        <form method="POST" action="{{ route('admin.payments.reject', $p->id) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-danger">Reject</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:var(--text-light);padding:40px;">No pending payments</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="card">
    <div class="card-header"><h3>Approved Payments</h3></div>
    <div class="table-responsive">
        <table>
            <thead><tr><th>Student</th><th>Event</th><th>Date</th></tr></thead>
            <tbody>
            @forelse($approvedPayments as $p)
                <tr>
                    <td>{{ $p->student->name ?? 'N/A' }}</td>
                    <td>{{ $p->event->title ?? 'N/A' }}</td>
                    <td>{{ $p->payment_date ? $p->payment_date->format('M d, Y') : '' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align:center;color:var(--text-light);padding:40px;">No approved payments</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
