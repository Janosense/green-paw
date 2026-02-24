@extends('layouts.app')

@section('title', 'Dashboard — Green Paw LMS')
@section('page_title', 'Dashboard')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Welcome back</div>
            <div class="stat-value" style="font-size: 22px; font-family: 'Plus Jakarta Sans', sans-serif;">
                {{ auth()->user()->name }}</div>
            <div class="stat-change">{{ auth()->user()->roles->pluck('name')->first() ?? 'User' }}</div>
        </div>

        @if(auth()->user()->hasAnyRole(['super-admin', 'admin']))
            <div class="stat-card">
                <div class="stat-label">Total Users</div>
                <div class="stat-value">{{ \App\Models\User::count() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Active Roles</div>
                <div class="stat-value">{{ \Spatie\Permission\Models\Role::count() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Permissions</div>
                <div class="stat-value">{{ \Spatie\Permission\Models\Permission::count() }}</div>
            </div>
        @endif
    </div>

    @if(auth()->user()->hasAnyRole(['super-admin', 'admin']))
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Quick Actions</h2>
            </div>
            <div class="card-body">
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add User
                    </a>
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-outline-accent btn-sm">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create Role
                    </a>
                    <a href="{{ route('admin.users.import') }}" class="btn btn-secondary btn-sm">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Import Users
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Getting Started</h2>
            </div>
            <div class="card-body">
                <p style="color: var(--text-secondary); line-height: 1.8;">
                    Welcome to <strong>GreenPaw LMS</strong>! Your learning journey starts here.
                    Browse the course catalog, track your progress, and earn certificates as you complete courses.
                </p>
            </div>
        </div>
    @endif
@endsection