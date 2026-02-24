@extends('layouts.app')

@section('title', $course->title . ' — Live Sessions')
@section('page_title', $course->title . ' — Live Sessions')

@section('topbar_actions')
    <a href="{{ route('admin.courses.live-sessions.create', $course) }}" class="btn btn-primary btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Schedule Session
    </a>
@endsection

@section('content')
    <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-secondary btn-sm" style="margin-bottom: 16px;">←
        Back to Course</a>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 16px;">✅ {{ session('success') }}</div>
    @endif

    <!-- Upcoming Sessions -->
    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 12px;">Upcoming Sessions</h3>
    @forelse($upcoming as $session)
        <div class="card" style="margin-bottom: 10px; border-left: 3px solid var(--accent);">
            <div class="card-body"
                style="padding: 14px 16px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-weight: 600; font-size: 15px;">{{ $session->platformIcon() }} {{ $session->title }}</div>
                    <div style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">
                        {{ $session->starts_at->format('M d, Y · h:i A') }} · {{ $session->duration_minutes }}min
                        · <span style="text-transform: capitalize;">{{ str_replace('_', ' ', $session->platform) }}</span>
                    </div>
                    @if($session->meeting_url)
                        <a href="{{ $session->meeting_url }}" target="_blank"
                            style="font-size: 12px; color: var(--accent); margin-top: 4px; display: inline-block;">🔗 Join Link</a>
                    @endif
                </div>
                <div style="display: flex; gap: 6px;">
                    @if($session->isLive())
                        <span class="badge badge-accent" style="animation: pulse 2s infinite;">🔴 LIVE</span>
                    @endif
                    <a href="{{ route('admin.courses.live-sessions.edit', [$course, $session]) }}"
                        class="btn btn-secondary btn-sm" style="padding: 4px 10px;">Edit</a>
                    <form method="POST" action="{{ route('admin.courses.live-sessions.destroy', [$course, $session]) }}"
                        onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 10px;">×</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-body" style="text-align: center; padding: 32px; color: var(--text-muted);">No upcoming sessions.
            </div>
        </div>
    @endforelse

    <!-- Past Sessions -->
    @if($past->count())
        <h3 style="font-size: 16px; font-weight: 700; margin: 24px 0 12px;">Past Sessions</h3>
        @foreach($past as $session)
            <div class="card" style="margin-bottom: 8px; opacity: 0.7;">
                <div class="card-body"
                    style="padding: 10px 16px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="font-weight: 600; font-size: 14px;">{{ $session->platformIcon() }} {{ $session->title }}</span>
                        <span
                            style="font-size: 12px; color: var(--text-muted); margin-left: 8px;">{{ $session->starts_at->format('M d · h:i A') }}</span>
                    </div>
                    <form method="POST" action="{{ route('admin.courses.live-sessions.destroy', [$course, $session]) }}"
                        onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 10px;">×</button>
                    </form>
                </div>
            </div>
        @endforeach
    @endif

    <style>
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }
    </style>
@endsection