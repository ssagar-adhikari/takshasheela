@extends('layouts.admin')
@section('title', 'My account')
@section('content')
<div class="page-heading"><div><p class="eyebrow">YOUR PERSONAL SPACE</p><h1>My account</h1><p class="muted">Keep your profile accurate and your account secure.</p></div></div>
<form method="post" action="{{ route('admin.profile.update') }}" class="panel form-panel settings-form">@csrf @method('PUT')
<h2>Profile details</h2><label>Full name<input name="name" value="{{ old('name', auth()->user()->name) }}" autocomplete="name" maxlength="255" required></label><label>Email address<input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" autocomplete="email" maxlength="255" required></label><hr><h2>Security</h2><label>Current password<input type="password" name="current_password" autocomplete="current-password" required><small>Required to confirm account changes.</small></label><label>New password<input type="password" name="password" autocomplete="new-password" minlength="12"><small>Leave blank to keep your password. Otherwise use at least 12 characters with letters and numbers.</small></label><label>Confirm new password<input type="password" name="password_confirmation" autocomplete="new-password" minlength="12"></label><div><button class="button">Save account →</button></div></form>
@endsection
