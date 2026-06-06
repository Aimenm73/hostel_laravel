@extends('layouts.student')
@section('title', 'Attendance')
@section('page-title', 'Attendance')
@section('content')
@include('partials.page-hero', ['icon' => 'fa-clipboard-check', 'title' => 'Night Roll Call', 'subtitle' => 'Check in when curfew roll call is active'])

@if($activeSession)
    @if($myRecord)
        <div class="attendance-success-card glass-card">
            <i class="fas fa-check-circle"></i>
            <h2>You're marked present!</h2>
            <p style="color:var(--text-light);margin-top:8px;">{{ $activeSession->title }} · {{ $myRecord->marked_at->format('g:i A') }} via {{ $myRecord->method }}</p>
            <div class="present-pulse" style="margin-top:20px;justify-content:center;">Checked in</div>
        </div>
    @else
        <div class="roll-call-live" style="margin-bottom:28px;">
            <div>
                <h2 style="margin:0 0 8px;">{{ $activeSession->title }} is OPEN</h2>
                <p style="opacity:0.85;">Tap below to mark yourself present, or scan the QR displayed by warden.</p>
                <form method="POST" action="{{ route('student.attendance.checkIn') }}" style="margin-top:20px;">@csrf
                    <button type="submit" class="btn btn-primary" style="padding:14px 28px;font-size:16px;"><i class="fas fa-fingerprint"></i> Check In Now</button>
                </form>
            </div>
            <div class="roll-qr-wrap">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode(route('student.attendance.scan', $activeSession->qr_token)) }}" alt="QR" width="160">
            </div>
        </div>
    @endif
@else
    <div class="glass-card" style="text-align:center;padding:48px;margin-bottom:28px;">
        <i class="fas fa-moon" style="font-size:48px;color:var(--text-light);opacity:0.4;"></i>
        <h3 style="margin:16px 0 8px;">No active roll call</h3>
        <p style="color:var(--text-light);">Check back when the warden starts tonight's session.</p>
    </div>
@endif

<div class="card">
    <div class="card-header"><h3>Your Attendance History</h3></div>
    <div class="table-responsive">
        <table>
            <thead><tr><th>Session</th><th>Date</th><th>Method</th><th>Time</th></tr></thead>
            <tbody>
            @forelse($history as $h)
                <tr>
                    <td>{{ $h->session->title ?? 'Roll Call' }}</td>
                    <td>{{ $h->session->session_date->format('M d, Y') }}</td>
                    <td><span class="badge badge-approved">{{ $h->method }}</span></td>
                    <td>{{ $h->marked_at->format('H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;padding:32px;color:var(--text-light);">No records yet</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
