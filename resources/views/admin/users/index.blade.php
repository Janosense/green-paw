@extends('layouts.app')

@section('title', 'Users — Green Paw LMS')
@section('page_title', 'User Management')

@section('topbar_actions')
    <a href="{{ route('admin.users.import') }}" class="btn btn-secondary btn-sm">Import CSV</a>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add User
    </a>
@endsection

@section('content')
    <!-- Filters -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body" style="padding: 16px 24px;">
            <form method="GET" action="{{ route('admin.users.index') }}"
                style="display: flex; gap: 12px; align-items: center;">
                <input type="text" name="search" class="form-input" placeholder="Search users..."
                    value="{{ request('search') }}" style="max-width: 300px;">
                <select name="role" class="form-input form-select" style="max-width: 180px;">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                @if(request('search') || request('role'))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm"
                        style="color: var(--text-muted);">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Users table -->
    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Provider</th>
                        <th>Joined</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="user-avatar" style="width: 32px; height: 32px; font-size: 13px;">
                                        @if($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}" alt="">
                                        @else
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <span style="font-weight: 600;">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td style="color: var(--text-secondary);">{{ $user->email }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge badge-accent">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td style="color: var(--text-muted);">{{ $user->provider ? ucfirst($user->provider) : 'Email' }}
                            </td>
                            <td style="color: var(--text-muted);">{{ $user->created_at->format('M d, Y') }}</td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                            onsubmit="return confirm('Delete this user?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="pagination">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection