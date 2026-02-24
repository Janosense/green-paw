<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LearningPath;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LearningPathController extends Controller
{
    public function index()
    {
        $paths = LearningPath::withCount('courses')->orderBy('sort_order')->get();
        return view('admin.learning-paths.index', compact('paths'));
    }

    public function create()
    {
        $courses = Course::published()->orderBy('title')->get();
        return view('admin.learning-paths.form', ['path' => null, 'courses' => $courses]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
            'is_published' => ['boolean'],
            'courses' => ['array'],
            'courses.*' => ['exists:courses,id'],
        ]);

        $path = LearningPath::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'is_published' => $request->boolean('is_published'),
        ]);

        if (!empty($validated['courses'])) {
            $syncData = [];
            foreach ($validated['courses'] as $i => $courseId) {
                $syncData[$courseId] = ['sort_order' => $i, 'is_required' => true];
            }
            $path->courses()->sync($syncData);
        }

        return redirect()->route('admin.learning-paths.index')
            ->with('success', 'Learning path created.');
    }

    public function edit(LearningPath $learningPath)
    {
        $learningPath->load('courses');
        $courses = Course::published()->orderBy('title')->get();
        return view('admin.learning-paths.form', ['path' => $learningPath, 'courses' => $courses]);
    }

    public function update(Request $request, LearningPath $learningPath)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
            'is_published' => ['boolean'],
            'courses' => ['array'],
            'courses.*' => ['exists:courses,id'],
        ]);

        $learningPath->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'is_published' => $request->boolean('is_published'),
        ]);

        $syncData = [];
        foreach (($validated['courses'] ?? []) as $i => $courseId) {
            $syncData[$courseId] = ['sort_order' => $i, 'is_required' => true];
        }
        $learningPath->courses()->sync($syncData);

        return redirect()->route('admin.learning-paths.index')
            ->with('success', 'Learning path updated.');
    }

    public function destroy(LearningPath $learningPath)
    {
        $learningPath->delete();
        return redirect()->route('admin.learning-paths.index')
            ->with('success', 'Learning path deleted.');
    }
}
