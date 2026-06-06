<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessMenu;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;

class MessMenuController extends Controller
{
    public function index()
    {
        $menus = MessMenu::orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")->get();
        $days = MessMenu::dayLabels();

        if ($menus->isEmpty()) {
            foreach (array_keys($days) as $day) {
                MessMenu::create(['day_of_week' => $day]);
            }
            $menus = MessMenu::orderByRaw("FIELD(day_of_week, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")->get();
        }

        return view('admin.mess_menu', compact('menus', 'days'));
    }

    public function update(Request $request, MessMenu $messMenu)
    {
        $request->validate([
            'breakfast' => 'nullable|string|max:1000',
            'lunch' => 'nullable|string|max:1000',
            'dinner' => 'nullable|string|max:1000',
        ]);

        $messMenu->update($request->only('breakfast', 'lunch', 'dinner'));

        ActivityLogger::log('mess_menu_updated', "Updated menu for {$messMenu->day_label}", 'fa-utensils', '#06d6a0');

        return back()->with('success', "{$messMenu->day_label} menu updated.");
    }
}
