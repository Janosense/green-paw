<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LiveSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiveSessionController extends Controller
{
    public function index(Course $course)
    {
        $upcoming = $course->liveSessions()->upcoming()->get();
        $past = $course->liveSessions()->past()->limit(20)->get();

        return view('admin.live-sessions.index', compact('course', 'upcoming', 'past'));
    }

    public function create(Course $course)
    {
        return view('admin.live-sessions.form', ['course' => $course, 'session' => null]);
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'platform' => 'required|in:zoom,google_meet,teams,other',
            'meeting_url' => 'nullable|url',
            'starts_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:5|max:480',
        ]);

        $course->liveSessions()->create(array_merge($data, [
            'created_by' => Auth::id(),
        ]));

        return redirect()->route('admin.courses.live-sessions.index', $course)
            ->with('success', 'Live session scheduled!');
    }

    public function edit(Course $course, LiveSession $liveSession)
    {
        return view('admin.live-sessions.form', ['course' => $course, 'session' => $liveSession]);
    }

    public function update(Request $request, Course $course, LiveSession $liveSession)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'platform' => 'required|in:zoom,google_meet,teams,other',
            'meeting_url' => 'nullable|url',
            'starts_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:5|max:480',
        ]);

        $liveSession->update($data);

        return redirect()->route('admin.courses.live-sessions.index', $course)
            ->with('success', 'Session updated!');
    }

    public function destroy(Course $course, LiveSession $liveSession)
    {
        $liveSession->delete();
        return redirect()->route('admin.courses.live-sessions.index', $course)
            ->with('success', 'Session deleted.');
    }
}
