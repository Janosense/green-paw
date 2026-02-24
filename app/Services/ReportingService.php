<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserPoint;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    /**
     * Platform-wide KPIs.
     */
    public function platformOverview(): array
    {
        return [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'published_courses' => Course::published()->count(),
            'total_enrollments' => Enrollment::count(),
            'active_enrollments' => Enrollment::active()->count(),
            'completed_enrollments' => Enrollment::completed()->count(),
            'completion_rate' => $this->completionRate(),
            'avg_quiz_score' => (int) round(QuizAttempt::graded()->avg('percentage') ?? 0),
            'total_lessons_completed' => LessonCompletion::count(),
            'total_points_awarded' => (int) UserPoint::sum('points'),
        ];
    }

    /**
     * 30-day engagement trend data for charts.
     */
    public function engagementTrend(int $days = 30): array
    {
        $start = Carbon::now()->subDays($days)->startOfDay();
        $labels = [];
        $enrollments = [];
        $completions = [];
        $quizAttempts = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('M d');

            $enrollments[] = Enrollment::whereDate('enrolled_at', $dateStr)->count();
            $completions[] = LessonCompletion::whereDate('completed_at', $dateStr)->count();
            $quizAttempts[] = QuizAttempt::whereDate('submitted_at', $dateStr)->count();
        }

        return compact('labels', 'enrollments', 'completions', 'quizAttempts');
    }

    /**
     * Top courses by enrollment.
     */
    public function topCourses(int $limit = 10): Collection
    {
        return Course::withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Per-course analytics.
     */
    public function courseAnalytics(Course $course): array
    {
        $enrollments = Enrollment::where('course_id', $course->id);
        $totalEnrolled = $enrollments->count();
        $completed = (clone $enrollments)->completed()->count();
        $avgProgress = (int) round((clone $enrollments)->avg('progress_percent') ?? 0);

        // Lesson engagement
        $lessons = $course->lessons()->ordered()->get()->map(function ($lesson) {
            $completions = LessonCompletion::where('lesson_id', $lesson->id)->count();
            $avgTime = (int) round(LessonCompletion::where('lesson_id', $lesson->id)->avg('time_spent_seconds') ?? 0);
            return (object) [
                'title' => $lesson->title,
                'completions' => $completions,
                'avg_time_seconds' => $avgTime,
            ];
        });

        // Quiz performance
        $quizzes = $course->quizzes->map(function ($quiz) {
            $attempts = QuizAttempt::where('quiz_id', $quiz->id)->whereIn('status', ['submitted', 'graded']);
            return (object) [
                'title' => $quiz->title,
                'attempt_count' => (clone $attempts)->count(),
                'avg_score' => (int) round((clone $attempts)->avg('percentage') ?? 0),
                'pass_rate' => $this->quizPassRate($quiz),
            ];
        });

        return [
            'total_enrolled' => $totalEnrolled,
            'completed' => $completed,
            'completion_rate' => $totalEnrolled > 0 ? (int) round(($completed / $totalEnrolled) * 100) : 0,
            'avg_progress' => $avgProgress,
            'lessons' => $lessons,
            'quizzes' => $quizzes,
        ];
    }

    /**
     * Grade book: students × quizzes matrix.
     */
    public function gradeBook(Course $course): array
    {
        $quizzes = $course->quizzes;
        $students = User::whereHas('enrollments', fn($q) => $q->where('course_id', $course->id))->get();

        $grades = [];
        foreach ($students as $student) {
            $row = ['student' => $student, 'scores' => []];
            foreach ($quizzes as $quiz) {
                $best = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('user_id', $student->id)
                    ->whereIn('status', ['submitted', 'graded'])
                    ->orderByDesc('percentage')
                    ->first();
                $row['scores'][$quiz->id] = $best;
            }
            $grades[] = $row;
        }

        return compact('quizzes', 'students', 'grades');
    }

    /**
     * Student progress data.
     */
    public function studentProgress(User $user): array
    {
        $enrollments = $user->enrollments()->with('course.quizzes')->get();
        $recentQuizzes = QuizAttempt::where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->with('quiz')
            ->orderByDesc('submitted_at')
            ->limit(10)
            ->get();
        $pointsTimeline = UserPoint::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return [
            'enrollments' => $enrollments,
            'courses_enrolled' => $enrollments->count(),
            'courses_completed' => $enrollments->where('status', 'completed')->count(),
            'total_points' => $user->totalPoints(),
            'streak' => $user->currentStreak(),
            'badges_count' => $user->badges()->count(),
            'lessons_completed' => $user->completedLessons()->count(),
            'recent_quizzes' => $recentQuizzes,
            'points_timeline' => $pointsTimeline,
        ];
    }

    /**
     * Export data as CSV string.
     */
    public function exportCsv(string $type, ?int $courseId = null): string
    {
        return match ($type) {
            'enrollments' => $this->exportEnrollments($courseId),
            'grades' => $this->exportGrades($courseId),
            'progress' => $this->exportProgress($courseId),
            default => '',
        };
    }

    // --- Private helpers ---

    private function completionRate(): int
    {
        $total = Enrollment::count();
        if ($total === 0)
            return 0;
        return (int) round((Enrollment::completed()->count() / $total) * 100);
    }

    private function quizPassRate($quiz): int
    {
        $graded = QuizAttempt::where('quiz_id', $quiz->id)->whereIn('status', ['submitted', 'graded']);
        $total = (clone $graded)->count();
        if ($total === 0)
            return 0;
        $passed = (clone $graded)->where('percentage', '>=', $quiz->passing_score)->count();
        return (int) round(($passed / $total) * 100);
    }

    private function exportEnrollments(?int $courseId): string
    {
        $query = Enrollment::with('user', 'course');
        if ($courseId)
            $query->where('course_id', $courseId);

        $rows = [['Student', 'Email', 'Course', 'Status', 'Progress %', 'Enrolled At', 'Completed At']];
        foreach ($query->get() as $e) {
            $rows[] = [$e->user->name, $e->user->email, $e->course->title, $e->status, $e->progress_percent, $e->enrolled_at, $e->completed_at];
        }
        return $this->arrayToCsv($rows);
    }

    private function exportGrades(?int $courseId): string
    {
        $query = QuizAttempt::with('user', 'quiz.course')->whereIn('status', ['submitted', 'graded']);
        if ($courseId)
            $query->whereHas('quiz', fn($q) => $q->where('course_id', $courseId));

        $rows = [['Student', 'Email', 'Course', 'Quiz', 'Score', 'Total', 'Percentage', 'Status', 'Submitted']];
        foreach ($query->get() as $a) {
            $rows[] = [$a->user->name, $a->user->email, $a->quiz->course->title, $a->quiz->title, $a->score, $a->total_points, $a->percentage . '%', $a->status, $a->submitted_at];
        }
        return $this->arrayToCsv($rows);
    }

    private function exportProgress(?int $courseId): string
    {
        $query = Enrollment::with('user', 'course');
        if ($courseId)
            $query->where('course_id', $courseId);

        $rows = [['Student', 'Email', 'Course', 'Progress %', 'Lessons Completed', 'Total Lessons', 'Status']];
        foreach ($query->get() as $e) {
            $totalLessons = $e->course->lessons()->count();
            $completedLessons = LessonCompletion::where('user_id', $e->user_id)
                ->whereIn('lesson_id', $e->course->lessons()->pluck('id'))
                ->count();
            $rows[] = [$e->user->name, $e->user->email, $e->course->title, $e->progress_percent, $completedLessons, $totalLessons, $e->status];
        }
        return $this->arrayToCsv($rows);
    }

    private function arrayToCsv(array $rows): string
    {
        $output = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        return $csv;
    }
}
