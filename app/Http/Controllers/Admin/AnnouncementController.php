<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('author', 'course')->latest()->paginate(20);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $courses = Course::orderBy('title')->get();
        return view('admin.announcements.form', ['announcement' => null, 'courses' => $courses]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
            'publish_now' => 'nullable|boolean',
        ]);

        Announcement::create([
            'title' => $data['title'],
            'body' => $data['body'],
            'course_id' => $data['course_id'] ?? null,
            'user_id' => Auth::id(),
            'published_at' => $request->has('publish_now') ? now() : null,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created!');
    }

    public function edit(Announcement $announcement)
    {
        $courses = Course::orderBy('title')->get();
        return view('admin.announcements.form', compact('announcement', 'courses'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
            'publish_now' => 'nullable|boolean',
        ]);

        $announcement->update([
            'title' => $data['title'],
            'body' => $data['body'],
            'course_id' => $data['course_id'] ?? null,
            'published_at' => $request->has('publish_now') ? ($announcement->published_at ?? now()) : null,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated!');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted.');
    }

    /**
     * Student-facing announcement feed.
     */
    public function feed()
    {
        $user = Auth::user();
        $enrolledCourseIds = $user->enrollments()->pluck('course_id');

        $announcements = Announcement::published()
            ->where(function ($q) use ($enrolledCourseIds) {
                $q->whereNull('course_id') // Global
                    ->orWhereIn('course_id', $enrolledCourseIds);
            })
            ->with('author', 'course')
            ->latest('published_at')
            ->paginate(20);

        return view('announcements.feed', compact('announcements'));
    }
}
