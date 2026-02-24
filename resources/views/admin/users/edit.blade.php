@extends('layouts.app')

@section('title', 'Edit User — Green Paw LMS')
@section('page_title', 'Edit User')

@section('content')
    <div style="max-width: 600px;">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf @method('PUT')

                    <div class="form-group">
                        <label class="form-label" for="name">Full name</label>
                        <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $user->name) }}"
                            required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email address</label>
                        <input type="email" name="email" id="email" class="form-input"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">New password <span
                                style="color: var(--text-muted); font-weight: 400;">(leave blank to keep
                                current)</span></label>
                        <input type="password" name="password" id="password" class="form-input">
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="role">Role</label>
                        <select name="role" id="role" class="form-input form-select" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        @error('role') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn btn-primary" style="width: auto;">Update User</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary" style="width: auto;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection