<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_change_password_page(): void
    {
        $this->get(route('admin.password.edit'))->assertRedirect(route('login'));
    }

    public function test_admin_can_open_change_password_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.password.edit'))
            ->assertOk()
            ->assertSee('Current password');
    }

    public function test_current_password_must_be_correct(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'current-password',
        ]);

        $this->actingAs($admin)->put(route('admin.password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('current-password', $admin->refresh()->password));
    }

    public function test_admin_can_change_their_password(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'current-password',
        ]);

        $this->actingAs($admin)->put(route('admin.password.update'), [
            'current_password' => 'current-password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertSessionHas('success');

        $this->assertAuthenticatedAs($admin);
        $this->assertTrue(Hash::check('new-secure-password', $admin->refresh()->password));
    }
}
