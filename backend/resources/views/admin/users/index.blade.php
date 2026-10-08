@extends('layouts.admin')
@section('title', 'Users')
@section('content')
<div class="page-heading">
    <div><p class="eyebrow">ACCOUNT MANAGEMENT</p><h1>Users</h1><p class="muted">Manage accounts and who can access the administration panel.</p></div>
    <a class="button" href="{{ route('admin.users.create') }}">+ Add user</a>
</div>
<section class="panel">
    <form method="get" action="{{ route('admin.users.index') }}" class="filter-bar user-filters">
        <label class="search-field"><span class="sr-only">Search users</span><input type="search" name="search" value="{{ request('search') }}" placeholder="Search name or email" maxlength="255"></label>
        <label><span class="sr-only">Role</span><select name="role"><option value="">All roles</option><option value="admin" @selected(request('role') === 'admin')>Administrator</option><option value="user" @selected(request('role') === 'user')>User</option></select></label>
        <label><span class="sr-only">Status</span><select name="status"><option value="">All statuses</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="inactive" @selected(request('status') === 'inactive')>Inactive</option></select></label>
        <button class="button" type="submit">Filter</button>
        @if(request()->filled('search') || request()->filled('role') || request()->filled('status'))<a href="{{ route('admin.users.index') }}">Clear</a>@endif
    </form>
    <div class="panel-heading"><div><h2>Accounts</h2><p class="muted">{{ $users->total() }} {{ Str::plural('user', $users->total()) }} found. Only active administrators can sign in to the CMS.</p></div></div>
    @if($users->isEmpty())
        <div class="empty-state"><h3>No matching users</h3><p>Try another name, email, or filter.</p></div>
    @else
        <div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th><span class="sr-only">Actions</span></th></tr></thead><tbody>
        @foreach($users as $user)
            <tr>
                <td><span class="table-title">{{ $user->name }}</span>@if($user->is(auth()->user()))<small>Your account</small>@endif</td>
                <td>{{ $user->email }}</td><td>{{ $user->role === 'admin' ? 'Administrator' : 'User' }}</td>
                <td><span class="badge {{ $user->is_active ? 'badge-published' : 'badge-draft' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td>
                <td>{{ $user->created_at?->format('M j, Y') }}</td>
                <td><div class="table-actions">
                    @if($user->is(auth()->user()))
                        <a href="{{ route('admin.profile.edit') }}">My account</a>
                    @else
                        <a href="{{ route('admin.users.edit', $user) }}">Edit<span class="sr-only"> {{ $user->name }}</span></a>
                        <form method="post" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Permanently delete this account? Their website content will be kept. To temporarily stop access, edit the account and choose Inactive.')">@csrf @method('DELETE')<button class="text-danger" type="submit">Delete<span class="sr-only"> {{ $user->name }}</span></button></form>
                    @endif
                </div></td>
            </tr>
        @endforeach
        </tbody></table></div>
        @if($users->hasPages())<div class="pagination">{{ $users->links() }}</div>@endif
    @endif
</section>
@endsection
