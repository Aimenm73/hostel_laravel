<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('admin.profile');
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        auth()->user()->update($request->only('name', 'phone'));

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
