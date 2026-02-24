<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonViewController extends Controller
{
    /**
     * Show the lesson viewer.
     */
    public function show(Course $course, Lesson $lesson)
    {
        $user = Auth::user();

        if (!$user->isEnrolledIn($course)) {
            if (!$lesson->is_free_preview) {
                return redirect()->route('catalog.show', $course)
                    ->with('success', 'Please enroll in this course to access lessons.');
            }
        }

        $course->load(['lessons' => fn($q) => $q->ordered()]);
        $isCompleted = $user->hasCompletedLesson($lesson);

        // Find prev/next lessons
        $lessons = $course->lessons;
        $currentIndex = $lessons->search(fn($l) => $l->id === $lesson->id);
        $prevLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;

        $enrollment = $user->enrollmentFor($course);

        return view('learn.lesson', compact(
            'course',
            'lesson',
            'lessons',
            'isCompleted',
            'prevLesson',
            'nextLesson',
            'enrollment'
        ));
    }

    /**
     * Mark a lesson as complete.
     */
    public function complete(Request $request, Lesson $lesson, GamificationService $gamification)
    {
        $user = Auth::user();

        if ($user->hasCompletedLesson($lesson)) {
            return back()->with('success', 'Lesson already completed.');
        }

        LessonCompletion::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'time_spent_seconds' => $request->input('time_spent', 0),
            'completed_at' => now(),
        ]);

        $results = $gamification->onLessonCompleted($user, $lesson);

        $message = "Lesson completed! +{$results['points_earned']} points";

        if ($results['course_completed']) {
            $message .= ' 🎉 Course completed!';
        }

        foreach ($results['badges_earned'] as $badge) {
            $message .= " 🏆 Badge earned: {$badge->name}!";
        }

        return back()->with('success', $message);
    }
}
