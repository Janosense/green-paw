<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Show enrolled courses dashboard.
     */
    public function myCourses()
    {
        $user = Auth::user();
        $activeEnrollments = $user->enrollments()
            ->active()
            ->with('course.instructor', 'course.lessons')
            ->latest('enrolled_at')
            ->get();

        $completedEnrollments = $user->enrollments()
            ->completed()
            ->with('course.instructor')
            ->latest('completed_at')
            ->get();

        $totalPoints = $user->totalPoints();
        $streak = $user->currentStreak();
        $badgeCount = $user->badges()->count();

        return view('learn.my-courses', compact(
            'activeEnrollments',
            'completedEnrollments',
            'totalPoints',
            'streak',
            'badgeCount'
        ));
    }

    /**
     * Enroll in a course.
     */
    public function enroll(Course $course)
    {
        $user = Auth::user();

        if ($user->isEnrolledIn($course)) {
            return back()->with('success', 'You are already enrolled in this course.');
        }

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
        ]);

        return redirect()->route('learn.my-courses')
            ->with('success', "Enrolled in \"{$course->title}\" successfully!");
    }

    /**
     * Unenroll from a course.
     */
    public function unenroll(Course $course)
    {
        Auth::user()->enrollments()
            ->where('course_id', $course->id)
            ->update(['status' => 'dropped']);

        return redirect()->route('learn.my-courses')
            ->with('success', "Unenrolled from \"{$course->title}\".");
    }
}
