<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'criteria_type',
        'criteria_value',
    ];

    protected function casts(): array
    {
        return ['criteria_value' => 'integer'];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')->withPivot('earned_at');
    }

    /**
     * Check if a user is eligible for this badge.
     */
    public function checkEligibility(User $user): bool
    {
        // Already earned
        if ($user->badges()->where('badge_id', $this->id)->exists()) {
            return false;
        }

        return match ($this->criteria_type) {
            'lessons_completed' => $user->completedLessons()->count() >= $this->criteria_value,
            'courses_completed' => $user->enrollments()->completed()->count() >= $this->criteria_value,
            'points_earned' => $user->totalPoints() >= $this->criteria_value,
            'streak_days' => $user->currentStreak() >= $this->criteria_value,
            default => false,
        };
    }
}
