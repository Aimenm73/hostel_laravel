@extends('layouts.admin')
@section('title', 'Search - Admin')
@section('page-title', 'Global Search')
@section('content')
<form method="GET" action="{{ route('admin.search') }}" class="search-hero-form">
    <i class="fas fa-search"></i>
    <input type="text" name="q" class="form-control" placeholder="Search students, roll no, rooms, complaints..." value="{{ $q }}" autofocus minlength="2">
    <button type="submit" class="btn btn-primary">Search</button>
</form>

@if(strlen($q) < 2 && $q !== '')
    <p style="color:var(--text-light);text-align:center;margin-top:40px;">Enter at least 2 characters to search.</p>
@elseif(strlen($q) >= 2)
    <div class="search-results-grid">
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-users"></i> Students ({{ $students->count() }})</h3></div>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Name</th><th>Roll</th><th>Room</th><th></th></tr></thead>
                    <tbody>
                    @forelse($students as $s)
                        <tr>
                            <td>{{ $s->name }}</td>
                            <td>{{ $s->studentDetail->roll_no ?? '—' }}</td>
                            <td>{{ $s->studentDetail->room->number ?? 'Unassigned' }}</td>
                            <td><a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline">Manage</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:var(--text-light);">No students found</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-door-open"></i> Rooms ({{ $rooms->count() }})</h3></div>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Room</th><th>Floor</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($rooms as $r)
                        <tr>
                            <td>{{ $r->number }}</td>
                            <td>{{ $r->floor }}</td>
                            <td><span class="badge badge-{{ $r->status === 'available' ? 'approved' : 'pending' }}">{{ $r->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:var(--text-light);">No rooms found</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card" style="grid-column:1/-1;">
            <div class="card-header"><h3><i class="fas fa-exclamation-triangle"></i> Complaints ({{ $complaints->count() }})</h3></div>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Title</th><th>Student</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    @forelse($complaints as $c)
                        <tr>
                            <td>{{ $c->title }}</td>
                            <td>{{ $c->student->name ?? 'N/A' }}</td>
                            <td><span class="badge badge-{{ $c->status }}">{{ $c->status }}</span></td>
                            <td><a href="{{ route('admin.complaints.show', $c) }}" class="btn btn-sm btn-primary">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:var(--text-light);">No complaints found</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="search-empty-hint">
        <i class="fas fa-search"></i>
        <h3>Find anything instantly</h3>
        <p>Search by student name, email, roll number, room number, or complaint title.</p>
    </div>
@endif
@endsection
