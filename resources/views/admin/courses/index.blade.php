@extends('layouts.app')

@section('title', 'Courses — Green Paw LMS')
@section('page_title', 'Course Management')

@section('topbar_actions')
    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-sm">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Course
    </a>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px 24px;">
            <form method="GET" action="{{ route('admin.courses.index') }}"
                style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <input type="text" name="search" class="form-input" placeholder="Search courses..."
                    value="{{ request('search') }}" style="max-width: 260px;">
                <select name="status" class="form-input form-select" style="max-width: 150px;">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                <select name="level" class="form-input form-select" style="max-width: 150px;">
                    <option value="">All Levels</option>
                    <option value="beginner" {{ request('level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="intermediate" {{ request('level') === 'intermediate' ? 'selected' : '' }}>Intermediate
                    </option>
                    <option value="advanced" {{ request('level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                @if(request()->hasAny(['search', 'status', 'level']))
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary btn-sm"
                        style="color: var(--text-muted);">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Course cards grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px;">
        @forelse($courses as $course)
            <div class="card" style="display: flex; flex-direction: column;">
                <div
                    style="height: 160px; background: linear-gradient(135deg, var(--bg-card-hover), var(--bg-secondary)); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    @if($course->thumbnail)
                        <img src="{{ Storage::url($course->thumbnail) }}" alt="{{ $course->title }}"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <svg width="48" height="48" fill="none" stroke="var(--text-muted)" viewBox="0 0 24 24"
                            style="opacity: 0.3;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    @endif
                </div>
                <div class="card-body" style="flex: 1; display: flex; flex-direction: column;">
                    <div style="display: flex; gap: 6px; margin-bottom: 10px; flex-wrap: wrap;">
                        @php
                            $statusColors = ['draft' => 'badge-warning', 'published' => 'badge-accent', 'archived' => 'badge-muted'];
                        @endphp
                        <span
                            class="badge {{ $statusColors[$course->status] ?? 'badge-muted' }}">{{ ucfirst($course->status) }}</span>
                        <span class="badge badge-muted">{{ ucfirst($course->level) }}</span>
                        @if($course->version > 1)
                            <span class="badge badge-muted">v{{ $course->version }}</span>
                        @endif
                    </div>
                    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 6px;">{{ $course->title }}</h3>
                    <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px; flex: 1;">
                        {{ Str::limit($course->short_description, 100) ?? 'No description' }}
                    </p>
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: var(--text-muted); margin-bottom: 16px;">
                        <span>{{ $course->lessons->count() }} lessons</span>
                        <span>by {{ $course->instructor->name }}</span>
                    </div>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-secondary btn-sm"
                            style="flex: 1;">Edit</a>
                        @if($course->status === 'draft')
                            <form method="POST" action="{{ route('admin.courses.publish', $course) }}" style="flex: 1;">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">Publish</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.courses.unpublish', $course) }}" style="flex: 1;">
                                @csrf
                                <button type="submit" class="btn btn-outline-accent btn-sm" style="width: 100%;">Unpublish</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.courses.duplicate', $course) }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm" title="Duplicate">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="card" style="grid-column: 1 / -1;">
                <div class="card-body" style="text-align: center; padding: 60px;">
                    <svg width="48" height="48" fill="none" stroke="var(--text-muted)" viewBox="0 0 24 24"
                        style="margin-bottom: 16px; opacity: 0.4;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <p style="color: var(--text-muted); margin-bottom: 16px;">No courses yet.</p>
                    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">Create Your First Course</a>
                </div>
            </div>
        @endforelse
    </div>

    @if($courses->hasPages())
        <div class="pagination" style="margin-top: 24px;">
            {{ $courses->links() }}
        </div>
    @endif
@endsection