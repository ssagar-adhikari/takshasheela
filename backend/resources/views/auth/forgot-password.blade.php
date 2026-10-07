@extends('layouts.auth')
@section('title', 'Reset your password')
@section('description', 'Enter your administrator email to receive a reset link.')
@section('content')
<form method="post" action="{{ route('password.email') }}" class="stack">@csrf
<label>Email address<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></label>
<button class="button">Send reset link →</button><a href="{{ route('login') }}">Return to sign in</a>
</form>
@endsection
