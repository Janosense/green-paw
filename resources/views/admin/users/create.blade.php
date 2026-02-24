@extends('layouts.app')

@section('title', 'Create User — Green Paw LMS')
@section('page_title', 'Create User')

@section('content')
    <div style="max-width: 600px;">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="name">Full name</label>
                        <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email address</label>
                        <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}" required>
                        @error('email') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-input" required>
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="role">Role</label>
                        <select name="role" id="role" class="form-input form-select" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        @error('role') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn btn-primary" style="width: auto;">Create User</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary" style="width: auto;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection