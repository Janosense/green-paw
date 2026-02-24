@extends('layouts.app')

@section('title', 'My Progress — Green Paw LMS')
@section('page_title', 'My Progress')

@section('content')
    <!-- Personal Stats -->
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 24px;">
        @php
            $stats = [
                ['label' => 'Courses Enrolled', 'value' => $courses_enrolled, 'icon' => '📚', 'color' => '#60a5fa'],
                ['label' => 'Completed', 'value' => $courses_completed, 'icon' => '✅', 'color' => '#34d399'],
                ['label' => 'Lessons Done', 'value' => $lessons_completed, 'icon' => '📖', 'color' => '#a78bfa'],
                ['label' => 'Total Points', 'value' => number_format($total_points), 'icon' => '⭐', 'color' => '#fbbf24'],
                ['label' => 'Learning Streak', 'value' => $streak . 'd', 'icon' => '🔥', 'color' => '#fb923c'],
                ['label' => 'Badges Earned', 'value' => $badges_count, 'icon' => '🏆', 'color' => '#f472b6'],
            ];
        @endphp

        @foreach($stats as $s)
            <div class="card">
                <div class="card-body" style="padding: 14px; text-align: center;">
                    <div style="font-size: 24px; margin-bottom: 2px;">{{ $s['icon'] }}</div>
                    <div
                        style="font-size: 22px; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: {{ $s['color'] }};">
                        {{ $s['value'] }}</div>
                    <div style="font-size: 11px; color: var(--text-muted);">{{ $s['label'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid-2" style="grid-template-columns: 1fr 360px; align-items: start;">
        <!-- Course Progress -->
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <h3 class="card-title">Course Progress</h3>
            </div>
            @forelse($enrollments as $enrollment)
                <div style="padding: 14px 16px; border-bottom: 1px solid var(--border);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <span style="font-weight: 600; font-size: 14px;">{{ $enrollment->course->title }}</span>
                        <span
                            style="font-size: 13px; font-weight: 700; color: {{ $enrollment->isCompleted() ? 'var(--accent)' : 'var(--text-muted)' }}; font-family: 'JetBrains Mono', monospace;">
                            {{ $enrollment->progress_percent }}%
                        </span>
                    </div>
                    <div style="height: 6px; background: var(--bg-input); border-radius: 3px; overflow: hidden;">
                        <div
                            style="height: 100%; width: {{ $enrollment->progress_percent }}%; background: {{ $enrollment->isCompleted() ? '#34d399' : '#60a5fa' }}; border-radius: 3px; transition: width 0.5s;">
                        </div>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; margin-top: 4px; font-size: 11px; color: var(--text-muted);">
                        <span>{{ ucfirst($enrollment->status) }}</span>
                        @if($enrollment->isCompleted())
                            <span>Completed {{ $enrollment->completed_at->diffForHumans() }}</span>
                        @else
                            <span>Enrolled {{ $enrollment->enrolled_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card-body" style="text-align: center; padding: 40px; color: var(--text-muted);">
                    Not enrolled in any courses yet.
                </div>
            @endforelse
        </div>

        <!-- Right column -->
        <div>
            <!-- Recent Quiz Results -->
            <div class="card" style="margin-bottom: 16px;">
                <div class="card-header">
                    <h3 class="card-title">Recent Quizzes</h3>
                </div>
                @forelse($recent_quizzes as $attempt)
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; padding: 10px 16px; border-bottom: 1px solid var(--border);">
                        <div>
                            <div style="font-size: 13px; font-weight: 600;">{{ $attempt->quiz->title }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">
                                {{ $attempt->submitted_at?->diffForHumans() }}</div>
                        </div>
                        @php $passed = $attempt->isPassed(); @endphp
                        <span
                            style="font-size: 13px; font-weight: 700; font-family: 'JetBrains Mono', monospace; color: {{ $passed ? '#34d399' : '#ef4444' }};">
                            {{ $attempt->percentage }}% {{ $passed ? '✓' : '✗' }}
                        </span>
                    </div>
                @empty
                    <div class="card-body"
                        style="text-align: center; padding: 24px; color: var(--text-muted); font-size: 13px;">No quiz attempts
                        yet.</div>
                @endforelse
            </div>

            <!-- Points History -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Points History</h3>
                </div>
                @forelse($points_timeline as $point)
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; padding: 8px 16px; border-bottom: 1px solid var(--border);">
                        <div>
                            <div style="font-size: 13px;">{{ $point->description }}</div>
                            <div style="font-size: 11px; color: var(--text-muted);">{{ $point->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <span
                            style="font-size: 13px; font-weight: 700; color: #fbbf24; font-family: 'JetBrains Mono', monospace;">+{{ $point->points }}</span>
                    </div>
                @empty
                    <div class="card-body"
                        style="text-align: center; padding: 24px; color: var(--text-muted); font-size: 13px;">No points earned
                        yet.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection