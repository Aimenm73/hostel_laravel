@extends('layouts.student')
@section('title', 'New Leave Request')
@section('page-title', 'New Leave Request')
@section('content')
<div class="card" style="max-width:600px;">
    <div class="card-header"><h3>Submit Leave Request</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('student.leaveRequests.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group"><label>Start Date</label><input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required></div>
                <div class="form-group"><label>End Date</label><input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required></div>
            </div>
            <div class="form-group"><label>Reason</label><textarea name="reason" class="form-control" required>{{ old('reason') }}</textarea></div>
            <div style="display:flex;gap:10px;">
                <a href="{{ route('student.leaveRequests.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
