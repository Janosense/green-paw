@extends('layouts.app')

@section('title', $course->title . ' — Analytics')
@section('page_title', $course->title . ' — Analytics')

@section('topbar_actions')
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.analytics.gradebook', $course) }}" class="btn btn-primary btn-sm">📊 Grade Book</a>
        <a href="{{ route('admin.analytics.export', ['type' => 'progress', 'course_id' => $course->id]) }}"
            class="btn btn-secondary btn-sm">📥 Export</a>
    </div>
@endsection

@section('content')
    <a href="{{ route('admin.analytics.dashboard') }}" class="btn btn-secondary btn-sm" style="margin-bottom: 16px;">← Back
        to Analytics</a>

    <!-- KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
        <div class="card">
            <div class="card-body" style="padding: 14px; text-align: center;">
                <div style="font-size: 24px; font-weight: 800; color: #34d399; font-family: 'JetBrains Mono', monospace;">
                    {{ $analytics['total_enrolled'] }}</div>
                <div style="font-size: 12px; color: var(--text-muted);">Enrolled</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body" style="padding: 14px; text-align: center;">
                <div style="font-size: 24px; font-weight: 800; color: #a78bfa; font-family: 'JetBrains Mono', monospace;">
                    {{ $analytics['completion_rate'] }}%</div>
                <div style="font-size: 12px; color: var(--text-muted);">Completion Rate</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body" style="padding: 14px; text-align: center;">
                <div style="font-size: 24px; font-weight: 800; color: #60a5fa; font-family: 'JetBrains Mono', monospace;">
                    {{ $analytics['avg_progress'] }}%</div>
                <div style="font-size: 12px; color: var(--text-muted);">Avg Progress</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body" style="padding: 14px; text-align: center;">
                <div style="font-size: 24px; font-weight: 800; color: #fbbf24; font-family: 'JetBrains Mono', monospace;">
                    {{ $analytics['completed'] }}</div>
                <div style="font-size: 12px; color: var(--text-muted);">Completed</div>
            </div>
        </div>
    </div>

    <div class="grid-2" style="align-items: start;">
        <!-- Lesson Engagement -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lesson Engagement</h3>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Lesson</th>
                            <th>Completions</th>
                            <th>Avg Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($analytics['lessons'] as $lesson)
                            <tr>
                                <td style="font-weight: 600;">{{ $lesson->title }}</td>
                                <td style="font-family: 'JetBrains Mono', monospace;">{{ $lesson->completions }}</td>
                                <td style="font-family: 'JetBrains Mono', monospace;">
                                    @if($lesson->avg_time_seconds > 0)
                                        {{ floor($lesson->avg_time_seconds / 60) }}m {{ $lesson->avg_time_seconds % 60 }}s
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 24px; color: var(--text-muted);">No lessons.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quiz Performance -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quiz Performance</h3>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Quiz</th>
                            <th>Attempts</th>
                            <th>Avg Score</th>
                            <th>Pass Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($analytics['quizzes'] as $quiz)
                            <tr>
                                <td style="font-weight: 600;">{{ $quiz->title }}</td>
                                <td style="font-family: 'JetBrains Mono', monospace;">{{ $quiz->attempt_count }}</td>
                                <td style="font-family: 'JetBrains Mono', monospace;">{{ $quiz->avg_score }}%</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <div
                                            style="flex: 1; height: 6px; background: var(--bg-input); border-radius: 3px; overflow: hidden;">
                                            <div
                                                style="height: 100%; width: {{ $quiz->pass_rate }}%; background: {{ $quiz->pass_rate >= 70 ? '#34d399' : ($quiz->pass_rate >= 40 ? '#fbbf24' : '#ef4444') }}; border-radius: 3px;">
                                            </div>
                                        </div>
                                        <span
                                            style="font-size: 12px; font-family: 'JetBrains Mono', monospace;">{{ $quiz->pass_rate }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 24px; color: var(--text-muted);">No quizzes.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection