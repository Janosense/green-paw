<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveSession extends Model
{
    protected $fillable = ['course_id', 'title', 'description', 'platform', 'meeting_url', 'starts_at', 'duration_minutes', 'created_by'];

    protected $casts = [
        'starts_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>', now())->orderBy('starts_at');
    }

    public function scopePast($query)
    {
        return $query->where('starts_at', '<=', now())->orderByDesc('starts_at');
    }

    public function endsAt()
    {
        return $this->starts_at->addMinutes($this->duration_minutes);
    }

    public function isLive(): bool
    {
        return now()->between($this->starts_at, $this->endsAt());
    }

    public function platformIcon(): string
    {
        return match ($this->platform) {
            'zoom' => '📹',
            'google_meet' => '🎥',
            'teams' => '💬',
            default => '📺',
        };
    }
}
