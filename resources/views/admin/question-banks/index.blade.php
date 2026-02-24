@extends('layouts.app')

@section('title', 'Question Banks — Green Paw LMS')
@section('page_title', 'Question Banks')

@section('topbar_actions')
    <a href="{{ route('admin.question-banks.create') }}" class="btn btn-primary btn-sm">+ New Bank</a>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Questions</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banks as $bank)
                        <tr>
                            <td style="font-weight: 600;">{{ $bank->name }}</td>
                            <td>{{ $bank->course?->title ?? '—' }}</td>
                            <td style="font-family: 'JetBrains Mono', monospace;">{{ $bank->items_count }}</td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="{{ route('admin.question-banks.edit', $bank) }}"
                                        class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.question-banks.destroy', $bank) }}"
                                        onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted);">No question
                                banks yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection