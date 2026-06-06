<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Notification;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('date', 'desc')->paginate(10);
        $myRegistrations = Registration::where('student_id', auth()->id())
            ->pluck('event_id')
            ->toArray();

        return view('student.events', compact('events', 'myRegistrations'));
    }

    public function register(Event $event)
    {
        $userId = auth()->id();

        // Check if already registered
        $existing = Registration::where('event_id', $event->id)
            ->where('student_id', $userId)
            ->first();

        if ($existing) {
            return back()->with('error', 'You are already registered for this event.');
        }

        // Check seats
        if ($event->booked >= $event->max_seats) {
            return back()->with('error', 'No seats available.');
        }

        Registration::create([
            'event_id' => $event->id,
            'student_id' => $userId,
        ]);

        $event->increment('booked');

        Notification::create([
            'user_id' => $userId,
            'message' => "You registered for {$event->title}",
        ]);

        ActivityLogger::log(
            'event_registered',
            'Registered for event: ' . $event->title,
            'fa-calendar-check',
            '#06d6a0'
        );

        return back()->with('success', 'Registered successfully!');
    }

    public function cancel(Event $event)
    {
        $registration = Registration::where('event_id', $event->id)
            ->where('student_id', auth()->id())
            ->first();

        if ($registration) {
            $registration->delete();
            $event->decrement('booked');

            ActivityLogger::log(
                'event_cancelled',
                'Cancelled registration for: ' . $event->title,
                'fa-calendar-times',
                '#ef476f'
            );

            return back()->with('success', 'Registration cancelled.');
        }

        return back()->with('error', 'Registration not found.');
    }
}
