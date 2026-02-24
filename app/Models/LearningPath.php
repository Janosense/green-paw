<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class LearningPath extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'thumbnail',
        'level',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'learning_path_courses')
            ->withPivot('sort_order', 'is_required')
            ->orderByPivot('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Calculate progress for a specific user.
     */
    public function progressFor(User $user): array
    {
        $courses = $this->courses;
        $total = $courses->count();
        if ($total === 0)
            return ['percent' => 0, 'completed' => 0, 'total' => 0];

        $completed = 0;
        foreach ($courses as $course) {
            $enrollment = $user->enrollments()->where('course_id', $course->id)->first();
            if ($enrollment && $enrollment->isCompleted()) {
                $completed++;
            }
        }

        return [
            'percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
            'completed' => $completed,
            'total' => $total,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (LearningPath $path) {
            if (empty($path->slug)) {
                $path->slug = Str::slug($path->title);
            }
        });
    }
}
