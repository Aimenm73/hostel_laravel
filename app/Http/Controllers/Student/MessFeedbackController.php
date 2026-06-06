<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\MessFeedback;
use Illuminate\Http\Request;

class MessFeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'meal_date' => 'required|date',
            'meal_type' => 'required|in:breakfast,lunch,dinner',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        MessFeedback::updateOrCreate(
            [
                'student_id' => auth()->id(),
                'meal_date' => $request->meal_date,
                'meal_type' => $request->meal_type,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return back()->with('success', 'Thank you! Your meal rating was saved.');
    }
}
