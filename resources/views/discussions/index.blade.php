@extends('layouts.app')

@section('title', $course->title . ' — Discussions')
@section('page_title', $course->title . ' — Forum')

@section('content')
    <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-secondary btn-sm" style="margin-bottom: 16px;">←
        Back to Course</a>

    <div class="grid-2" style="grid-template-columns: 1fr 360px; align-items: start;">
        <!-- Threads -->
        <div>
            <!-- Pinned -->
            @if($pinned->count())
                <div style="margin-bottom: 16px;">
                    @foreach($pinned as $thread)
                        <a href="{{ route('discussions.show', [$course, $thread]) }}" class="card"
                            style="display: block; margin-bottom: 8px; border-left: 3px solid var(--accent); text-decoration: none;">
                            <div class="card-body"
                                style="padding: 12px 16px; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <span
                                        style="font-size: 11px; color: var(--accent); font-weight: 700; text-transform: uppercase;">📌
                                        Pinned</span>
                                    <div style="font-weight: 600; margin-top: 2px;">{{ $thread->title }}</div>
                                    <div style="font-size: 12px; color: var(--text-muted);">{{ $thread->user->name }} ·
                                        {{ $thread->created_at->diffForHumans() }}</div>
                                </div>
                                <span class="badge badge-muted">{{ $thread->replies_count }} replies</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- Regular threads -->
            @forelse($discussions as $thread)
                <a href="{{ route('discussions.show', [$course, $thread]) }}" class="card"
                    style="display: block; margin-bottom: 8px; text-decoration: none;">
                    <div class="card-body"
                        style="padding: 12px 16px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 600;">
                                {{ $thread->title }}
                                @if($thread->is_locked) <span style="color: var(--text-muted);">🔒</span> @endif
                            </div>
                            <div style="font-size: 12px; color: var(--text-muted);">{{ $thread->user->name }} ·
                                {{ $thread->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="badge badge-muted">{{ $thread->replies_count }}</span>
                    </div>
                </a>
            @empty
                <div class="card">
                    <div class="card-body" style="text-align: center; padding: 40px; color: var(--text-muted);">No discussions
                        yet. Start one!</div>
                </div>
            @endforelse

            {{ $discussions->links() }}
        </div>

        <!-- New Thread -->
        <div class="card" style="position: sticky; top: 20px;">
            <div class="card-header">
                <h3 class="card-title">New Discussion</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('discussions.store', $course) }}">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="title" class="form-input" placeholder="Discussion title..." required>
                    </div>
                    <div class="form-group">
                        <textarea name="body" class="form-input form-textarea" rows="4" placeholder="What's on your mind?"
                            required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Post Discussion</button>
                </form>
            </div>
        </div>
    </div>
@endsection