@extends('layouts.app')

@section('title', 'Leaderboard — Green Paw LMS')
@section('page_title', 'Leaderboard')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 320px; gap: 24px; align-items: start;">
        <!-- Main leaderboard -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">🏅 Top Learners</h2>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">Rank</th>
                            <th>Learner</th>
                            <th style="text-align: right;">Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaders as $i => $leader)
                            <tr style="{{ auth()->id() === $leader->id ? 'background: rgba(52, 211, 153, 0.05);' : '' }}">
                                <td>
                                    @if($i === 0)
                                        <span style="font-size: 20px;">🥇</span>
                                    @elseif($i === 1)
                                        <span style="font-size: 20px;">🥈</span>
                                    @elseif($i === 2)
                                        <span style="font-size: 20px;">🥉</span>
                                    @else
                                        <span
                                            style="font-size: 14px; font-weight: 600; color: var(--text-muted); padding-left: 4px;">#{{ $i + 1 }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div
                                            style="width: 32px; height: 32px; border-radius: 50%; background: var(--bg-input); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; color: var(--accent);">
                                            {{ strtoupper(substr($leader->name, 0, 1)) }}
                                        </div>
                                        <span style="font-weight: 600;">
                                            {{ $leader->name }}
                                            @if(auth()->id() === $leader->id) <span
                                            style="font-size: 11px; color: var(--accent);">(you)</span> @endif
                                        </span>
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    <span
                                        style="font-weight: 700; font-family: 'JetBrains Mono', monospace; color: var(--accent);">{{ number_format($leader->total_points) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Your stats -->
        <div>
            <div class="card" style="margin-bottom: 16px;">
                <div class="card-header">
                    <h3 class="card-title">Your Stats</h3>
                </div>
                <div class="card-body">
                    <div style="text-align: center; margin-bottom: 16px;">
                        <div
                            style="font-size: 36px; font-weight: 800; color: var(--accent); font-family: 'JetBrains Mono', monospace;">
                            {{ number_format($myPoints) }}</div>
                        <div style="font-size: 13px; color: var(--text-muted);">Total Points</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 24px; font-weight: 700;">🔥 {{ $myStreak }}</div>
                        <div style="font-size: 13px; color: var(--text-muted);">Day Streak</div>
                    </div>
                </div>
            </div>
            <a href="{{ route('gamification.badges') }}" class="btn btn-secondary" style="width: 100%;">🏆 View All
                Badges</a>
        </div>
    </div>
@endsection