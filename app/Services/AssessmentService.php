<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;

class AssessmentService
{
    public function __construct(
        protected GamificationService $gamification
    ) {
    }

    /**
     * Start a new quiz attempt.
     */
    public function startAttempt(User $user, Quiz $quiz): QuizAttempt
    {
        return QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'started_at' => now(),
            'total_points' => $quiz->totalPoints(),
        ]);
    }

    /**
     * Submit and grade an attempt.
     */
    public function submitAttempt(QuizAttempt $attempt, array $answers, int $timeSpent = 0, int $tabSwitches = 0): array
    {
        $attempt->update([
            'answers' => $answers,
            'submitted_at' => now(),
            'time_spent_seconds' => $timeSpent,
            'tab_switches' => $tabSwitches,
        ]);

        // Auto-grade
        $attempt->autoGrade();
        $attempt->refresh();

        $results = [
            'score' => $attempt->score,
            'total' => $attempt->total_points,
            'percentage' => $attempt->percentage,
            'passed' => $attempt->isPassed(),
            'has_essay' => $attempt->quiz->hasEssayQuestions(),
            'points_earned' => 0,
        ];

        // Award gamification points if fully graded and passed
        if ($attempt->status === 'graded' && $attempt->isPassed()) {
            $results['points_earned'] = $this->gamification->awardPoints(
                $attempt->user,
                20,
                'quiz_complete',
                "Passed quiz: {$attempt->quiz->title} ({$attempt->percentage}%)"
            );
            $this->gamification->checkAndAwardBadges($attempt->user);
        }

        return $results;
    }

    /**
     * Instructor grades essay questions in an attempt.
     */
    public function gradeEssay(QuizAttempt $attempt, int $essayPoints, string $feedback = ''): void
    {
        $newScore = $attempt->score + $essayPoints;
        $total = $attempt->total_points;
        $percentage = $total > 0 ? (int) round(($newScore / $total) * 100) : 0;

        $attempt->update([
            'score' => $newScore,
            'percentage' => $percentage,
            'status' => 'graded',
            'instructor_feedback' => $feedback,
        ]);

        // Award gamification points if passed
        if ($attempt->isPassed()) {
            $this->gamification->awardPoints(
                $attempt->user,
                20,
                'quiz_complete',
                "Passed quiz: {$attempt->quiz->title} ({$percentage}%)"
            );
            $this->gamification->checkAndAwardBadges($attempt->user);
        }
    }
}
