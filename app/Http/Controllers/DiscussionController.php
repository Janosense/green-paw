<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Discussion;
use App\Models\DiscussionReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function index(Course $course)
    {
        $pinned = $course->discussions()->pinned()->with('user')->latest()->get();
        $discussions = $course->discussions()->where('is_pinned', false)->with('user')->latest()->paginate(20);

        return view('discussions.index', compact('course', 'pinned', 'discussions'));
    }

    public function show(Course $course, Discussion $discussion)
    {
        $discussion->load(['user', 'replies.user', 'replies.children.user']);

        return view('discussions.show', compact('course', 'discussion'));
    }

    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $course->discussions()->create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return redirect()->route('discussions.index', $course)
            ->with('success', 'Discussion created!');
    }

    public function reply(Request $request, Discussion $discussion)
    {
        if ($discussion->is_locked) {
            return back()->with('error', 'This discussion is locked.');
        }

        $request->validate(['body' => 'required|string']);

        $discussion->allReplies()->create([
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'body' => $request->body,
        ]);

        $discussion->increment('replies_count');

        return back()->with('success', 'Reply posted!');
    }

    public function togglePin(Discussion $discussion)
    {
        $discussion->update(['is_pinned' => !$discussion->is_pinned]);
        return back()->with('success', $discussion->is_pinned ? 'Pinned!' : 'Unpinned.');
    }

    public function toggleLock(Discussion $discussion)
    {
        $discussion->update(['is_locked' => !$discussion->is_locked]);
        return back()->with('success', $discussion->is_locked ? 'Locked.' : 'Unlocked.');
    }
}
