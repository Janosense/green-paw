<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discussion extends Model
{
    protected $fillable = ['course_id', 'user_id', 'title', 'body', 'is_pinned', 'is_locked', 'replies_count'];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(DiscussionReply::class)->whereNull('parent_id')->orderBy('created_at');
    }

    public function allReplies(): HasMany
    {
        return $this->hasMany(DiscussionReply::class)->orderBy('created_at');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeNotLocked($query)
    {
        return $query->where('is_locked', false);
    }
}
