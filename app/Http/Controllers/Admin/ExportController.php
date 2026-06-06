<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Models\HostelFee;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function index()
    {
        return view('admin.reports');
    }

    public function studentsCsv(): StreamedResponse
    {
        return $this->csv('students_' . date('Y-m-d') . '.csv', ['Name', 'Email', 'Roll No', 'Department', 'Room', 'Phone'], function ($out) {
            User::where('role', 'student')->with('studentDetail.room')->chunk(100, function ($rows) use ($out) {
                foreach ($rows as $s) {
                    fputcsv($out, [
                        $s->name,
                        $s->email,
                        $s->studentDetail->roll_no ?? '',
                        $s->studentDetail->department ?? '',
                        $s->studentDetail->room->number ?? '',
                        $s->phone ?? '',
                    ]);
                }
            });
        });
    }

    public function complaintsCsv(): StreamedResponse
    {
        return $this->csv('complaints_' . date('Y-m-d') . '.csv', ['Title', 'Student', 'Priority', 'Status', 'Created'], function ($out) {
            Complaint::with('student')->orderBy('created_at', 'desc')->chunk(100, function ($rows) use ($out) {
                foreach ($rows as $c) {
                    fputcsv($out, [
                        $c->title,
                        $c->student->name ?? '',
                        $c->priority,
                        $c->status,
                        $c->created_at?->format('Y-m-d H:i'),
                    ]);
                }
            });
        });
    }

    public function feesCsv(): StreamedResponse
    {
        return $this->csv('fees_' . date('Y-m-d') . '.csv', ['Student', 'Title', 'Amount', 'Due', 'Status'], function ($out) {
            HostelFee::with('student')->orderBy('due_date')->chunk(100, function ($rows) use ($out) {
                foreach ($rows as $f) {
                    fputcsv($out, [
                        $f->student->name ?? '',
                        $f->title,
                        $f->amount,
                        $f->due_date->format('Y-m-d'),
                        $f->status,
                    ]);
                }
            });
        });
    }

    public function printReport(Request $request)
    {
        $type = $request->get('type', 'students');
        $data = match ($type) {
            'complaints' => Complaint::with('student')->orderBy('created_at', 'desc')->limit(200)->get(),
            'leaves' => LeaveRequest::with('student')->orderBy('created_at', 'desc')->limit(200)->get(),
            default => User::where('role', 'student')->with('studentDetail.room')->get(),
        };

        return view('admin.reports_print', compact('type', 'data'));
    }

    private function csv(string $filename, array $headers, callable $writer): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $writer) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            $writer($out);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
