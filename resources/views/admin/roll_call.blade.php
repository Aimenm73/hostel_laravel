@extends('layouts.admin')
@section('title', 'Roll Call')
@section('page-title', 'Roll Call')
@section('content')
@include('partials.page-hero', [
    'icon' => 'fa-clipboard-check',
    'title' => 'Night Roll Call',
    'subtitle' => 'QR check-in for curfew attendance',
    'actions' => $activeSession ? '' : '<form method="POST" action="'.route('admin.rollCall.start').'" style="display:inline;">'.csrf_field().'<button class="btn btn-primary"><i class="fas fa-play"></i> Start Tonight\'s Roll Call</button></form>'
])

@if($activeSession)
@php
    $present = $activeSession->records->count();
    $pct = $totalStudents > 0 ? round(($present / $totalStudents) * 100) : 0;
    $checkInUrl = route('student.attendance.scan', $activeSession->qr_token);
@endphp
<div class="roll-call-live">
    <div>
        <h2 style="margin:0 0 8px;">{{ $activeSession->title }} — LIVE</h2>
        <p style="opacity:0.85;margin-bottom:16px;">Students scan the QR or tap check-in on their portal</p>
        <div class="roll-stats">
            <div class="roll-stat"><strong>{{ $present }}</strong><span>Present</span></div>
            <div class="roll-stat"><strong>{{ $totalStudents - $present }}</strong><span>Absent</span></div>
            <div class="roll-stat"><strong>{{ $pct }}%</strong><span>Rate</span></div>
        </div>
        <form method="POST" action="{{ route('admin.rollCall.close', $activeSession) }}" style="margin-top:20px;">@csrf @method('PATCH')
            <button class="btn btn-danger"><i class="fas fa-stop"></i> Close Session</button>
        </form>
    </div>
    <div class="roll-qr-wrap">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($checkInUrl) }}" alt="Roll call QR" width="200" height="200">
        <p style="color:#333;font-size:11px;margin-top:8px;max-width:200px;">Scan to mark present</p>
    </div>
</div>

<div class="card" style="margin-bottom:28px;">
    <div class="card-header"><h3>Present Students</h3></div>
    <div class="table-responsive">
        <table>
            <thead><tr><th>Name</th><th>Roll</th><th>Method</th><th>Time</th></tr></thead>
            <tbody>
            @forelse($activeSession->records as $rec)
                <tr>
                    <td>{{ $rec->student->name }}</td>
                    <td>{{ $rec->student->studentDetail->roll_no ?? '' }}</td>
                    <td><span class="badge badge-approved">{{ $rec->method }}</span></td>
                    <td>{{ $rec->marked_at->format('H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;padding:24px;color:var(--text-light);">Waiting for check-ins...</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header"><h3>Session History</h3></div>
    <div class="table-responsive">
        <table>
            <thead><tr><th>Date</th><th>Title</th><th>Present</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($sessions as $s)
                <tr>
                    <td>{{ $s->session_date->format('M d, Y') }}</td>
                    <td>{{ $s->title }}</td>
                    <td>{{ $s->records_count }} / {{ $totalStudents }}</td>
                    <td><span class="badge badge-{{ $s->status === 'open' ? 'pending' : 'approved' }}">{{ $s->status }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding:16px;">{{ $sessions->links() }}</div>
</div>
@endsection
