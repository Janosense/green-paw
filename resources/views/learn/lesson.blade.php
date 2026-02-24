@extends('layouts.app')

@section('title', $lesson->title . ' — Green Paw LMS')
@section('page_title', $course->title)

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 300px; gap: 24px; align-items: start;">
        <!-- Main lesson content -->
        <div>
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">{{ $lesson->title }}</h2>
                        <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                            {{ $lesson->contentTypeIcon() }} {{ ucfirst($lesson->content_type) }}
                            @if($lesson->duration_minutes) · {{ $lesson->duration_minutes }} min @endif
                        </div>
                    </div>
                    @if($isCompleted)
                        <span class="badge badge-accent">✓ Completed</span>
                    @endif
                </div>
                <div class="card-body">
                    @if(in_array($lesson->content_type, ['text', 'html']))
                        <div class="lesson-content" style="line-height: 1.8; color: var(--text-secondary);">
                            @if($lesson->content_type === 'html')
                                {!! $lesson->content !!}
                            @else
                                {!! nl2br(e($lesson->content)) !!}
                            @endif
                        </div>
                    @elseif($lesson->content_type === 'video')
                        <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: var(--radius-sm); background: var(--bg-secondary);">
                            @if($lesson->media_url)
                                @if(str_contains($lesson->media_url, 'youtube') || str_contains($lesson->media_url, 'youtu.be'))
                                    <iframe src="{{ $lesson->media_url }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" allowfullscreen></iframe>
                                @else
                                    <video controls style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                                        <source src="{{ Storage::url($lesson->media_url) }}">
                                    </video>
                                @endif
                            @else
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: var(--text-muted);">No video available</div>
                            @endif
                        </div>
                        @if($lesson->content)
                            <div style="margin-top: 20px; line-height: 1.8; color: var(--text-secondary);">
                                {!! nl2br(e($lesson->content)) !!}
                            </div>
                        @endif
                    @elseif($lesson->content_type === 'audio')
                        @if($lesson->media_url)
                            <audio controls style="width: 100%; margin-bottom: 16px;">
                                <source src="{{ Storage::url($lesson->media_url) }}">
                            </audio>
                        @endif
                        @if($lesson->content)
                            <div style="line-height: 1.8; color: var(--text-secondary);">{!! nl2br(e($lesson->content)) !!}</div>
                        @endif
                    @elseif($lesson->content_type === 'pdf')
                        @if($lesson->media_url)
                            <iframe src="{{ Storage::url($lesson->media_url) }}" style="width: 100%; height: 600px; border: 0; border-radius: var(--radius-sm);"></iframe>
                        @endif
                    @endif
                </div>

                <!-- Navigation & completion -->
                <div style="padding: 16px 20px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; gap: 8px;">
                        @if($prevLesson)
                            <a href="{{ route('learn.lesson', [$course, $prevLesson]) }}" class="btn btn-secondary btn-sm">← Previous</a>
                        @endif
                        @if($nextLesson)
                            <a href="{{ route('learn.lesson', [$course, $nextLesson]) }}" class="btn btn-secondary btn-sm">Next →</a>
                        @endif
                    </div>

                    @if(!$isCompleted)
                        <form method="POST" action="{{ route('learn.lesson.complete', $lesson) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">
                                ✓ Mark as Complete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar: lesson list -->
        <div class="card" style="position: sticky; top: 80px;">
            <div class="card-header">
                <h3 class="card-title" style="font-size: 14px;">Lessons</h3>
                @if($enrollment)
                    <span style="font-size: 12px; color: var(--accent); font-weight: 600;">{{ $enrollment->progress_percent }}%</span>
                @endif
            </div>
            <div style="max-height: 500px; overflow-y: auto;">
                @foreach($lessons as $i => $l)
                <a href="{{ route('learn.lesson', [$course, $l]) }}"
                    style="display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-bottom: 1px solid var(--border); text-decoration: none; color: var(--text-primary); transition: background var(--transition);
                    {{ $l->id === $lesson->id ? 'background: var(--bg-card-hover);' : '' }}"
                    onmouseover="this.style.background='var(--bg-card-hover)'" onmouseout="this.style.background='{{ $l->id === $lesson->id ? 'var(--bg-card-hover)' : '' }}'">
                    @if(auth()->user()->hasCompletedLesson($l))
                        <span style="width: 22px; height: 22px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 11px; color: var(--bg-primary); flex-shrink: 0;">✓</span>
                    @else
                        <span style="width: 22px; height: 22px; border-radius: 50%; border: 2px solid var(--border); display: flex; align-items: center; justify-content: center; font-size: 10px; color: var(--text-muted); flex-shrink: 0;">{{ $i + 1 }}</span>
                    @endif
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 13px; font-weight: {{ $l->id === $lesson->id ? '700' : '500' }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ $l->title }}
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection
