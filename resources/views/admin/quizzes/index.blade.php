@extends('layouts.app')

@section('title', 'Quizzes — ' . $course->title)
@section('page_title', $course->title . ' — Quizzes')

@section('topbar_actions')
    <a href="{{ route('admin.courses.quizzes.create', $course) }}" class="btn btn-primary btn-sm">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Quiz
    </a>
@endsection

@section('content')
    <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-secondary btn-sm" style="margin-bottom: 16px;">←
        Back to Course</a>

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Questions</th>
                        <th>Time Limit</th>
                        <th>Passing</th>
                        <th>Attempts</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quizzes as $quiz)
                        <tr>
                            <td style="font-weight: 600;">{{ $quiz->title }}</td>
                            <td style="font-family: 'JetBrains Mono', monospace;">{{ $quiz->questions_count }}</td>
                            <td>{{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' min' : '—' }}</td>
                            <td>{{ $quiz->passing_score }}%</td>
                            <td>{{ $quiz->attempts_count }} taken / {{ $quiz->max_attempts }} max</td>
                            <td>
                                @if($quiz->is_published)
                                    <span class="badge badge-accent">Published</span>
                                @else
                                    <span class="badge badge-warning">Draft</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="{{ route('admin.courses.quizzes.edit', [$course, $quiz]) }}"
                                        class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.courses.quizzes.destroy', [$course, $quiz]) }}"
                                        onsubmit="return confirm('Delete this quiz?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">No quizzes yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection