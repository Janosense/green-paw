@extends('layouts.app')

@section('title', $discussion->title . ' — Discussion')
@section('page_title', $discussion->title)

@section('topbar_actions')
    @if(auth()->user()->hasAnyRole(['super-admin', 'admin', 'instructor']))
        <div style="display: flex; gap: 6px;">
            <form method="POST" action="{{ route('discussions.pin', $discussion) }}" style="display:inline;">@csrf
                <button type="submit"
                    class="btn btn-secondary btn-sm">{{ $discussion->is_pinned ? '📌 Unpin' : '📌 Pin' }}</button>
            </form>
            <form method="POST" action="{{ route('discussions.lock', $discussion) }}" style="display:inline;">@csrf
                <button type="submit"
                    class="btn btn-secondary btn-sm">{{ $discussion->is_locked ? '🔓 Unlock' : '🔒 Lock' }}</button>
            </form>
        </div>
    @endif
@endsection

@section('content')
    <a href="{{ route('discussions.index', $course) }}" class="btn btn-secondary btn-sm" style="margin-bottom: 16px;">← Back
        to Forum</a>

    <!-- Original post -->
    <div class="card" style="margin-bottom: 16px; border-left: 3px solid var(--accent);">
        <div class="card-body" style="padding: 16px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <div
                    style="width: 36px; height: 36px; border-radius: 50%; background: var(--bg-input); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; color: var(--accent);">
                    {{ strtoupper(substr($discussion->user->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 14px;">{{ $discussion->user->name }}</div>
                    <div style="font-size: 11px; color: var(--text-muted);">
                        {{ $discussion->created_at->format('M d, Y · h:i A') }}</div>
                </div>
                @if($discussion->is_pinned) <span class="badge badge-accent" style="margin-left: auto;">📌 Pinned</span>
                @endif
                @if($discussion->is_locked) <span class="badge badge-warning"
                style="margin-left: {{ $discussion->is_pinned ? '0' : 'auto' }};">🔒 Locked</span> @endif
            </div>
            <div style="line-height: 1.6; white-space: pre-wrap;">{{ $discussion->body }}</div>
        </div>
    </div>

    <!-- Replies -->
    <div style="margin-bottom: 16px;">
        <h3 style="font-size: 16px; margin-bottom: 12px; color: var(--text-secondary);">{{ $discussion->replies_count }}
            {{ Str::plural('Reply', $discussion->replies_count) }}</h3>

        @foreach($discussion->replies as $reply)
            <div class="card" style="margin-bottom: 8px; margin-left: 0;">
                <div class="card-body" style="padding: 12px 16px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                        <div
                            style="width: 28px; height: 28px; border-radius: 50%; background: var(--bg-input); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px;">
                            {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                        </div>
                        <span style="font-weight: 600; font-size: 13px;">{{ $reply->user->name }}</span>
                        <span
                            style="font-size: 11px; color: var(--text-muted);">{{ $reply->created_at->diffForHumans() }}</span>
                    </div>
                    <div style="line-height: 1.5; font-size: 14px; white-space: pre-wrap;">{{ $reply->body }}</div>
                </div>

                <!-- Nested replies -->
                @if($reply->children->count())
                    <div style="border-top: 1px solid var(--border); margin-left: 32px;">
                        @foreach($reply->children as $child)
                            <div style="padding: 10px 16px; border-bottom: 1px solid var(--border);">
                                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
                                    <span style="font-weight: 600; font-size: 12px;">{{ $child->user->name }}</span>
                                    <span
                                        style="font-size: 11px; color: var(--text-muted);">{{ $child->created_at->diffForHumans() }}</span>
                                </div>
                                <div style="font-size: 13px; white-space: pre-wrap;">{{ $child->body }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Reply form -->
    @if(!$discussion->is_locked)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Post a Reply</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('discussions.reply', $discussion) }}">
                    @csrf
                    <div class="form-group">
                        <textarea name="body" class="form-input form-textarea" rows="3" placeholder="Write your reply..."
                            required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: auto;">Reply</button>
                </form>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 24px; color: var(--text-muted);">🔒 This discussion is
                locked. No new replies can be posted.</div>
        </div>
    @endif
@endsection