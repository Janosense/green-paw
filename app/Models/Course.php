<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'instructor_id',
        'title',
        'slug',
        'description',
        'short_description',
        'thumbnail',
        'level',
        'status',
        'version',
        'parent_course_id',
        'settings',
        'duration_minutes',
        'price',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'price' => 'decimal:2',
            'published_at' => 'datetime',
            'duration_minutes' => 'integer',
            'version' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }

    public function liveSessions(): HasMany
    {
        return $this->hasMany(LiveSession::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'course_category');
    }

    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'course_id', 'prerequisite_id')
            ->withTimestamps();
    }

    public function dependentCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'prerequisite_id', 'course_id')
            ->withTimestamps();
    }

    public function parentCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'parent_course_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(Course::class, 'parent_course_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeByLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search)
            return $query;

        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('short_description', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Methods
    |--------------------------------------------------------------------------
    */

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function totalDuration(): int
    {
        return $this->lessons()->sum('duration_minutes') ?: ($this->duration_minutes ?? 0);
    }

    public function lessonCount(): int
    {
        return $this->lessons()->count();
    }

    /**
     * Duplicate this course with all its lessons.
     */
    public function duplicate(): static
    {
        $clone = $this->replicate(['slug', 'status', 'published_at']);
        $clone->title = $this->title . ' (Copy)';
        $clone->slug = Str::slug($clone->title) . '-' . Str::random(5);
        $clone->status = 'draft';
        $clone->published_at = null;
        $clone->save();

        // Clone lessons
        foreach ($this->lessons as $lesson) {
            $lessonClone = $lesson->replicate();
            $lessonClone->course_id = $clone->id;
            $lessonClone->save();
        }

        // Clone categories
        $clone->categories()->sync($this->categories->pluck('id'));

        // Clone prerequisites
        $clone->prerequisites()->sync($this->prerequisites->pluck('id'));

        return $clone;
    }

    /**
     * Create a new version of this course.
     */
    public function createNewVersion(): static
    {
        $parentId = $this->parent_course_id ?? $this->id;

        $newVersion = $this->duplicate();
        $newVersion->title = str_replace(' (Copy)', '', $newVersion->title);
        $newVersion->slug = Str::slug($newVersion->title) . '-v' . ($this->version + 1);
        $newVersion->version = $this->version + 1;
        $newVersion->parent_course_id = $parentId;
        $newVersion->save();

        return $newVersion;
    }

    /**
     * Auto-generate slug on create.
     */
    protected static function booted(): void
    {
        static::creating(function (Course $course) {
            if (empty($course->slug)) {
                $baseSlug = Str::slug($course->title);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $course->slug = $slug;
            }
        });
    }
}
