@extends('layouts.app')

@section('title', 'Analytics Dashboard — Green Paw LMS')
@section('page_title', 'Analytics Dashboard')

@section('topbar_actions')
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.analytics.export', 'enrollments') }}" class="btn btn-secondary btn-sm">📥 Export
            Enrollments</a>
        <a href="{{ route('admin.analytics.export', 'grades') }}" class="btn btn-secondary btn-sm">📥 Export Grades</a>
        <a href="{{ route('admin.analytics.export', 'progress') }}" class="btn btn-secondary btn-sm">📥 Export Progress</a>
    </div>
@endsection

@section('content')
    <!-- KPI Cards -->
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px;">
        @php
            $kpis = [
                ['label' => 'Total Users', 'value' => number_format($overview['total_users']), 'icon' => '👤', 'color' => '#60a5fa'],
                ['label' => 'Active Enrollments', 'value' => number_format($overview['active_enrollments']), 'icon' => '📚', 'color' => '#34d399'],
                ['label' => 'Completion Rate', 'value' => $overview['completion_rate'] . '%', 'icon' => '✅', 'color' => '#a78bfa'],
                ['label' => 'Avg Quiz Score', 'value' => $overview['avg_quiz_score'] . '%', 'icon' => '📝', 'color' => '#fbbf24'],
                ['label' => 'Lessons Completed', 'value' => number_format($overview['total_lessons_completed']), 'icon' => '📖', 'color' => '#f472b6'],
                ['label' => 'Points Awarded', 'value' => number_format($overview['total_points_awarded']), 'icon' => '⭐', 'color' => '#fb923c'],
            ];
        @endphp

        @foreach($kpis as $kpi)
            <div class="card" style="position: relative; overflow: hidden;">
                <div class="card-body" style="padding: 16px; text-align: center;">
                    <div style="font-size: 28px; margin-bottom: 4px;">{{ $kpi['icon'] }}</div>
                    <div
                        style="font-size: 28px; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: {{ $kpi['color'] }};">
                        {{ $kpi['value'] }}</div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">{{ $kpi['label'] }}</div>
                </div>
                <div
                    style="position: absolute; bottom: 0; left: 0; right: 0; height: 3px; background: {{ $kpi['color'] }}; opacity: 0.4;">
                </div>
            </div>
        @endforeach
    </div>

    <!-- Engagement Trend Chart -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3 class="card-title">30-Day Engagement Trend</h3>
        </div>
        <div class="card-body" style="padding: 16px;">
            <canvas id="trendChart" height="280"></canvas>
        </div>
    </div>

    <!-- Top Courses + Quick Stats -->
    <div class="grid-2" style="grid-template-columns: 1fr 380px; align-items: start;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top Courses by Enrollment</h3>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Course</th>
                            <th>Enrollments</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topCourses as $i => $course)
                            <tr>
                                <td style="font-weight: 700; color: var(--accent);">{{ $i + 1 }}</td>
                                <td style="font-weight: 600;">{{ $course->title }}</td>
                                <td style="font-family: 'JetBrains Mono', monospace;">{{ $course->enrollments_count }}</td>
                                <td style="text-align: right;">
                                    <a href="{{ route('admin.analytics.course', $course) }}" class="btn btn-secondary btn-sm"
                                        style="padding: 4px 10px;">Details</a>
                                    <a href="{{ route('admin.analytics.gradebook', $course) }}" class="btn btn-secondary btn-sm"
                                        style="padding: 4px 10px;">Grades</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 32px; color: var(--text-muted);">No courses
                                    yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Stats</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                @php
                    $stats = [
                        ['Published Courses', $overview['published_courses']],
                        ['Total Enrollments', number_format($overview['total_enrollments'])],
                        ['Completed Enrollments', number_format($overview['completed_enrollments'])],
                    ];
                @endphp
                @foreach($stats as $stat)
                    <div
                        style="display: flex; justify-content: space-between; padding: 12px 16px; border-bottom: 1px solid var(--border);">
                        <span style="font-size: 14px; color: var(--text-secondary);">{{ $stat[0] }}</span>
                        <span
                            style="font-size: 14px; font-weight: 700; font-family: 'JetBrains Mono', monospace;">{{ $stat[1] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: @json($trend['labels']),
                datasets: [
                    {
                        label: 'Enrollments',
                        data: @json($trend['enrollments']),
                        borderColor: '#34d399',
                        backgroundColor: 'rgba(52, 211, 153, 0.1)',
                        fill: true,
                        tension: 0.4,
                    },
                    {
                        label: 'Lesson Completions',
                        data: @json($trend['completions']),
                        borderColor: '#60a5fa',
                        backgroundColor: 'rgba(96, 165, 250, 0.1)',
                        fill: true,
                        tension: 0.4,
                    },
                    {
                        label: 'Quiz Attempts',
                        data: @json($trend['quizAttempts']),
                        borderColor: '#fbbf24',
                        backgroundColor: 'rgba(251, 191, 36, 0.1)',
                        fill: true,
                        tension: 0.4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { labels: { color: '#94a3b8' } } },
                scales: {
                    x: { ticks: { color: '#64748b', maxTicksLimit: 10 }, grid: { color: 'rgba(100,116,139,0.1)' } },
                    y: { ticks: { color: '#64748b' }, grid: { color: 'rgba(100,116,139,0.1)' }, beginAtZero: true },
                },
            },
        });
    </script>
@endsection