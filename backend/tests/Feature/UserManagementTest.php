<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_user(): void
    {
        $this->actingAs($this->admin())->post(route('admin.users.store'), [
            'name' => 'Second Admin',
            'email' => 'second@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertRedirect(route('admin.users.index'));

        $user = User::whereEmail('second@example.com')->firstOrFail();

        $this->assertSame('admin', $user->role);
        $this->assertTrue(Hash::check('secure-password', $user->password));
    }

    public function test_admin_can_update_user_without_replacing_password(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'existing-password',
        ]);

        $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertSame('Updated Name', $user->name);
        $this->assertTrue(Hash::check('existing-password', $user->password));
    }

    public function test_admin_can_delete_another_user_but_not_self(): void
    {
        $admin = $this->admin();
        $other = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $other))
            ->assertRedirect(route('admin.users.index'));

        $this->assertModelMissing($other);

        $this->delete(route('admin.users.destroy', $admin))
            ->assertSessionHas('error');

        $this->assertModelExists($admin);
    }
}
