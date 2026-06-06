@extends('layouts.admin')
@section('title', 'Fee Ledger')
@section('page-title', 'Fee Ledger')
@section('content')
@include('partials.page-hero', [
    'icon' => 'fa-wallet',
    'title' => 'Hostel Fee Ledger',
    'subtitle' => 'Manage student fees and payment records',
    'actions' => '<button class="btn btn-primary" onclick="openModal(\'addFeeModal\')"><i class="fas fa-plus"></i> Add Fee</button>'
])

<div class="fee-summary-grid">
    <div class="fee-summary-card pending"><h4>Pending Total</h4><div class="amount">Rs. {{ number_format($stats['pending'], 0) }}</div></div>
    <div class="fee-summary-card paid"><h4>Collected</h4><div class="amount">Rs. {{ number_format($stats['paid'], 0) }}</div></div>
    <div class="fee-summary-card overdue"><h4>Overdue Items</h4><div class="amount">{{ $stats['overdue'] }}</div></div>
</div>

<div style="margin-bottom:16px;display:flex;gap:8px;">
    <a href="?status=" class="btn btn-sm {{ !$status ? 'btn-primary' : 'btn-outline' }}">All</a>
    <a href="?status=pending" class="btn btn-sm {{ $status === 'pending' ? 'btn-primary' : 'btn-outline' }}">Pending</a>
    <a href="?status=paid" class="btn btn-sm {{ $status === 'paid' ? 'btn-primary' : 'btn-outline' }}">Paid</a>
</div>

<div class="card glass-card">
    @forelse($fees as $fee)
        <div class="fee-row">
            <div style="flex:1;">
                <strong>{{ $fee->student->name }}</strong>
                <div style="font-size:12px;color:var(--text-light);">{{ $fee->title }} · {{ ucfirst($fee->category) }}</div>
            </div>
            <div style="text-align:right;">
                <strong>Rs. {{ number_format($fee->amount, 0) }}</strong>
                <div style="font-size:11px;color:var(--text-light);">Due {{ $fee->due_date->format('M d, Y') }}</div>
            </div>
            <span class="badge badge-{{ $fee->status === 'paid' ? 'approved' : ($fee->due_date->isPast() ? 'urgent' : 'pending') }}">{{ $fee->status }}</span>
            @if($fee->status === 'pending')
                <form method="POST" action="{{ route('admin.fees.paid', $fee) }}">@csrf @method('PATCH')
                    <button class="btn btn-sm btn-success"><i class="fas fa-check"></i> Paid</button>
                </form>
            @endif
            <form method="POST" action="{{ route('admin.fees.destroy', $fee) }}" onsubmit="return confirm('Remove?')">@csrf @method('DELETE')
                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
            </form>
        </div>
    @empty
        <p style="text-align:center;padding:40px;color:var(--text-light);">No fee entries</p>
    @endforelse
    <div style="padding:16px;">{{ $fees->links() }}</div>
</div>

<div class="modal-overlay" id="addFeeModal">
    <div class="modal" style="max-width:480px;">
        <div class="modal-header"><h3>Add Fee</h3><button class="modal-close" onclick="closeModal('addFeeModal')">&times;</button></div>
        <form method="POST" action="{{ route('admin.fees.store') }}">@csrf
            <div class="modal-body">
                <div class="form-group"><label>Student</label><select name="student_id" class="form-control" required>@foreach($students as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ $s->email }})</option>@endforeach</select></div>
                <div class="form-group"><label>Title</label><input type="text" name="title" class="form-control" required placeholder="Monthly Hostel Fee"></div>
                <div class="form-row">
                    <div class="form-group"><label>Category</label><select name="category" class="form-control"><option value="hostel">Hostel</option><option value="mess">Mess</option><option value="security">Security</option><option value="other">Other</option></select></div>
                    <div class="form-group"><label>Amount (Rs.)</label><input type="number" name="amount" class="form-control" step="0.01" min="0" required></div>
                </div>
                <div class="form-group"><label>Due Date</label><input type="date" name="due_date" class="form-control" required></div>
                <div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal('addFeeModal')">Cancel</button><button type="submit" class="btn btn-primary">Add Fee</button></div>
        </form>
    </div>
</div>
@endsection
