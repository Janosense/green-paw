@extends('layouts.app')

@section('title', ($session ? 'Edit' : 'Schedule') . ' Live Session')
@section('page_title', ($session ? 'Edit' : 'Schedule') . ' Live Session')

@section('content')
    <a href="{{ route('admin.courses.live-sessions.index', $course) }}" class="btn btn-secondary btn-sm"
        style="margin-bottom: 16px;">← Back to Sessions</a>

    <div class="card" style="max-width: 600px;">
        <div class="card-body">
            <form method="POST"
                action="{{ $session ? route('admin.courses.live-sessions.update', [$course, $session]) : route('admin.courses.live-sessions.store', $course) }}">
                @csrf
                @if($session) @method('PUT') @endif

                <div class="form-group">
                    <label class="form-label" for="title">Session Title</label>
                    <input type="text" name="title" id="title" class="form-input"
                        value="{{ old('title', $session?->title) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" id="description" class="form-input form-textarea"
                        rows="3">{{ old('description', $session?->description) }}</textarea>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label" for="platform">Platform</label>
                        <select name="platform" id="platform" class="form-input form-select">
                            @foreach(['zoom' => '📹 Zoom', 'google_meet' => '🎥 Google Meet', 'teams' => '💬 Teams', 'other' => '📺 Other'] as $val => $label)
                                <option value="{{ $val }}" {{ old('platform', $session?->platform) === $val ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="duration_minutes">Duration (min)</label>
                        <input type="number" name="duration_minutes" id="duration_minutes" class="form-input"
                            value="{{ old('duration_minutes', $session?->duration_minutes ?? 60) }}" min="5" max="480"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="starts_at">Start Date & Time</label>
                    <input type="datetime-local" name="starts_at" id="starts_at" class="form-input"
                        value="{{ old('starts_at', $session?->starts_at?->format('Y-m-d\TH:i')) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="meeting_url">Meeting URL</label>
                    <input type="url" name="meeting_url" id="meeting_url" class="form-input"
                        value="{{ old('meeting_url', $session?->meeting_url) }}" placeholder="https://zoom.us/j/...">
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary"
                        style="width: auto;">{{ $session ? 'Update' : 'Schedule' }} Session</button>
                    <a href="{{ route('admin.courses.live-sessions.index', $course) }}" class="btn btn-secondary"
                        style="width: auto;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection