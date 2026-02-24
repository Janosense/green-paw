@extends('layouts.app')

@section('title', 'Import Users — Green Paw LMS')
@section('page_title', 'Import Users via CSV')

@section('content')
    <div style="max-width: 600px;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Upload CSV</h3>
                <a href="{{ route('admin.users.import.template') }}" class="btn btn-secondary btn-sm">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Template
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.import.process') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="csv_file">CSV File</label>
                        <input type="file" name="csv_file" id="csv_file" class="form-input" accept=".csv,.txt" required
                            style="padding: 8px;">
                        @error('csv_file') <p class="form-error">{{ $message }}</p> @enderror
                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">Required columns: <code
                                style="color: var(--accent); font-family: 'JetBrains Mono', monospace; font-size: 11px;">name, email</code>.
                            Optional: <code
                                style="color: var(--accent); font-family: 'JetBrains Mono', monospace; font-size: 11px;">role, password</code>
                        </p>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="default_role">Default Role</label>
                        <select name="default_role" id="default_role" class="form-input form-select">
                            <option value="student" selected>Student</option>
                            <option value="instructor">Instructor</option>
                            <option value="admin">Admin</option>
                        </select>
                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px;">Used when a row doesn't
                            specify a role.</p>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: auto;">Import Users</button>
                </form>
            </div>
        </div>

        @if(session('import_errors'))
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">Import Errors</h3>
                    <span class="badge badge-danger">{{ session('skipped') }} skipped</span>
                </div>
                <div class="card-body">
                    @foreach(session('import_errors') as $error)
                        <p
                            style="font-size: 13px; color: var(--danger); margin-bottom: 4px; font-family: 'JetBrains Mono', monospace;">
                            {{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection