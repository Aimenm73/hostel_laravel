<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('date', 'desc')->paginate(10);
        return view('admin.events', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'venue' => 'nullable|string|max:200',
            'date' => 'required|date',
            'time' => 'required',
            'max_seats' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
        ]);

        $data = $request->only('title', 'description', 'venue', 'date', 'time', 'max_seats', 'status');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/events', $filename);
            $data['image'] = $filename;
        }

        Event::create($data);

        return back()->with('success', 'Event created successfully.');
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'venue' => 'nullable|string|max:200',
            'date' => 'required|date',
            'time' => 'required',
            'max_seats' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:5120',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
        ]);

        $data = $request->only('title', 'description', 'venue', 'date', 'time', 'max_seats', 'status');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/events', $filename);
            $data['image'] = $filename;
        }

        $event->update($data);

        return back()->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return back()->with('success', 'Event deleted successfully.');
    }

    public function registrations(Event $event)
    {
        $registrations = Registration::where('event_id', $event->id)
            ->with('student')
            ->get();

        return response()->json([
            'event' => $event,
            'registrations' => $registrations,
        ]);
    }
}
