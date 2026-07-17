@extends('layouts.admin')

@section('title', 'Change password')
@section('heading', 'Change password')
@section('subheading', 'Update the password for your administrator account.')

@section('content')
<div class="card form-card">
    <form method="POST" action="{{ route('admin.password.update') }}">
        @csrf
        @method('PUT')

        <div class="field">
            <label for="current_password">Current password</label>
            <input id="current_password" name="current_password" type="password" required autofocus autocomplete="current-password">
            @error('current_password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label for="password">New password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password">
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm new password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
        </div>

        <button class="button" type="submit">Change password</button>
    </form>
</div>
@endsection
