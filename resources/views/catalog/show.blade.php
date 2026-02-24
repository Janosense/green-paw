@extends('layouts.app')

@section('title', $course->title . ' — Green Paw LMS')
@section('page_title', $course->title)
@section('meta_description', $course->short_description)

@section('content')
    <div class="grid-2" style="grid-template-columns: 1fr 360px; align-items: start;">
        <!-- Left: Course info -->
        <div>
            <!-- Hero -->
            <div class="card" style="margin-bottom: 24px;">
                @if($course->thumbnail)
                    <div style="height: 280px; overflow: hidden;">
                        <img src="{{ Storage::url($course->thumbnail) }}" alt="{{ $course->title }}"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                @endif
                <div class="card-body">
                    <div style="display: flex; gap: 8px; margin-bottom: 12px; flex-wrap: wrap;">
                        <span class="badge badge-accent">{{ ucfirst($course->level) }}</span>
                        @foreach($course->categories as $cat)
                            <span class="badge badge-muted">{{ $cat->name }}</span>
                        @endforeach
                        @if($course->version > 1)
                            <span class="badge badge-muted">Version {{ $course->version }}</span>
                        @endif
                    </div>

                    <p style="color: var(--text-secondary); font-size: 15px; line-height: 1.7; margin-bottom: 16px;">
                        {{ $course->short_description }}
                    </p>

                    <div style="display: flex; gap: 24px; font-size: 13px; color: var(--text-muted);">
                        <span>👤 {{ $course->instructor->name }}</span>
                        <span>📚 {{ $course->lessons->count() }} lessons</span>
                        @if($course->totalDuration())
                            <span>⏱ {{ $course->totalDuration() }} min</span>
                        @endif
                        @if($course->published_at)
                            <span>📅 {{ $course->published_at->format('M Y') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Full description -->
            @if($course->description)
                <div class="card" style="margin-bottom: 24px;">
                    <div class="card-header">
                        <h2 class="card-title">About This Course</h2>
                    </div>
                    <div class="card-body" style="color: var(--text-secondary); line-height: 1.8;">
                        {!! nl2br(e($course->description)) !!}
                    </div>
                </div>
            @endif

            <!-- Prerequisites -->
            @if($course->prerequisites->count())
                <div class="card" style="margin-bottom: 24px;">
                    <div class="card-header">
                        <h2 class="card-title">Prerequisites</h2>
                    </div>
                    <div class="card-body">
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @foreach($course->prerequisites as $prereq)
                                <a href="{{ route('catalog.show', $prereq) }}"
                                    style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; background: var(--bg-input); border-radius: var(--radius-sm); text-decoration: none; color: var(--text-primary); transition: background var(--transition);"
                                    onmouseover="this.style.background='var(--bg-card-hover)'"
                                    onmouseout="this.style.background='var(--bg-input)'">
                                    <svg width="16" height="16" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <span style="font-weight: 600; font-size: 14px;">{{ $prereq->title }}</span>
                                    <span class="badge badge-muted" style="margin-left: auto;">{{ ucfirst($prereq->level) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right: Lesson list -->
        <div class="card" style="position: sticky; top: 80px;">
            <div class="card-header">
                <h2 class="card-title">Course Content</h2>
                <span style="font-size: 13px; color: var(--text-muted);">{{ $course->lessons->count() }} lessons</span>
            </div>
            <div style="max-height: 500px; overflow-y: auto;">
                @forelse($course->lessons as $index => $lesson)
                    <div
                        style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-bottom: 1px solid var(--border);">
                        <span
                            style="width: 24px; height: 24px; border-radius: 50%; background: var(--bg-input); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; color: var(--text-muted); flex-shrink: 0;">
                            {{ $index + 1 }}
                        </span>
                        <div style="flex: 1; min-width: 0;">
                            <div
                                style="font-size: 14px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $lesson->title }}
                            </div>
                            <div style="font-size: 12px; color: var(--text-muted); display: flex; gap: 8px;">
                                <span>{{ $lesson->contentTypeIcon() }} {{ ucfirst($lesson->content_type) }}</span>
                                @if($lesson->duration_minutes)<span>{{ $lesson->duration_minutes }}min</span>@endif
                            </div>
                        </div>
                        @if($lesson->is_free_preview)
                            <span class="badge badge-accent" style="font-size: 10px;">FREE</span>
                        @else
                            <svg width="14" height="14" fill="none" stroke="var(--text-muted)" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        @endif
                    </div>
                @empty
                    <div style="padding: 32px; text-align: center; color: var(--text-muted);">No lessons yet.</div>
                @endforelse
            </div>
            <div class="card-body" style="border-top: 1px solid var(--border);">
                @if($course->price && $course->price > 0)
                    <div style="text-align: center; margin-bottom: 12px;">
                        <span
                            style="font-size: 28px; font-weight: 800; color: var(--accent); font-family: 'JetBrains Mono', monospace;">
                            ${{ number_format($course->price, 2) }}
                        </span>
                    </div>
                @endif
                <button class="btn btn-primary" style="width: 100%;">
                    {{ ($course->price && $course->price > 0) ? 'Enroll Now' : 'Start Learning' }}
                </button>
            </div>
        </div>
    </div>
@endsection