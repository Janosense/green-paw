<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    protected $fillable = [
        'quiz_id',
        'user_id',
        'answers',
        'score',
        'total_points',
        'percentage',
        'status',
        'started_at',
        'submitted_at',
        'time_spent_seconds',
        'instructor_feedback',
        'tab_switches',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'score' => 'integer',
            'total_points' => 'integer',
            'percentage' => 'integer',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'time_spent_seconds' => 'integer',
            'tab_switches' => 'integer',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPassed(): bool
    {
        return $this->percentage >= $this->quiz->passing_score;
    }

    public function isTimedOut(): bool
    {
        if (!$this->quiz->hasTimeLimit())
            return false;
        $elapsed = now()->diffInSeconds($this->started_at);
        return $elapsed >= ($this->quiz->time_limit_minutes * 60);
    }

    public function remainingSeconds(): ?int
    {
        if (!$this->quiz->hasTimeLimit())
            return null;
        $total = $this->quiz->time_limit_minutes * 60;
        $elapsed = now()->diffInSeconds($this->started_at);
        return max(0, $total - $elapsed);
    }

    /**
     * Auto-grade all auto-gradable questions in this attempt.
     */
    public function autoGrade(): void
    {
        $answers = $this->answers ?? [];
        $score = 0;
        $totalPoints = 0;

        foreach ($this->quiz->questions as $question) {
            $totalPoints += $question->points;
            $userAnswer = $answers[$question->id] ?? null;

            if ($question->isAutoGradable()) {
                $score += $question->grade($userAnswer);
            }
        }

        $this->update([
            'score' => $score,
            'total_points' => $totalPoints,
            'percentage' => $totalPoints > 0 ? (int) round(($score / $totalPoints) * 100) : 0,
            'status' => $this->quiz->hasEssayQuestions() ? 'submitted' : 'graded',
        ]);
    }

    public function scopeNeedsGrading($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeGraded($query)
    {
        return $query->where('status', 'graded');
    }
}
