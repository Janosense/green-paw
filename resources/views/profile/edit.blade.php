@extends('layouts.app')

@section('title', 'Edit Profile — Green Paw LMS')
@section('page_title', 'Edit Profile')

@section('content')
    <div style="max-width: 600px;">
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3 class="card-title">Avatar</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data"
                    style="display: flex; align-items: center; gap: 20px;">
                    @csrf
                    <div class="user-avatar" style="width: 64px; height: 64px; font-size: 24px; flex-shrink: 0;">
                        @if($user->avatar)
                            <img src="{{ Storage::url($user->avatar) }}" alt="">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div style="flex: 1;">
                        <input type="file" name="avatar" accept="image/*" class="form-input" style="padding: 8px;">
                        @error('avatar') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="btn btn-secondary btn-sm">Upload</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Profile Information</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label" for="name">Full name</label>
                        <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $user->name) }}"
                            required>
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="bio">Bio</label>
                        <textarea name="bio" id="bio" class="form-input form-textarea"
                            placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="timezone">Timezone</label>
                        <select name="timezone" id="timezone" class="form-input form-select">
                            @foreach(timezone_identifiers_list() as $tz)
                                <option value="{{ $tz }}" {{ old('timezone', $user->timezone) === $tz ? 'selected' : '' }}>
                                    {{ $tz }}</option>
                            @endforeach
                        </select>
                        @error('timezone') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn btn-primary" style="width: auto;">Save Changes</button>
                        <a href="{{ route('profile.show') }}" class="btn btn-secondary" style="width: auto;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection