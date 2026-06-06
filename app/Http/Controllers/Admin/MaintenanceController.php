<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSchedule;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $start = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $schedules = MaintenanceSchedule::whereBetween('starts_at', [$start, $end])
            ->orderBy('starts_at')
            ->get();

        $allUpcoming = MaintenanceSchedule::where('ends_at', '>=', now())
            ->orderBy('starts_at')
            ->limit(20)
            ->get();

        return view('admin.maintenance', compact('schedules', 'month', 'start', 'allUpcoming'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'type' => 'required|in:water,power,wifi,elevator,general',
            'floor' => 'nullable|integer|min:1',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
        ]);

        MaintenanceSchedule::create([
            ...$request->only('title', 'description', 'type', 'floor', 'starts_at', 'ends_at'),
            'created_by' => auth()->id(),
        ]);

        ActivityLogger::log('maintenance_scheduled', $request->title, 'fa-wrench', '#f59e0b');

        return back()->with('success', 'Maintenance schedule added.');
    }

    public function destroy(MaintenanceSchedule $maintenance)
    {
        $maintenance->delete();
        return back()->with('success', 'Schedule removed.');
    }
}
