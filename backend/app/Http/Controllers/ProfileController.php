<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $request->merge(['email' => Str::lower((string) $request->input('email'))]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($request->user()->id)],
            'current_password' => ['required', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(12)->letters()->numbers()],
        ]);
        $request->user()->fill(['name' => $data['name'], 'email' => $data['email']]);
        if (! empty($data['password'])) {
            $request->user()->password = $data['password'];
            $request->user()->setRememberToken(Str::random(60));
        }
        $request->user()->save();
        $request->session()->regenerate();

        return back()->with('status', 'Your account has been updated.');
    }
}
