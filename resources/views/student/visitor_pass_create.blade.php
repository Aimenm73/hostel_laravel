@extends('layouts.student')
@section('title', 'Request Visitor Pass')
@section('page-title', 'Request Visitor Pass')
@section('content')
<div class="card" style="max-width:560px;">
    <div class="card-header"><h3><i class="fas fa-id-card"></i> New Visitor Pass</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('student.visitorPasses.store') }}">
            @csrf
            <div class="form-group">
                <label>Visitor Full Name</label>
                <input type="text" name="visitor_name" class="form-control" value="{{ old('visitor_name') }}" required>
            </div>
            <div class="form-group">
                <label>Relationship</label>
                <input type="text" name="relationship" class="form-control" placeholder="e.g. Father, Friend" value="{{ old('relationship') }}">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Visit Date</label>
                    <input type="date" name="visit_date" class="form-control" value="{{ old('visit_date') }}" min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label>Expected Arrival</label>
                    <input type="time" name="expected_arrival" class="form-control" value="{{ old('expected_arrival') }}">
                </div>
            </div>
            <div class="form-group">
                <label>Purpose of Visit</label>
                <textarea name="purpose" class="form-control" rows="3" required>{{ old('purpose') }}</textarea>
            </div>
            <div style="display:flex;gap:10px;">
                <a href="{{ route('student.visitorPasses.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
