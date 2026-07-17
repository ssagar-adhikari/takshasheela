<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_admin_can_log_in_and_log_out(): void
    {
        $admin = User::factory()->create([
            'password' => 'correct-password',
            'role' => 'admin',
        ]);

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'correct-password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);

        $this->post('/admin/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_non_admin_cannot_log_in_to_backend(): void
    {
        $user = User::factory()->create([
            'password' => 'correct-password',
            'role' => 'viewer',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'correct-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
