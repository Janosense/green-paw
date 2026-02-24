<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = ['course_id', 'user_id', 'title', 'body', 'published_at'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('course_id');
    }

    public function isGlobal(): bool
    {
        return $this->course_id === null;
    }

    public function isPublished(): bool
    {
        return $this->published_at && $this->published_at->lte(now());
    }
}
