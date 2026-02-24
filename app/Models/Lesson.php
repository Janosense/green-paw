<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'description',
        'content_type',
        'content',
        'media_url',
        'sort_order',
        'duration_minutes',
        'is_free_preview',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_free_preview' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
            'duration_minutes' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function contentTypeLabel(): string
    {
        return match ($this->content_type) {
            'video' => '🎬 Video',
            'audio' => '🎧 Audio',
            'pdf' => '📄 PDF',
            'html' => '🌐 HTML',
            'text' => '📝 Text',
            default => $this->content_type,
        };
    }

    public function contentTypeIcon(): string
    {
        return match ($this->content_type) {
            'video' => '🎬',
            'audio' => '🎧',
            'pdf' => '📄',
            'html' => '🌐',
            'text' => '📝',
            default => '📎',
        };
    }

    /**
     * Auto-generate slug on create.
     */
    protected static function booted(): void
    {
        static::creating(function (Lesson $lesson) {
            if (empty($lesson->slug)) {
                $lesson->slug = Str::slug($lesson->title);
            }
        });
    }
}
