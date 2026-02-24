@extends('layouts.app')

@section('title', 'Learning Paths — Green Paw LMS')
@section('page_title', 'Learning Paths')

@section('content')
    <p style="color: var(--text-secondary); margin-bottom: 24px;">Follow structured curricula to master new skills
        step-by-step.</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 20px;">
        @forelse($paths as $path)
            <a href="{{ route('paths.show', $path) }}" class="card"
                style="text-decoration: none; color: inherit; transition: all var(--transition);"
                onmouseover="this.style.borderColor='var(--accent-dim)'; this.style.transform='translateY(-2px)'"
                onmouseout="this.style.borderColor='var(--border)'; this.style.transform='none'">
                <div class="card-body">
                    <div style="display: flex; gap: 6px; margin-bottom: 10px;">
                        <span class="badge badge-accent">{{ ucfirst($path->level) }}</span>
                        <span class="badge badge-muted">{{ $path->courses_count }} courses</span>
                    </div>
                    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 6px;">{{ $path->title }}</h3>
                    <p style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                        {{ Str::limit($path->description, 120) }}</p>
                </div>
            </a>
        @empty
            <div class="card" style="grid-column: 1 / -1;">
                <div class="card-body" style="text-align: center; padding: 60px;">
                    <p style="color: var(--text-muted);">No learning paths available yet.</p>
                </div>
            </div>
        @endforelse
    </div>
@endsection