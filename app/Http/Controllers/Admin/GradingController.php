<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use App\Services\AssessmentService;
use Illuminate\Http\Request;

class GradingController extends Controller
{
    public function index()
    {
        $pendingAttempts = QuizAttempt::needsGrading()
            ->with('quiz.course', 'quiz.questions', 'user')
            ->latest('submitted_at')
            ->paginate(20);

        return view('admin.grading.index', compact('pendingAttempts'));
    }

    public function grade(Request $request, QuizAttempt $attempt, AssessmentService $assessment)
    {
        $validated = $request->validate([
            'essay_points' => ['required', 'integer', 'min:0'],
            'feedback' => ['nullable', 'string'],
        ]);

        $assessment->gradeEssay($attempt, $validated['essay_points'], $validated['feedback'] ?? '');

        return redirect()->route('admin.grading.index')
            ->with('success', "Graded {$attempt->user->name}'s attempt. Final score: {$attempt->percentage}%.");
    }
}
