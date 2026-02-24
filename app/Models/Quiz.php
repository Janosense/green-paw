<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'course_id',
        'lesson_id',
        'title',
        'description',
        'time_limit_minutes',
        'passing_score',
        'max_attempts',
        'shuffle_questions',
        'shuffle_answers',
        'show_results_after',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'time_limit_minutes' => 'integer',
            'passing_score' => 'integer',
            'max_attempts' => 'integer',
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            'show_results_after' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function hasTimeLimit(): bool
    {
        return $this->time_limit_minutes !== null && $this->time_limit_minutes > 0;
    }

    public function totalPoints(): int
    {
        return $this->questions()->sum('points');
    }

    public function attemptsFor(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return $this->attempts()->where('user_id', $user->id)->orderByDesc('created_at')->get();
    }

    public function remainingAttempts(User $user): int
    {
        $used = $this->attempts()->where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'graded'])->count();
        return max(0, $this->max_attempts - $used);
    }

    public function hasEssayQuestions(): bool
    {
        return $this->questions()->where('type', 'essay')->exists();
    }
}
