@extends('layouts.student')
@section('title', 'Announcements')
@section('page-title', 'Announcements')
@section('content')
<div class="card">
    <div class="card-header"><h3>All Announcements</h3></div>
    <div class="table-responsive">
        <table>
            <thead><tr><th>Title</th><th>Type</th><th>Content</th><th>Date</th></tr></thead>
            <tbody>
            @forelse($announcements as $a)
                <tr>
                    <td style="font-weight:600;">{{ $a->title }}</td>
                    <td><span class="badge badge-{{ $a->type }}">{{ $a->type }}</span></td>
                    <td>{{ $a->content }}</td>
                    <td>{{ $a->created_at ? $a->created_at->format('M d, Y') : '' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:var(--text-light);padding:40px;">No announcements</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper" style="padding:16px;">{{ $announcements->links() }}</div>
</div>
@endsection
