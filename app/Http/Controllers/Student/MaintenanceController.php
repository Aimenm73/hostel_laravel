<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $start = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $floor = auth()->user()->studentDetail?->room?->floor;

        $schedules = MaintenanceSchedule::whereBetween('starts_at', [$start, $end])
            ->where(function ($q) use ($floor) {
                $q->whereNull('floor');
                if ($floor) {
                    $q->orWhere('floor', $floor);
                }
            })
            ->orderBy('starts_at')
            ->get();

        $activeNow = MaintenanceSchedule::where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->where(function ($q) use ($floor) {
                $q->whereNull('floor');
                if ($floor) {
                    $q->orWhere('floor', $floor);
                }
            })
            ->get();

        return view('student.maintenance', compact('schedules', 'month', 'start', 'activeNow', 'floor'));
    }
}
