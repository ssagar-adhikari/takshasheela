@csrf
@if(isset($user)) @method('PUT') @endif
<div class="field">
    <label for="name">Name</label>
    <input id="name" name="name" type="text" value="{{ old('name', $user->name ?? '') }}" required autofocus autocomplete="name">
    @error('name') <p class="field-error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required autocomplete="email">
    @error('email') <p class="field-error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="password">Password @isset($user)<span class="muted">(leave blank to keep current password)</span>@endisset</label>
    <input id="password" name="password" type="password" @empty($user) required @endempty autocomplete="new-password">
    @error('password') <p class="field-error">{{ $message }}</p> @enderror
</div>
<div class="field">
    <label for="password_confirmation">Confirm password</label>
    <input id="password_confirmation" name="password_confirmation" type="password" @empty($user) required @endempty autocomplete="new-password">
</div>
<div class="row" style="justify-content:flex-start">
    <button class="button" type="submit">{{ isset($user) ? 'Save changes' : 'Create user' }}</button>
    <a class="button secondary" href="{{ route('admin.users.index') }}">Cancel</a>
</div>
