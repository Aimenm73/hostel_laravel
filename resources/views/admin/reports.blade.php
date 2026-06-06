@extends('layouts.admin')
@section('title', 'Reports & Export')
@section('page-title', 'Reports')
@section('content')
@include('partials.page-hero', ['icon' => 'fa-file-export', 'title' => 'Reports & Export Center', 'subtitle' => 'Download CSV files or print polished PDF-ready reports'])

<div class="export-grid">
    <div class="export-card glass-card">
        <i class="fas fa-users"></i>
        <h4>Students</h4>
        <p>Full roster with roll numbers and rooms</p>
        <div class="btn-group">
            <a href="{{ route('admin.reports.students') }}" class="btn btn-primary"><i class="fas fa-download"></i> CSV</a>
            <a href="{{ route('admin.reports.print', ['type' => 'students']) }}" target="_blank" class="btn btn-outline"><i class="fas fa-print"></i> Print / PDF</a>
        </div>
    </div>
    <div class="export-card glass-card">
        <i class="fas fa-exclamation-triangle"></i>
        <h4>Complaints</h4>
        <p>All complaints with status</p>
        <div class="btn-group">
            <a href="{{ route('admin.reports.complaints') }}" class="btn btn-primary"><i class="fas fa-download"></i> CSV</a>
            <a href="{{ route('admin.reports.print', ['type' => 'complaints']) }}" target="_blank" class="btn btn-outline"><i class="fas fa-print"></i> Print / PDF</a>
        </div>
    </div>
    <div class="export-card glass-card">
        <i class="fas fa-wallet"></i>
        <h4>Fee Ledger</h4>
        <p>Hostel fees and payment status</p>
        <div class="btn-group">
            <a href="{{ route('admin.reports.fees') }}" class="btn btn-primary"><i class="fas fa-download"></i> CSV</a>
            <a href="{{ route('admin.reports.print', ['type' => 'leaves']) }}" target="_blank" class="btn btn-outline"><i class="fas fa-print"></i> Leave Report</a>
        </div>
    </div>
</div>
@endsection
