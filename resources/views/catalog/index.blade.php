@extends('layouts.app')

@section('title', 'Course Catalog — Green Paw LMS')
@section('page_title', 'Course Catalog')

@section('content')
    <!-- Search & Filters -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px 24px;">
            <form method="GET" action="{{ route('catalog.index') }}"
                style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <input type="text" name="search" class="form-input" placeholder="Search courses..."
                    value="{{ request('search') }}" style="max-width: 280px;">
                <select name="category" class="form-input form-select" style="max-width: 180px;">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->courses_count }})</option>
                    @endforeach
                </select>
                <select name="level" class="form-input form-select" style="max-width: 150px;">
                    <option value="">All Levels</option>
                    <option value="beginner" {{ request('level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="intermediate" {{ request('level') === 'intermediate' ? 'selected' : '' }}>Intermediate
                    </option>
                    <option value="advanced" {{ request('level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                </select>
                <select name="sort" class="form-input form-select" style="max-width: 150px;">
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="title" {{ request('sort') === 'title' ? 'selected' : '' }}>A → Z</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Search</button>
                @if(request()->hasAny(['search', 'category', 'level', 'sort']))
                    <a href="{{ route('catalog.index') }}" class="btn btn-secondary btn-sm"
                        style="color: var(--text-muted);">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Course grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        @forelse($courses as $course)
            <a href="{{ route('catalog.show', $course) }}" class="card"
                style="text-decoration: none; color: inherit; display: flex; flex-direction: column; transition: all var(--transition);"
                onmouseover="this.style.borderColor='var(--accent-dim)'; this.style.transform='translateY(-2px)'"
                onmouseout="this.style.borderColor='var(--border)'; this.style.transform='none'">
                <div
                    style="height: 150px; background: linear-gradient(135deg, var(--bg-card-hover), var(--bg-secondary)); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    @if($course->thumbnail)
                        <img src="{{ Storage::url($course->thumbnail) }}" alt="{{ $course->title }}"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <svg width="40" height="40" fill="none" stroke="var(--text-muted)" viewBox="0 0 24 24"
                            style="opacity: 0.3;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    @endif
                </div>
                <div class="card-body" style="flex: 1; display: flex; flex-direction: column;">
                    <div style="display: flex; gap: 6px; margin-bottom: 10px;">
                        <span class="badge badge-accent">{{ ucfirst($course->level) }}</span>
                        @foreach($course->categories->take(2) as $cat)
                            <span class="badge badge-muted">{{ $cat->name }}</span>
                        @endforeach
                    </div>
                    <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 6px;">{{ $course->title }}</h3>
                    <p style="font-size: 13px; color: var(--text-secondary); flex: 1; margin-bottom: 12px;">
                        {{ Str::limit($course->short_description, 100) }}
                    </p>
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: var(--text-muted);">
                        <span>{{ $course->lessons->count() }} lessons</span>
                        <span>{{ $course->instructor->name }}</span>
                    </div>
                    @if($course->price && $course->price > 0)
                        <div
                            style="margin-top: 8px; font-size: 18px; font-weight: 800; color: var(--accent); font-family: 'JetBrains Mono', monospace;">
                            ${{ number_format($course->price, 2) }}
                        </div>
                    @else
                        <div style="margin-top: 8px; font-size: 14px; font-weight: 600; color: var(--success);">Free</div>
                    @endif
                </div>
            </a>
        @empty
            <div class="card" style="grid-column: 1 / -1;">
                <div class="card-body" style="text-align: center; padding: 60px;">
                    <p style="color: var(--text-muted); font-size: 16px;">No courses available yet.</p>
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