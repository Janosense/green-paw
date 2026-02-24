@extends('layouts.app')

@section('title', $path->title . ' — Green Paw LMS')
@section('page_title', $path->title)

@section('content')
    <div style="max-width: 800px;">
        <!-- Path info -->
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-body">
                <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                    <span class="badge badge-accent">{{ ucfirst($path->level) }}</span>
                    <span class="badge badge-muted">{{ $path->courses->count() }} courses</span>
                </div>
                <p style="color: var(--text-secondary); line-height: 1.7;">{{ $path->description }}</p>

                @if($progress)
                    <div style="margin-top: 16px;">
                        <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                            <span style="color: var(--text-muted);">Your Progress</span>
                            <span
                                style="color: var(--accent); font-weight: 600;">{{ $progress['completed'] }}/{{ $progress['total'] }}
                                courses ({{ $progress['percent'] }}%)</span>
                        </div>
                        <div style="height: 8px; background: var(--bg-input); border-radius: 4px; overflow: hidden;">
                            <div
                                style="height: 100%; width: {{ $progress['percent'] }}%; background: var(--accent); border-radius: 4px; transition: width 0.3s;">
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Course list -->
        <h2 style="font-size: 16px; font-weight: 700; margin-bottom: 16px;">Courses in this Path</h2>

        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($path->courses as $i => $course)
                @php $cp = $courseProgress[$course->id] ?? ['enrolled' => false, 'percent' => 0, 'completed' => false]; @endphp
                <div class="card" style="{{ $cp['completed'] ? 'border-color: rgba(52, 211, 153, 0.3);' : '' }}">
                    <div class="card-body" style="display: flex; align-items: center; gap: 16px;">
                        <!-- Step number -->
                        <div
                            style="width: 40px; height: 40px; border-radius: 50%; background: {{ $cp['completed'] ? 'var(--accent)' : 'var(--bg-input)' }}; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700; color: {{ $cp['completed'] ? 'var(--bg-primary)' : 'var(--text-muted)' }}; flex-shrink: 0;">
                            @if($cp['completed']) ✓ @else {{ $i + 1 }} @endif
                        </div>

                        <!-- Course info -->
                        <div style="flex: 1; min-width: 0;">
                            <h3 style="font-size: 15px; font-weight: 700; margin-bottom: 4px;">{{ $course->title }}</h3>
                            <div style="font-size: 13px; color: var(--text-muted);">
                                {{ $course->lessons->count() }} lessons · {{ ucfirst($course->level) }}
                                @if($course->instructor) · {{ $course->instructor->name }} @endif
                            </div>
                            @if($cp['enrolled'] && !$cp['completed'])
                                <div style="margin-top: 8px;">
                                    <div
                                        style="height: 4px; background: var(--bg-input); border-radius: 2px; overflow: hidden; max-width: 200px;">
                                        <div
                                            style="height: 100%; width: {{ $cp['percent'] }}%; background: var(--accent); border-radius: 2px;">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Action -->
                        @if($cp['completed'])
                            <span class="badge badge-accent">✓ Completed</span>
                        @elseif($cp['enrolled'])
                            <a href="{{ route('catalog.show', $course) }}" class="btn btn-primary btn-sm">Continue</a>
                        @else
                            <form method="POST" action="{{ route('learn.enroll', $course) }}">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm">Enroll</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection