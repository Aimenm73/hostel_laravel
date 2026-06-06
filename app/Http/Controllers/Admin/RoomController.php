<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('studentDetails.user')->get();
        $roomsByFloor = $rooms->groupBy('floor')->sortKeys();
        return view('admin.rooms', compact('rooms', 'roomsByFloor'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|string|max:10|unique:rooms,number',
            'floor' => 'required|integer|min:1',
            'type' => 'required|string|in:single,double,triple,suite',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|string|in:available,occupied,maintenance',
        ]);

        Room::create($request->only('number', 'floor', 'type', 'capacity', 'status'));

        return back()->with('success', 'Room added successfully.');
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'number' => 'required|string|max:10|unique:rooms,number,' . $room->id,
            'floor' => 'required|integer|min:1',
            'type' => 'required|string|in:single,double,triple,suite',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|string|in:available,occupied,maintenance',
        ]);

        $room->update($request->only('number', 'floor', 'type', 'capacity', 'status'));

        return back()->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        if ($room->studentDetails()->count() > 0) {
            return back()->with('error', 'Cannot delete room with assigned students.');
        }

        $room->delete();
        return back()->with('success', 'Room deleted successfully.');
    }
}
