<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'avatar',
        'bio',
        'preferences',
        'provider',
        'provider_id',
        'timezone',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Learning Relationships
    |--------------------------------------------------------------------------
    */

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function completedLessons(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }

    public function points(): HasMany
    {
        return $this->hasMany(UserPoint::class);
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')->withPivot('earned_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Gamification Helpers
    |--------------------------------------------------------------------------
    */

    public function totalPoints(): int
    {
        return (int) $this->points()->sum('points');
    }

    public function currentStreak(): int
    {
        $dates = $this->completedLessons()
            ->selectRaw('DATE(completed_at) as day')
            ->groupBy('day')
            ->orderByDesc('day')
            ->pluck('day');

        if ($dates->isEmpty())
            return 0;

        $streak = 0;
        $expected = now()->startOfDay();

        foreach ($dates as $date) {
            $d = \Carbon\Carbon::parse($date)->startOfDay();
            if ($d->eq($expected) || $d->eq($expected->copy()->subDay())) {
                $streak++;
                $expected = $d;
            } else {
                break;
            }
        }

        return $streak;
    }

    public function isEnrolledIn(Course $course): bool
    {
        return $this->enrollments()->where('course_id', $course->id)->where('status', '!=', 'dropped')->exists();
    }

    public function enrollmentFor(Course $course): ?Enrollment
    {
        return $this->enrollments()->where('course_id', $course->id)->first();
    }

    public function hasCompletedLesson(Lesson $lesson): bool
    {
        return $this->completedLessons()->where('lesson_id', $lesson->id)->exists();
    }
}
