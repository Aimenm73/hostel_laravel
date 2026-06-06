<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentDetail;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index()
    {
        $students = User::where('role', 'student')
            ->with(['studentDetail.room'])
            ->paginate(10);

        $rooms = Room::all();

        return view('admin.students', compact('students', 'rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:6',
            'roll_no' => 'required|string|max:50',
            'department' => 'required|string|max:100',
            'year' => 'required|integer|min:1|max:4',
            'room_id' => 'nullable|exists:rooms,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'student',
        ]);

        StudentDetail::create([
            'user_id' => $user->id,
            'roll_no' => $request->roll_no,
            'department' => $request->department,
            'year' => $request->year,
            'room_id' => $request->room_id,
        ]);

        return back()->with('success', 'Student added successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'roll_no' => 'required|string|max:50',
            'department' => 'required|string|max:100',
            'year' => 'required|integer|min:1|max:4',
            'room_id' => 'nullable|exists:rooms,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        $user->studentDetail()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'roll_no' => $request->roll_no,
                'department' => $request->department,
                'year' => $request->year,
                'room_id' => $request->room_id,
            ]
        );

        return back()->with('success', 'Student updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return back()->with('success', 'Student deleted successfully.');
    }

    public function assignRoom(Request $request, $id)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
        ]);

        $user = User::findOrFail($id);
        $room = Room::findOrFail($request->room_id);

        if ($room->occupantsCount() >= $room->capacity) {
            return back()->with('error', 'Room is already full.');
        }

        $user->studentDetail()->updateOrCreate(
            ['user_id' => $user->id],
            ['room_id' => $request->room_id]
        );

        return back()->with('success', 'Room assigned successfully.');
    }
}
