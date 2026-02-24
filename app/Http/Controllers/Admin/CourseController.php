<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(Request $request)
    {
        $query = Course::with('instructor', 'categories', 'lessons');

        // Instructors see only their own courses, admins see all
        if (!Auth::user()->hasAnyRole(['super-admin', 'admin'])) {
            $query->where('instructor_id', Auth::id());
        }

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($level = $request->input('level')) {
            $query->byLevel($level);
        }

        $courses = $query->latest()->paginate(15);
        $categories = Category::roots()->get();

        return view('admin.courses.index', compact('courses', 'categories'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        $categories = Category::roots()->with('children')->get();
        $allCourses = Course::published()->orderBy('title')->get(['id', 'title']);

        return view('admin.courses.create', compact('categories', 'allCourses'));
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'categories' => ['array'],
            'categories.*' => ['exists:categories,id'],
            'prerequisites' => ['array'],
            'prerequisites.*' => ['exists:courses,id'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $course = Course::create([
            'instructor_id' => Auth::id(),
            'title' => $validated['title'],
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'price' => $validated['price'] ?? null,
        ]);

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('courses/thumbnails', 'public');
            $course->update(['thumbnail' => $path]);
        }

        if (!empty($validated['categories'])) {
            $course->categories()->sync($validated['categories']);
        }

        if (!empty($validated['prerequisites'])) {
            $course->prerequisites()->sync($validated['prerequisites']);
        }

        return redirect()->route('admin.courses.edit', $course)
            ->with('success', 'Course created. Now add some lessons!');
    }

    /**
     * Show the form for editing a course (also serves as the lesson manager).
     */
    public function edit(Course $course)
    {
        $course->load('lessons', 'categories', 'prerequisites');
        $categories = Category::roots()->with('children')->get();
        $allCourses = Course::published()
            ->where('id', '!=', $course->id)
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('admin.courses.edit', compact('course', 'categories', 'allCourses'));
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'categories' => ['array'],
            'categories.*' => ['exists:categories,id'],
            'prerequisites' => ['array'],
            'prerequisites.*' => ['exists:courses,id'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $course->update([
            'title' => $validated['title'],
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'],
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'price' => $validated['price'] ?? null,
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $path = $request->file('thumbnail')->store('courses/thumbnails', 'public');
            $course->update(['thumbnail' => $path]);
        }

        $course->categories()->sync($validated['categories'] ?? []);
        $course->prerequisites()->sync($validated['prerequisites'] ?? []);

        return redirect()->route('admin.courses.edit', $course)
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Course $course)
    {
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    /**
     * Publish a course.
     */
    public function publish(Course $course)
    {
        $course->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', 'Course published successfully.');
    }

    /**
     * Unpublish a course.
     */
    public function unpublish(Course $course)
    {
        $course->update([
            'status' => 'draft',
            'published_at' => null,
        ]);

        return back()->with('success', 'Course unpublished.');
    }

    /**
     * Duplicate a course.
     */
    public function duplicate(Course $course)
    {
        $clone = $course->duplicate();

        return redirect()->route('admin.courses.edit', $clone)
            ->with('success', 'Course duplicated. Edit the copy below.');
    }

    /**
     * Create a new version of a course.
     */
    public function newVersion(Course $course)
    {
        $newVersion = $course->createNewVersion();

        return redirect()->route('admin.courses.edit', $newVersion)
            ->with('success', "Version {$newVersion->version} created.");
    }
}
