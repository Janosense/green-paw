<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgesSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['name' => 'First Steps', 'slug' => 'first-steps', 'icon' => '🌱', 'description' => 'Complete your first lesson', 'criteria_type' => 'lessons_completed', 'criteria_value' => 1],
            ['name' => 'Knowledge Seeker', 'slug' => 'knowledge-seeker', 'icon' => '📖', 'description' => 'Complete 10 lessons', 'criteria_type' => 'lessons_completed', 'criteria_value' => 10],
            ['name' => 'Scholar', 'slug' => 'scholar', 'icon' => '🎓', 'description' => 'Complete 50 lessons', 'criteria_type' => 'lessons_completed', 'criteria_value' => 50],
            ['name' => 'Graduate', 'slug' => 'graduate', 'icon' => '🎯', 'description' => 'Complete your first course', 'criteria_type' => 'courses_completed', 'criteria_value' => 1],
            ['name' => 'Overachiever', 'slug' => 'overachiever', 'icon' => '🏆', 'description' => 'Complete 5 courses', 'criteria_type' => 'courses_completed', 'criteria_value' => 5],
            ['name' => 'Point Collector', 'slug' => 'point-collector', 'icon' => '💎', 'description' => 'Earn 100 points', 'criteria_type' => 'points_earned', 'criteria_value' => 100],
            ['name' => 'Point Master', 'slug' => 'point-master', 'icon' => '⚡', 'description' => 'Earn 500 points', 'criteria_type' => 'points_earned', 'criteria_value' => 500],
            ['name' => 'Dedicated Learner', 'slug' => 'dedicated-learner', 'icon' => '🔥', 'description' => '3-day learning streak', 'criteria_type' => 'streak_days', 'criteria_value' => 3],
            ['name' => 'Unstoppable', 'slug' => 'unstoppable', 'icon' => '💪', 'description' => '7-day learning streak', 'criteria_type' => 'streak_days', 'criteria_value' => 7],
            ['name' => 'Legend', 'slug' => 'legend', 'icon' => '👑', 'description' => '30-day learning streak', 'criteria_type' => 'streak_days', 'criteria_value' => 30],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(['slug' => $badge['slug']], $badge);
        }
    }
}
