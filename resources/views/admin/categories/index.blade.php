@extends('layouts.app')

@section('title', 'Categories — Green Paw LMS')
@section('page_title', 'Course Categories')

@section('topbar_actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Category
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Slug</th>
                        <th>Courses</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td style="font-weight: 600;">{{ $category->name }}</td>
                            <td><code
                                    style="color: var(--accent); font-family: 'JetBrains Mono', monospace; font-size: 13px;">{{ $category->slug }}</code>
                            </td>
                            <td style="font-family: 'JetBrains Mono', monospace;">{{ $category->courses_count }}</td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                        class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                        onsubmit="return confirm('Delete this category?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @foreach($category->children as $child)
                            <tr>
                                <td style="padding-left: 40px; color: var(--text-secondary);">↳ {{ $child->name }}</td>
                                <td><code
                                        style="color: var(--accent); font-family: 'JetBrains Mono', monospace; font-size: 13px;">{{ $child->slug }}</code>
                                </td>
                                <td style="font-family: 'JetBrains Mono', monospace;">—</td>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <a href="{{ route('admin.categories.edit', $child) }}"
                                            class="btn btn-secondary btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $child) }}"
                                            onsubmit="return confirm('Delete?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection