<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hostel Report — {{ ucfirst($type) }}</title>
    <style>
        body{font-family:'Segoe UI',sans-serif;padding:40px;color:#1a1a2e;}
        h1{font-size:24px;margin-bottom:4px;}
        .meta{color:#666;font-size:13px;margin-bottom:32px;}
        table{width:100%;border-collapse:collapse;font-size:13px;}
        th,td{border:1px solid #e9ecef;padding:10px 12px;text-align:left;}
        th{background:#4361ee;color:#fff;}
        tr:nth-child(even){background:#f8f9fa;}
        @media print{.no-print{display:none;} body{padding:20px;}}
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()" style="margin-bottom:20px;padding:10px 20px;cursor:pointer;">Print / Save as PDF</button>
    <h1>COMSATS Hostel — {{ ucfirst($type) }} Report</h1>
    <p class="meta">Generated {{ now()->format('F d, Y H:i') }}</p>
    <table>
        @if($type === 'complaints')
            <thead><tr><th>Title</th><th>Student</th><th>Priority</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>@foreach($data as $r)<tr><td>{{ $r->title }}</td><td>{{ $r->student->name ?? '' }}</td><td>{{ $r->priority }}</td><td>{{ $r->status }}</td><td>{{ $r->created_at?->format('Y-m-d') }}</td></tr>@endforeach</tbody>
        @elseif($type === 'leaves')
            <thead><tr><th>Student</th><th>From</th><th>To</th><th>Status</th></tr></thead>
            <tbody>@foreach($data as $r)<tr><td>{{ $r->student->name ?? '' }}</td><td>{{ $r->start_date?->format('Y-m-d') }}</td><td>{{ $r->end_date?->format('Y-m-d') }}</td><td>{{ $r->status }}</td></tr>@endforeach</tbody>
        @else
            <thead><tr><th>Name</th><th>Email</th><th>Roll</th><th>Department</th><th>Room</th></tr></thead>
            <tbody>@foreach($data as $r)<tr><td>{{ $r->name }}</td><td>{{ $r->email }}</td><td>{{ $r->studentDetail->roll_no ?? '' }}</td><td>{{ $r->studentDetail->department ?? '' }}</td><td>{{ $r->studentDetail->room->number ?? '' }}</td></tr>@endforeach</tbody>
        @endif
    </table>
</body>
</html>
