@extends('layouts.app')

@section('title', 'Learning Paths — Green Paw LMS')
@section('page_title', 'Learning Paths')

@section('topbar_actions')
    <a href="{{ route('admin.learning-paths.create') }}" class="btn btn-primary btn-sm">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Path
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Level</th>
                        <th>Courses</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paths as $path)
                        <tr>
                            <td style="font-weight: 600;">{{ $path->title }}</td>
                            <td><span class="badge badge-muted">{{ ucfirst($path->level) }}</span></td>
                            <td style="font-family: 'JetBrains Mono', monospace;">{{ $path->courses_count }}</td>
                            <td>
                                @if($path->is_published)
                                    <span class="badge badge-accent">Published</span>
                                @else
                                    <span class="badge badge-warning">Draft</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="{{ route('admin.learning-paths.edit', $path) }}"
                                        class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.learning-paths.destroy', $path) }}"
                                        onsubmit="return confirm('Delete this learning path?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">No learning
                                paths yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection