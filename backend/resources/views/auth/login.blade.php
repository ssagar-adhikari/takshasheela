@extends('layouts.auth')
@section('title', 'Welcome back')
@section('description', 'Sign in to manage your website and its content.')
@section('content')
<form method="post" action="{{ route('login.store') }}" class="stack">
    @csrf
    <label>Email address<input type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus placeholder="you@example.com"></label>
    <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
    <div class="form-row"><label class="checkbox"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Remember me</label><a href="{{ route('password.request') }}">Forgot password?</a></div>
    <button class="button" type="submit">Sign in <span aria-hidden="true">→</span></button>
    <p class="form-note">Access is reserved for authorized administrators.</p>
</form>
@endsection
