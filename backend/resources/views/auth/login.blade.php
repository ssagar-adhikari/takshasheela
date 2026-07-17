@extends('layouts.admin')

@section('title', 'Admin login')

@section('content')
<div class="card auth-card">
    <h1>Admin login</h1>
    <p class="muted">Sign in to manage backend users.</p>
    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="current-password">
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="checkbox">
            <input id="remember" name="remember" type="checkbox" value="1">
            <label for="remember">Remember me</label>
        </div>
        <button class="button" type="submit">Sign in</button>
    </form>
</div>
@endsection
