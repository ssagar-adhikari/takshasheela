@extends('layouts.admin')
@php($editing = $user->exists)
@section('title', $editing ? 'Edit user' : 'Add user')
@section('content')
<div class="page-heading"><div><p class="eyebrow">ACCOUNT MANAGEMENT</p><h1>{{ $editing ? 'Edit user' : 'Add user' }}</h1><p class="muted">{{ $editing ? 'Update profile details, password, and access.' : 'Create an account for someone on your team.' }}</p></div><a class="button button-outline" href="{{ route('admin.users.index') }}">← Users</a></div>
<form method="post" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" class="panel form-panel settings-form">
    @csrf @if($editing) @method('PUT') @endif
    <h2>Profile details</h2>
    <label>Full name<input name="name" value="{{ old('name', $user->name) }}" autocomplete="off" maxlength="255" required></label>
    <label>Email address<input type="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="off" maxlength="255" required></label>
    <div class="form-columns">
        <label>Role<select name="role" required><option value="admin" @selected(old('role', $user->role) === 'admin')>Administrator</option><option value="user" @selected(old('role', $user->role) === 'user')>User</option></select><small>Administrators can manage all content, settings, and users. Users have no CMS access.</small></label>
        <label>Status<select name="is_active" required><option value="1" @selected((string) old('is_active', (int) $user->is_active) === '1')>Active</option><option value="0" @selected((string) old('is_active', (int) $user->is_active) === '0')>Inactive</option></select><small>Inactive accounts cannot sign in to the CMS.</small></label>
    </div>
    <hr><h2>{{ $editing ? 'Change password' : 'Password' }}</h2>
    <label>{{ $editing ? 'New password' : 'Password' }}<input type="password" name="password" autocomplete="new-password" minlength="12" @required(!$editing)><small>{{ $editing ? 'Leave blank to keep the existing password. ' : '' }}Use at least 12 characters including letters and numbers.</small></label>
    <label>Confirm password<input type="password" name="password_confirmation" autocomplete="new-password" minlength="12" @required(!$editing)></label>
    <div class="about-save-actions"><a class="button button-outline" href="{{ route('admin.users.index') }}">Cancel</a><button class="button" type="submit">{{ $editing ? 'Save user' : 'Create user' }} →</button></div>
</form>
@endsection
