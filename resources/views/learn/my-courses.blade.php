@extends('layouts.app')

@section('title', 'My Courses — Green Paw LMS')
@section('page_title', 'My Courses')

@section('content')
    <!-- Stats bar -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 20px;">
                <div
                    style="font-size: 28px; font-weight: 800; color: var(--accent); font-family: 'JetBrains Mono', monospace;">
                    {{ number_format($totalPoints) }}</div>
                <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Total Points</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 20px;">
                <div
                    style="font-size: 28px; font-weight: 800; color: var(--warning); font-family: 'JetBrains Mono', monospace;">
                    🔥 {{ $streak }}</div>
                <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Day Streak</div>
            </div>
        </div>
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 20px;">
                <div
                    style="font-size: 28px; font-weight: 800; color: var(--text-primary); font-family: 'JetBrains Mono', monospace;">
                    🏆 {{ $badgeCount }}</div>
                <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">Badges Earned</div>
            </div>
        </div>
    </div>

    <!-- Active courses -->
    <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 16px;">
        In Progress <span style="color: var(--text-muted); font-weight: 400;">({{ $activeEnrollments->count() }})</span>
    </h2>

    @if($activeEnrollments->count())
        <div
            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px; margin-bottom: 32px;">
            @foreach($activeEnrollments as $enrollment)
                <div class="card" style="display: flex; flex-direction: column;">
                    <div class="card-body" style="flex: 1;">
                        <div style="display: flex; gap: 6px; margin-bottom: 10px;">
                            <span class="badge badge-accent">{{ ucfirst($enrollment->course->level) }}</span>
                        </div>
                        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 6px;">{{ $enrollment->course->title }}</h3>
                        <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;">
                            {{ Str::limit($enrollment->course->short_description, 80) }}
                        </p>

                        <!-- Progress bar -->
                        <div style="margin-bottom: 12px;">
                            <div
                                style="display: flex; justify-content: space-between; font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">
                                <span>Progress</span>
                                <span style="font-weight: 600; color: var(--accent);">{{ $enrollment->progress_percent }}%</span>
                            </div>
                            <div style="height: 6px; background: var(--bg-input); border-radius: 3px; overflow: hidden;">
                                <div
                                    style="height: 100%; width: {{ $enrollment->progress_percent }}%; background: var(--accent); border-radius: 3px; transition: width 0.3s;">
                                </div>
                            </div>
                        </div>

                @php
                    $nextLesson = $enrollment->course->lessons->first(fn($l) => !auth()->user()->hasCompletedLesson($l));
                @endphp

                        <div style="display: flex; gap: 8px;">
                            @if($nextLesson)
                                <a href="{{ route('learn.lesson', [$enrollment->course, $nextLesson]) }}" class="btn btn-primary btn-sm"
                                    style="flex: 1;">
                                    Continue Learning
                                </a>
                            @else
                                <span class="btn btn-secondary btn-sm" style="flex: 1; opacity: 0.6;">All lessons completed</span>
                            @endif
                            <form method="POST" action="{{ route('learn.unenroll', $enrollment->course) }}"
                                onsubmit="return confirm('Unenroll from this course?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-secondary btn-sm" title="Unenroll"
                                    style="padding: 6px 10px;">✕</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card" style="margin-bottom: 32px;">
            <div class="card-body" style="text-align: center; padding: 40px;">
                <p style="color: var(--text-muted); margin-bottom: 12px;">No active courses. Browse the catalog to get started!
                </p>
                <a href="{{ route('catalog.index') }}" class="btn btn-primary">Browse Courses</a>
            </div>
        </div>
    @endif

    <!-- Completed courses -->
    @if($completedEnrollments->count())
        <h2 style="font-size: 18px; font-weight: 700; margin-bottom: 16px;">
            Completed <span style="color: var(--text-muted); font-weight: 400;">({{ $completedEnrollments->count() }})</span>
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px;">
            @foreach($completedEnrollments as $enrollment)
                <div class="card" style="border-color: rgba(52, 211, 153, 0.2);">
                    <div class="card-body">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                            <span style="font-size: 20px;">✅</span>
                            <h3 style="font-size: 16px; font-weight: 700;">{{ $enrollment->course->title }}</h3>
                        </div>
                        <div style="font-size: 12px; color: var(--text-muted);">
                            Completed {{ $enrollment->completed_at?->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection