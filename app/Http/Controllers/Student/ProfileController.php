<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $user->load('studentDetail.room');
        $idCardData = json_encode([
            'hostel' => 'COMSATS Hostel',
            'id' => $user->id,
            'name' => $user->name,
            'roll' => $user->studentDetail->roll_no ?? '',
            'room' => $user->studentDetail->room->number ?? 'N/A',
        ]);
        return view('student.profile', compact('user', 'idCardData'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'roll_no' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1|max:4',
        ]);

        $user = auth()->user();
        $user->update($request->only('name', 'phone'));

        if ($user->studentDetail) {
            $user->studentDetail->update($request->only('roll_no', 'department', 'year'));
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        auth()->user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_pic' => 'required|image|mimes:jpeg,png,gif,webp|max:5120',
        ]);

        $file = $request->file('profile_pic');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/profiles', $filename);

        auth()->user()->update(['profile_pic' => $filename]);

        return back()->with('success', 'Profile picture updated successfully.');
    }
}
