<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'progress_percent',
        'enrolled_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
            'progress_percent' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Recalculate progress based on completed lessons.
     */
    public function updateProgress(): void
    {
        $totalLessons = $this->course->lessons()->published()->count();
        if ($totalLessons === 0) {
            $this->update(['progress_percent' => 0]);
            return;
        }

        $completedLessons = LessonCompletion::where('user_id', $this->user_id)
            ->whereIn('lesson_id', $this->course->lessons()->published()->pluck('id'))
            ->count();

        $percent = (int) round(($completedLessons / $totalLessons) * 100);

        $data = ['progress_percent' => $percent];

        if ($percent >= 100 && $this->status === 'active') {
            $data['status'] = 'completed';
            $data['completed_at'] = now();
        }

        $this->update($data);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
