<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class CreateAdministrator extends Command
{
    protected $signature = 'cms:create-admin {email?} {--name= : Administrator display name}';

    protected $description = 'Create an administrator with a securely prompted password';

    public function handle(): int
    {
        $email = Str::lower($this->argument('email') ?? $this->ask('Email address'));
        $name = $this->option('name') ?? $this->ask('Name');
        $password = $this->secret('Password (at least 12 characters, including letters and numbers)');
        $confirmation = $this->secret('Confirm password');
        $validator = Validator::make(['name' => $name, 'email' => $email, 'password' => $password, 'password_confirmation' => $confirmation], [
            'name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->numbers()],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }
        $user = new User(['name' => $name, 'email' => $email, 'password' => $password]);
        $user->role = 'admin';
        $user->save();
        $this->info('Administrator created: '.$email);

        return self::SUCCESS;
    }
}
