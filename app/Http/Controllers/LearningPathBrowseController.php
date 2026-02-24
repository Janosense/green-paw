<?php

namespace App\Http\Controllers;

use App\Models\LearningPath;
use Illuminate\Support\Facades\Auth;

class LearningPathBrowseController extends Controller
{
    public function index()
    {
        $paths = LearningPath::published()
            ->withCount('courses')
            ->orderBy('sort_order')
            ->get();

        return view('learning-paths.index', compact('paths'));
    }

    public function show(LearningPath $path)
    {
        if (!$path->is_published) {
            abort(404);
        }

        $path->load('courses.instructor', 'courses.lessons');
        $user = Auth::user();
        $progress = $user ? $path->progressFor($user) : null;

        // Get enrollment status for each course
        $courseProgress = [];
        foreach ($path->courses as $course) {
            $enrollment = $user?->enrollmentFor($course);
            $courseProgress[$course->id] = [
                'enrolled' => $enrollment !== null,
                'percent' => $enrollment?->progress_percent ?? 0,
                'completed' => $enrollment?->isCompleted() ?? false,
            ];
        }

        return view('learning-paths.show', compact('path', 'progress', 'courseProgress'));
    }
}
