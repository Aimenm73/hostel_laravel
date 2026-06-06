<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MessMenu;
use Carbon\Carbon;

class MessMenuController extends Controller
{
    public function index()
    {
        $menus = MessMenu::orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")->get();
        $todayKey = strtolower(Carbon::now()->format('l'));
        $todayMenu = $menus->firstWhere('day_of_week', $todayKey);

        return view('student.mess_menu', compact('menus', 'todayKey', 'todayMenu'));
    }
}
