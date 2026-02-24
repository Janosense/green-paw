@extends('layouts.app')

@section('title', 'Edit Role — Green Paw LMS')
@section('page_title', 'Edit Role')

@section('content')
    <div style="max-width: 700px;">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                    @csrf @method('PUT')

                    <div class="form-group">
                        <label class="form-label" for="name">Role name</label>
                        <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $role->name) }}" required
                            {{ in_array($role->name, ['super-admin', 'admin', 'instructor', 'student', 'guest']) ? 'readonly' : '' }}>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                        @if(in_array($role->name, ['super-admin', 'admin', 'instructor', 'student', 'guest']))
                            <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Default roles cannot be renamed.</p>
                        @endif
                    </div>

                    @if($role->name !== 'super-admin')
                    <div class="form-group">
                        <label class="form-label">Permissions</label>
                        @foreach($permissions as $group => $perms)
                            <div style="margin-bottom: 16px;">
                                <p style="font-size: 13px; font-weight: 600; color: var(--accent); margin-bottom: 8px; text-transform: capitalize;">{{ $group }}</p>
                                <div class="checkbox-grid">
                                    @foreach($perms as $perm)
                                        <div class="checkbox-item">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_{{ $perm->id }}"
                                                {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                                            <label for="perm_{{ $perm->id }}">{{ $perm->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-success" style="margin-bottom: 20px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        This role automatically has all permissions.
                    </div>
                    @endif

                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn btn-primary" style="width: auto;">Update Role</button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary" style="width: auto;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
