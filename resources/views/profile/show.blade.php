@extends('layouts.app')

@section('title', 'Profile — Green Paw LMS')
@section('page_title', 'My Profile')

@section('topbar_actions')
    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">Edit Profile</a>
@endsection

@section('content')
    <div class="grid-2">
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 40px;">
                <div class="user-avatar" style="width: 80px; height: 80px; font-size: 32px; margin: 0 auto 16px;">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 4px;">{{ $user->name }}</h2>
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 12px;">{{ $user->email }}</p>
                <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
                    @foreach($user->roles as $role)
                        <span class="badge badge-accent">{{ $role->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Details</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; gap: 16px;">
                    <div>
                        <div class="stat-label">Bio</div>
                        <p style="color: var(--text-secondary); font-size: 14px;">{{ $user->bio ?? 'No bio set' }}</p>
                    </div>
                    <div>
                        <div class="stat-label">Timezone</div>
                        <p style="color: var(--text-secondary); font-size: 14px;">{{ $user->timezone }}</p>
                    </div>
                    <div>
                        <div class="stat-label">Authentication</div>
                        <p style="color: var(--text-secondary); font-size: 14px;">
                            {{ $user->provider ? ucfirst($user->provider) : 'Email' }}</p>
                    </div>
                    <div>
                        <div class="stat-label">Member Since</div>
                        <p style="color: var(--text-secondary); font-size: 14px;">{{ $user->created_at->format('M d, Y') }}
                        </p>
                    </div>
                    @if($user->tenant)
                        <div>
                            <div class="stat-label">Organization</div>
                            <p style="color: var(--text-secondary); font-size: 14px;">{{ $user->tenant->name }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection