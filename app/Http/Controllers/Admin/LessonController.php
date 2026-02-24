<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Show the form for creating a new lesson.
     */
    public function create(Course $course)
    {
        $nextOrder = $course->lessons()->max('sort_order') + 1;

        return view('admin.lessons.create', compact('course', 'nextOrder'));
    }

    /**
     * Store a newly created lesson.
     */
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'content_type' => ['required', 'in:video,audio,pdf,html,text'],
            'content' => ['nullable', 'string'],
            'media_url' => ['nullable', 'string', 'max:2048'],
            'media_file' => ['nullable', 'file', 'max:102400'], // 100MB
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'is_free_preview' => ['boolean'],
            'is_published' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $mediaUrl = $validated['media_url'] ?? null;

        if ($request->hasFile('media_file')) {
            $mediaUrl = $request->file('media_file')
                ->store("courses/{$course->id}/lessons", 'public');
        }

        $course->lessons()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content_type' => $validated['content_type'],
            'content' => $validated['content'] ?? null,
            'media_url' => $mediaUrl,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'is_free_preview' => $request->boolean('is_free_preview'),
            'is_published' => $request->boolean('is_published', true),
            'sort_order' => $validated['sort_order'] ?? ($course->lessons()->max('sort_order') + 1),
        ]);

        return redirect()->route('admin.courses.edit', $course)
            ->with('success', 'Lesson added successfully.');
    }

    /**
     * Show the form for editing a lesson.
     */
    public function edit(Course $course, Lesson $lesson)
    {
        return view('admin.lessons.edit', compact('course', 'lesson'));
    }

    /**
     * Update the specified lesson.
     */
    public function update(Request $request, Course $course, Lesson $lesson)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'content_type' => ['required', 'in:video,audio,pdf,html,text'],
            'content' => ['nullable', 'string'],
            'media_url' => ['nullable', 'string', 'max:2048'],
            'media_file' => ['nullable', 'file', 'max:102400'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'is_free_preview' => ['boolean'],
            'is_published' => ['boolean'],
        ]);

        $mediaUrl = $validated['media_url'] ?? $lesson->media_url;

        if ($request->hasFile('media_file')) {
            $mediaUrl = $request->file('media_file')
                ->store("courses/{$course->id}/lessons", 'public');
        }

        $lesson->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content_type' => $validated['content_type'],
            'content' => $validated['content'] ?? null,
            'media_url' => $mediaUrl,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'is_free_preview' => $request->boolean('is_free_preview'),
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->route('admin.courses.edit', $course)
            ->with('success', 'Lesson updated successfully.');
    }

    /**
     * Remove the specified lesson.
     */
    public function destroy(Course $course, Lesson $lesson)
    {
        $lesson->delete();

        return redirect()->route('admin.courses.edit', $course)
            ->with('success', 'Lesson deleted.');
    }

    /**
     * Reorder lessons (AJAX).
     */
    public function reorder(Request $request, Course $course)
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:lessons,id'],
        ]);

        foreach ($validated['order'] as $index => $lessonId) {
            Lesson::where('id', $lessonId)
                ->where('course_id', $course->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
