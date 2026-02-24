@extends('layouts.app')

@section('title', 'Grade Book — ' . $course->title)
@section('page_title', $course->title . ' — Grade Book')

@section('topbar_actions')
    <a href="{{ route('admin.analytics.export', ['type' => 'grades', 'course_id' => $course->id]) }}"
        class="btn btn-secondary btn-sm">📥 Export CSV</a>
@endsection

@section('content')
    <a href="{{ route('admin.analytics.course', $course) }}" class="btn btn-secondary btn-sm" style="margin-bottom: 16px;">←
        Back to Course Analytics</a>

    <div class="card">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="position: sticky; left: 0; background: var(--bg-card); z-index: 1; min-width: 180px;">
                            Student</th>
                        @foreach($quizzes as $quiz)
                            <th style="text-align: center; min-width: 140px; font-size: 13px;">
                                {{ $quiz->title }}<br>
                                <span
                                    style="font-weight: 400; color: var(--text-muted); font-size: 11px;">{{ $quiz->passing_score }}%
                                    to pass</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($grades as $row)
                        <tr>
                            <td style="position: sticky; left: 0; background: var(--bg-card); z-index: 1; font-weight: 600;">
                                {{ $row['student']->name }}
                            </td>
                            @foreach($quizzes as $quiz)
                                @php
                                    $attempt = $row['scores'][$quiz->id] ?? null;
                                @endphp
                                <td style="text-align: center;">
                                    @if($attempt)
                                                            @php
                                                                $passed = $attempt->percentage >= $quiz->passing_score;
                                                                $bg = $passed ? 'rgba(52, 211, 153, 0.15)' : 'rgba(239, 68, 68, 0.15)';
                                                                $color = $passed ? '#34d399' : '#ef4444';
                                                            @endphp
                                         <span
                                                                style="display: inline-block; padding: 4px 10px; border-radius: var(--radius-sm); background: {{ $bg }}; color: {{ $color }}; font-weight: 700; font-family: 'JetBrains Mono', monospace; font-size: 13px;">
                                                                {{ $attempt->percentage }}%
                                                            </span>
                                                            @if($attempt->status === 'submitted')
                                                                <div style="font-size: 10px; color: var(--warning); margin-top: 2px;">⏳ Pending</div>
                                                            @endif
                                    @else
                                        <span style="color: var(--text-muted); font-size: 12px;">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $quizzes->count() + 1 }}"
                                style="text-align: center; padding: 40px; color: var(--text-muted);">No students enrolled.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection