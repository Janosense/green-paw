<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Auth;

class GamificationController extends Controller
{
    public function leaderboard(GamificationService $gamification)
    {
        $leaders = $gamification->getLeaderboard(20);
        $user = Auth::user();
        $myPoints = $user->totalPoints();
        $myStreak = $user->currentStreak();

        return view('gamification.leaderboard', compact('leaders', 'myPoints', 'myStreak'));
    }

    public function badges()
    {
        $user = Auth::user();
        $allBadges = Badge::all();
        $earnedBadgeIds = $user->badges()->pluck('badges.id')->toArray();

        return view('gamification.badges', compact('allBadges', 'earnedBadgeIds', 'user'));
    }
}
