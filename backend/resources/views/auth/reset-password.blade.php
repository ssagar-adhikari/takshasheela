@extends('layouts.auth')
@section('title', 'Choose a new password')
@section('description', 'Use at least 12 characters, including letters and numbers.')
@section('content')
<form method="post" action="{{ route('password.update') }}" class="stack">@csrf
<input type="hidden" name="token" value="{{ $token }}">
<label>Email address<input type="email" name="email" value="{{ old('email', request('email')) }}" autocomplete="email" required></label>
<label>New password<input type="password" name="password" minlength="12" autocomplete="new-password" required></label>
<label>Confirm password<input type="password" name="password_confirmation" minlength="12" autocomplete="new-password" required></label>
<button class="button">Reset password →</button>
</form>
@endsection
