<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Question extends Model
{
    protected $fillable = [
        'quiz_id',
        'type',
        'body',
        'options',
        'correct_answer',
        'points',
        'explanation',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'correct_answer' => 'array',
            'points' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Whether this question can be automatically graded.
     */
    public function isAutoGradable(): bool
    {
        return $this->type !== 'essay';
    }

    /**
     * Grade a user's answer. Returns points earned.
     */
    public function grade(mixed $answer): int
    {
        if (!$this->isAutoGradable() || $answer === null) {
            return 0;
        }

        $correct = $this->correct_answer;

        return match ($this->type) {
            'mcq' => $this->gradeMcq($answer, $correct),
            'true_false' => $this->gradeTrueFalse($answer, $correct),
            'fill_blank' => $this->gradeFillBlank($answer, $correct),
            default => 0,
        };
    }

    private function gradeMcq(mixed $answer, mixed $correct): int
    {
        $correctValue = is_array($correct) ? ($correct[0] ?? '') : $correct;
        return (string) $answer === (string) $correctValue ? $this->points : 0;
    }

    private function gradeTrueFalse(mixed $answer, mixed $correct): int
    {
        $correctValue = is_array($correct) ? ($correct[0] ?? '') : $correct;
        return Str::lower((string) $answer) === Str::lower((string) $correctValue) ? $this->points : 0;
    }

    private function gradeFillBlank(mixed $answer, mixed $correct): int
    {
        // Accept any of the correct answers (case-insensitive, trimmed)
        $acceptedAnswers = is_array($correct) ? $correct : [$correct];
        $userAnswer = Str::lower(trim((string) $answer));

        foreach ($acceptedAnswers as $accepted) {
            if (Str::lower(trim((string) $accepted)) === $userAnswer) {
                return $this->points;
            }
        }

        return 0;
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'mcq' => 'Multiple Choice',
            'true_false' => 'True / False',
            'fill_blank' => 'Fill in the Blank',
            'essay' => 'Essay',
            default => ucfirst($this->type),
        };
    }
}
