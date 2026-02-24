@extends('layouts.app')

@section('title', 'Create Role — Green Paw LMS')
@section('page_title', 'Create Role')

@section('content')
    <div style="max-width: 700px;">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.roles.store') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="name">Role name</label>
                        <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required placeholder="e.g. content-manager">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Permissions</label>
                        @foreach($permissions as $group => $perms)
                            <div style="margin-bottom: 16px;">
                                <p style="font-size: 13px; font-weight: 600; color: var(--accent); margin-bottom: 8px; text-transform: capitalize;">{{ $group }}</p>
                                <div class="checkbox-grid">
                                    @foreach($perms as $perm)
                                        <div class="checkbox-item">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_{{ $perm->id }}"
                                                {{ in_array($perm->name, old('permissions', [])) ? 'checked' : '' }}>
                                            <label for="perm_{{ $perm->id }}">{{ $perm->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn btn-primary" style="width: auto;">Create Role</button>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary" style="width: auto;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
