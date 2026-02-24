<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\AssessmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizTakeController extends Controller
{
    /**
     * Start a new quiz attempt.
     */
    public function start(Quiz $quiz, AssessmentService $assessment)
    {
        $user = Auth::user();

        if (!$quiz->is_published)
            abort(404);

        // Check remaining attempts
        if ($quiz->remainingAttempts($user) <= 0) {
            return back()->with('success', 'You have used all available attempts for this quiz.');
        }

        // Check for existing in-progress attempt
        $existing = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existing) {
            return redirect()->route('quiz.take', $existing);
        }

        $attempt = $assessment->startAttempt($user, $quiz);

        return redirect()->route('quiz.take', $attempt);
    }

    /**
     * Show the quiz-taking interface.
     */
    public function take(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id())
            abort(403);
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('quiz.results', $attempt);
        }

        $quiz = $attempt->quiz;
        $questions = $quiz->questions;

        // Shuffle if configured
        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        $remainingSeconds = $attempt->remainingSeconds();

        return view('quiz.take', compact('attempt', 'quiz', 'questions', 'remainingSeconds'));
    }

    /**
     * Submit quiz answers.
     */
    public function submit(Request $request, QuizAttempt $attempt, AssessmentService $assessment)
    {
        if ($attempt->user_id !== Auth::id())
            abort(403);
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('quiz.results', $attempt);
        }

        $answers = $request->input('answers', []);
        $timeSpent = (int) $request->input('time_spent', 0);
        $tabSwitches = (int) $request->input('tab_switches', 0);

        $results = $assessment->submitAttempt($attempt, $answers, $timeSpent, $tabSwitches);

        if ($results['has_essay']) {
            return redirect()->route('quiz.results', $attempt)
                ->with('success', "Quiz submitted! Score so far: {$results['percentage']}%. Essay questions are pending instructor review.");
        }

        $message = "Quiz completed! Score: {$results['percentage']}%.";
        if ($results['passed']) {
            $message .= " ✅ Passed! +{$results['points_earned']} points";
        } else {
            $message .= " ❌ Did not meet the passing score of {$attempt->quiz->passing_score}%.";
        }

        return redirect()->route('quiz.results', $attempt)->with('success', $message);
    }

    /**
     * Show quiz results.
     */
    public function results(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id())
            abort(403);

        $attempt->load('quiz.questions');

        return view('quiz.results', compact('attempt'));
    }
}
