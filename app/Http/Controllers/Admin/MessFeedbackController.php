<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessFeedback;
use Illuminate\Support\Facades\DB;

class MessFeedbackController extends Controller
{
    public function index()
    {
        $avgRating = MessFeedback::avg('rating') ?? 0;
        $totalReviews = MessFeedback::count();

        $byMeal = MessFeedback::select('meal_type', DB::raw('AVG(rating) as avg_rating'), DB::raw('COUNT(*) as total'))
            ->groupBy('meal_type')
            ->get();

        $recent = MessFeedback::with('student')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $distribution = MessFeedback::select('rating', DB::raw('COUNT(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        return view('admin.mess_feedback', compact('avgRating', 'totalReviews', 'byMeal', 'recent', 'distribution'));
    }
}
