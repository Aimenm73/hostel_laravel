@extends('layouts.student')
@section('title', 'New Complaint')
@section('page-title', 'New Complaint')
@section('content')
<div class="card" style="max-width:700px;">
    <div class="card-header"><h3>Submit a Complaint</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('student.complaints.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group"><label>Title</label><input type="text" name="title" class="form-control" value="{{ old('title') }}" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" class="form-control" required>{{ old('description') }}</textarea></div>
            <div class="form-row">
                <div class="form-group"><label>Category</label>
                    <select name="category" class="form-control" required>
                        <option value="maintenance">Maintenance</option><option value="noise">Noise</option><option value="food">Food</option><option value="security">Security</option><option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group"><label>Priority</label>
                    <select name="priority" class="form-control" required>
                        <option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option>
                    </select>
                </div>
            </div>
            <div class="form-group"><label>Image (optional)</label><input type="file" name="image" class="form-control" accept="image/*"></div>
            <div style="display:flex;gap:10px;">
                <a href="{{ route('student.complaints.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Complaint</button>
            </div>
        </form>
    </div>
</div>
@endsection
