<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Complaint;
use App\Models\Room;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $students = collect();
        $complaints = collect();
        $rooms = collect();

        if (strlen($q) >= 2) {
            $students = User::where('role', 'student')
                ->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhereHas('studentDetail', function ($sd) use ($q) {
                            $sd->where('roll_no', 'like', "%{$q}%")
                                ->orWhere('department', 'like', "%{$q}%");
                        });
                })
                ->with('studentDetail.room')
                ->limit(8)
                ->get();

            $complaints = Complaint::where('title', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%")
                ->with('student')
                ->limit(6)
                ->get();

            $rooms = Room::where('number', 'like', "%{$q}%")
                ->limit(6)
                ->get();
        }

        return view('admin.search', compact('q', 'students', 'complaints', 'rooms'));
    }
}
