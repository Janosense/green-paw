@extends('layouts.app')

@section('title', 'Roles — Green Paw LMS')
@section('page_title', 'Role Management')

@section('topbar_actions')
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Create Role
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Users</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td>
                                <span style="font-weight: 600;">{{ ucfirst($role->name) }}</span>
                                @if(in_array($role->name, ['super-admin', 'admin', 'instructor', 'student', 'guest']))
                                    <span class="badge badge-muted" style="margin-left: 6px; font-size: 10px;">DEFAULT</span>
                                @endif
                            </td>
                            <td>
                                @if($role->name === 'super-admin')
                                    <span class="badge badge-warning">All permissions</span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 13px;">{{ $role->permissions->count() }}
                                        permissions</span>
                                @endif
                            </td>
                            <td style="color: var(--text-secondary);">
                                <span style="font-family: 'JetBrains Mono', monospace;">{{ $role->users->count() }}</span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-secondary btn-sm">Edit</a>
                                    @if(!in_array($role->name, ['super-admin', 'admin', 'instructor', 'student', 'guest']))
                                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                            onsubmit="return confirm('Delete this role?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection