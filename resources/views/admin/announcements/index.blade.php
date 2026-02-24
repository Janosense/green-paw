@extends('layouts.app')

@section('title', 'Announcements — Admin')
@section('page_title', 'Announcements')

@section('topbar_actions')
    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        New Announcement
    </a>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 16px;">✅ {{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Scope</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $a)
                        <tr>
                            <td style="font-weight: 600;">{{ $a->title }}</td>
                            <td>
                                @if($a->course)
                                    <span style="color: var(--text-secondary);">{{ $a->course->title }}</span>
                                @else
                                    <span class="badge badge-accent">Global</span>
                                @endif
                            </td>
                            <td>{{ $a->author->name }}</td>
                            <td>
                                @if($a->isPublished())
                                    <span class="badge badge-accent">Published</span>
                                @else
                                    <span class="badge badge-warning">Draft</span>
                                @endif
                            </td>
                            <td style="font-size: 13px; color: var(--text-muted);">{{ $a->created_at->format('M d, Y') }}</td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 6px;">
                                    <a href="{{ route('admin.announcements.edit', $a) }}" class="btn btn-secondary btn-sm"
                                        style="padding: 4px 10px;">Edit</a>
                                    <form method="POST" action="{{ route('admin.announcements.destroy', $a) }}"
                                        onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            style="padding: 4px 10px;">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">No
                                announcements yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $announcements->links() }}
@endsection