<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $credentials = config('seeding.admin');

        if (blank($credentials['email']) || blank($credentials['password'])) {
            $this->command?->warn('Administrator not seeded: set ADMIN_EMAIL and ADMIN_PASSWORD in .env.');

            return;
        }

        $credentials['email'] = Str::lower(trim($credentials['email']));

        Validator::make($credentials, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ])->validate();

        if (User::where('email', $credentials['email'])->exists()) {
            $this->command?->warn('Account already exists; its password and role were left unchanged: '.$credentials['email']);

            return;
        }

        Validator::make($credentials, [
            'password' => ['required', 'string', Password::min(12)->letters()->numbers()],
        ])->validate();

        // The User model hashes the password through its hashed cast.
        $user = new User($credentials);
        $user->role = 'admin';
        $user->save();

        $this->command?->info('Administrator seeded: '.$user->email);
    }
}
