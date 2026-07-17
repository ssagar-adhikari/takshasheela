@extends('layouts.admin')

@section('title', 'Users')
@section('heading', 'Users')
@section('subheading', 'All backend accounts have the administrator role.')

@section('content')
<div class="card">
    <div class="row" style="margin-bottom:14px">
        <h2>Administrator accounts</h2>
        <a class="button" href="{{ route('admin.users.create') }}">Add user</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Created</th><th></th></tr></thead>
            <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->name }} @if($user->is(auth()->user())) <span class="muted">(you)</span> @endif</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge">Admin</span></td>
                    <td>{{ $user->created_at->format('M j, Y') }}</td>
                    <td>
                        <div class="actions">
                            <a class="button secondary small" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                            @unless($user->is(auth()->user()))
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
                                    @csrf @method('DELETE')
                                    <button class="button danger small" type="submit">Delete</button>
                                </form>
                            @endunless
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">No users found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $users->links() }}</div>
</div>
@endsection
