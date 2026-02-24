@extends('layouts.app')

@section('title', 'Announcements — Green Paw LMS')
@section('page_title', 'Announcements')

@section('content')
    @forelse($announcements as $announcement)
        <div class="card"
            style="margin-bottom: 12px; {{ $announcement->isGlobal() ? 'border-left: 3px solid var(--accent);' : '' }}">
            <div class="card-body" style="padding: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                    <div>
                        <h3 style="font-size: 16px; font-weight: 700; margin: 0;">{{ $announcement->title }}</h3>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                            {{ $announcement->author->name }}
                            · {{ $announcement->published_at->format('M d, Y') }}
                            @if($announcement->course)
                                · <span style="color: var(--accent);">{{ $announcement->course->title }}</span>
                            @else
                                · <span class="badge badge-accent" style="font-size: 10px;">Global</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div style="line-height: 1.6; font-size: 14px; white-space: pre-wrap;">{{ $announcement->body }}</div>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 48px; color: var(--text-muted);">No announcements yet.
            </div>
        </div>
    @endforelse

    {{ $announcements->links() }}
@endsection