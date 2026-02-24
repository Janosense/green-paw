<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\User;
use App\Models\UserPoint;

class GamificationService
{
    // Point values
    const POINTS_LESSON_COMPLETE = 10;
    const POINTS_COURSE_COMPLETE = 50;
    const POINTS_STREAK_BONUS = 5;

    /**
     * Handle lesson completion: award points, check badges, update enrollment progress.
     */
    public function onLessonCompleted(User $user, Lesson $lesson): array
    {
        $results = ['points_earned' => 0, 'badges_earned' => [], 'course_completed' => false];

        // Award lesson completion points
        $results['points_earned'] += $this->awardPoints(
            $user,
            self::POINTS_LESSON_COMPLETE,
            'lesson_complete',
            "Completed: {$lesson->title}"
        );

        // Check for streak bonus
        $streak = $user->currentStreak();
        if ($streak > 1 && $streak % 3 === 0) {
            $results['points_earned'] += $this->awardPoints(
                $user,
                self::POINTS_STREAK_BONUS * $streak,
                'streak',
                "{$streak}-day learning streak!"
            );
        }

        // Update enrollment progress
        $enrollment = $user->enrollmentFor($lesson->course);
        if ($enrollment) {
            $enrollment->updateProgress();
            $enrollment->refresh();

            if ($enrollment->isCompleted()) {
                $results['course_completed'] = true;
                $results['points_earned'] += $this->awardPoints(
                    $user,
                    self::POINTS_COURSE_COMPLETE,
                    'course_complete',
                    "Completed course: {$lesson->course->title}"
                );
            }
        }

        // Check for new badges
        $results['badges_earned'] = $this->checkAndAwardBadges($user);

        return $results;
    }

    /**
     * Award points to a user.
     */
    public function awardPoints(User $user, int $points, string $source, string $description = ''): int
    {
        UserPoint::create([
            'user_id' => $user->id,
            'points' => $points,
            'source' => $source,
            'description' => $description,
        ]);

        return $points;
    }

    /**
     * Check all badges and award any newly eligible ones.
     */
    public function checkAndAwardBadges(User $user): array
    {
        $earned = [];

        foreach (Badge::all() as $badge) {
            if ($badge->checkEligibility($user)) {
                $user->badges()->attach($badge->id, ['earned_at' => now()]);
                $earned[] = $badge;

                // Bonus points for earning a badge
                $this->awardPoints($user, 25, 'badge', "Earned badge: {$badge->name}");
            }
        }

        return $earned;
    }

    /**
     * Get leaderboard data.
     */
    public function getLeaderboard(int $limit = 20): \Illuminate\Support\Collection
    {
        return User::select('users.id', 'users.name', 'users.avatar')
            ->selectRaw('COALESCE(SUM(user_points.points), 0) as total_points')
            ->leftJoin('user_points', 'users.id', '=', 'user_points.user_id')
            ->groupBy('users.id', 'users.name', 'users.avatar')
            ->orderByDesc('total_points')
            ->limit($limit)
            ->get();
    }
}
