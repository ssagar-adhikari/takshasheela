<?php

namespace Tests\Feature;

use App\Models\AboutSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(array $attributes = []): User
    {
        return User::factory()->create(['role' => 'admin', 'password' => 'OriginalPassword123', ...$attributes]);
    }

    private function data(array $overrides = []): array
    {
        return [...[
            'name' => 'New Administrator', 'email' => 'new@example.com', 'role' => 'admin', 'is_active' => '1',
            'password' => 'NewSecurePassword123', 'password_confirmation' => 'NewSecurePassword123',
        ], ...$overrides];
    }

    public function test_all_management_endpoints_require_an_active_administrator(): void
    {
        $target = $this->admin();
        $requests = [['get', '/admin/users'], ['get', '/admin/users/create'], ['get', '/admin/users/'.$target->id.'/edit'],
            ['post', '/admin/users'], ['put', '/admin/users/'.$target->id], ['delete', '/admin/users/'.$target->id]];
        foreach ($requests as [$method, $url]) {
            $this->{$method}($url)->assertRedirect('/login');
        }
        foreach ([User::factory()->create(), $this->admin(['is_active' => false])] as $actor) {
            $this->asAccount($actor);
            foreach ($requests as [$method, $url]) {
                $this->{$method}($url)->assertForbidden();
            }
        }
        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    public function test_admin_can_list_search_filter_and_paginate_users(): void
    {
        $this->asAccount($this->admin(['name' => 'Owner']));
        $inactive = User::factory()->create(['name' => 'Search Person', 'email' => 'find@example.com', 'is_active' => false]);
        User::factory()->count(16)->create();
        $this->get('/admin/users')->assertOk()->assertSee('Add user')->assertSee('My account')
            ->assertViewHas('users', fn ($users) => $users->total() === 18 && $users->count() === 15);
        $this->get('/admin/users?page=2')->assertOk()->assertViewHas('users', fn ($users) => $users->count() === 3);
        $this->get('/admin/users?search=find%40example.com&role=user&status=inactive')->assertOk()
            ->assertViewHas('users', fn ($users) => $users->count() === 1 && $users->first()->is($inactive));
        $this->get('/admin/users?search=not-a-real-user')->assertOk()->assertSee('No matching users');
        $this->get('/admin/users/create')->assertOk()->assertSee('Confirm password');
        $this->get('/admin')->assertOk()->assertSee(route('admin.users.index'), false);
    }

    public function test_creating_an_account_normalizes_email_and_hashes_password(): void
    {
        $this->asAccount($this->admin())->post('/admin/users', $this->data(['email' => 'NEW@EXAMPLE.COM', 'remember_token' => 'injected']))
            ->assertRedirect('/admin/users')->assertSessionHasNoErrors();
        $user = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertSame('admin', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check('NewSecurePassword123', $user->password));
        $this->assertNull($user->remember_token);
        $this->post('/logout');
        $this->post('/login', ['email' => $user->email, 'password' => 'NewSecurePassword123'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_accounts_and_duplicate_emails_are_rejected_without_flashing_passwords(): void
    {
        $actor = $this->admin(['email' => 'owner@example.com']);
        $this->asAccount($actor)->post('/admin/users', $this->data([
            'name' => '', 'email' => 'OWNER@EXAMPLE.COM', 'role' => 'superadmin', 'is_active' => 'invalid',
            'password' => 'short', 'password_confirmation' => 'different',
        ]))->assertSessionHasErrors(['name', 'email', 'role', 'is_active', 'password']);
        $this->assertDatabaseCount('users', 1);
        $this->assertNull(session()->getOldInput('password'));
        $this->assertNull(session()->getOldInput('password_confirmation'));
        $this->post('/admin/users', $this->data(['password' => '', 'password_confirmation' => '']))->assertSessionHasErrors('password');
    }

    public function test_editing_without_a_password_preserves_it_and_ignores_unrecognized_fields(): void
    {
        $actor = $this->admin();
        $target = $this->admin(['email' => 'target@example.com']);
        $hash = $target->password;
        $this->asAccount($actor)->get('/admin/users/'.$target->id.'/edit')->assertOk()->assertDontSee($hash)->assertDontSee('OriginalPassword123');
        $this->put('/admin/users/'.$target->id, $this->data([
            'name' => 'Updated Name', 'email' => $target->email, 'password' => '', 'password_confirmation' => '',
            'email_verified_at' => '2020-01-01', 'remember_token' => 'injected',
        ]))->assertRedirect('/admin/users')->assertSessionHasNoErrors();
        $this->assertSame($hash, $target->fresh()->password);
        $this->assertSame('Updated Name', $target->fresh()->name);
        $this->assertNotSame('injected', $target->fresh()->remember_token);
    }

    public function test_password_changes_revoke_reset_tokens_and_database_sessions(): void
    {
        $actor = $this->admin();
        $target = $this->admin(['email' => 'target@example.com', 'remember_token' => 'old-remember-token']);
        $token = Password::createToken($target);
        $this->sessionRow($target, 'target-session');
        $this->sessionRow($actor, 'actor-session');
        config(['session.driver' => 'database']);
        $this->asAccount($actor)->put('/admin/users/'.$target->id, $this->data(['email' => $target->email]))
            ->assertRedirect('/admin/users')->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewSecurePassword123', $target->fresh()->password));
        $this->assertNotSame('old-remember-token', $target->fresh()->remember_token);
        $this->assertFalse(Password::tokenExists($target->fresh(), $token));
        $this->assertDatabaseMissing('sessions', ['id' => 'target-session']);
        $this->assertDatabaseHas('sessions', ['id' => 'actor-session']);
    }

    public function test_email_changes_clear_verification_and_old_reset_tokens(): void
    {
        $actor = $this->admin();
        $target = $this->admin(['email' => 'previous@example.com', 'email_verified_at' => now()]);
        Password::createToken($target);
        $this->asAccount($actor)->put('/admin/users/'.$target->id, $this->data(['email' => 'CHANGED@EXAMPLE.COM', 'password' => null]))
            ->assertSessionHasNoErrors();
        $this->assertSame('changed@example.com', $target->fresh()->email);
        $this->assertNull($target->fresh()->email_verified_at);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'previous@example.com']);
        $this->put('/admin/users/'.$target->id, $this->data(['email' => $actor->email]))->assertSessionHasErrors('email');
    }

    public function test_admin_can_deactivate_reactivate_and_change_roles(): void
    {
        $actor = $this->admin();
        $target = $this->admin(['email' => 'target@example.com']);
        $data = $this->data(['email' => $target->email, 'password' => null]);
        $this->asAccount($actor)->put('/admin/users/'.$target->id, [...$data, 'is_active' => '0'])->assertSessionHasNoErrors();
        $this->assertFalse($target->fresh()->is_active);
        $this->asAccount($target->fresh())->get('/admin')->assertForbidden();
        $this->asAccount($actor)->put('/admin/users/'.$target->id, $data)->assertSessionHasNoErrors();
        $this->assertTrue($target->fresh()->is_active);
        $this->put('/admin/users/'.$target->id, [...$data, 'role' => 'user'])->assertSessionHasNoErrors();
        $this->asAccount($target->fresh())->get('/admin')->assertForbidden();
        $this->asAccount($actor)->put('/admin/users/'.$target->id, $data)->assertSessionHasNoErrors();
        $this->asAccount($target->fresh())->get('/admin')->assertOk();
    }

    public function test_inactive_accounts_cannot_login_or_reset_passwords(): void
    {
        Notification::fake();
        $target = $this->admin(['is_active' => false]);
        $token = Password::createToken($target);
        $this->post('/login', ['email' => $target->email, 'password' => 'OriginalPassword123'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post('/forgot-password', ['email' => $target->email])->assertSessionHas('status');
        Notification::assertNothingSent();
        $this->post('/reset-password', ['email' => $target->email, 'token' => $token,
            'password' => 'ChangedPassword123', 'password_confirmation' => 'ChangedPassword123'])->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('OriginalPassword123', $target->fresh()->password));
    }

    public function test_own_account_uses_profile_and_cannot_be_changed_or_deleted_through_user_management(): void
    {
        $actor = $this->admin();
        $this->admin();
        $this->asAccount($actor)->get('/admin/users/'.$actor->id.'/edit')->assertRedirect('/admin/profile');
        $this->put('/admin/users/'.$actor->id, $this->data(['email' => $actor->email]))->assertSessionHasErrors('user');
        $this->delete('/admin/users/'.$actor->id)->assertSessionHasErrors('user');
        $this->put('/admin/users/'.$actor->id, $this->data(['email' => $actor->email, 'is_active' => '0']))->assertSessionHasErrors('user');
        $this->assertTrue($actor->fresh()->is_active);
        $this->assertTrue(Hash::check('OriginalPassword123', $actor->fresh()->password));
    }

    public function test_last_active_admin_is_protected_even_if_inactive_admins_exist(): void
    {
        $actor = $this->admin();
        $this->admin(['is_active' => false]);
        $message = 'The last active administrator cannot be deleted, deactivated, or changed to a user.';
        $this->asAccount($actor)->delete('/admin/users/'.$actor->id)->assertSessionHasErrors(['user' => $message]);
        foreach ([['is_active' => '0'], ['role' => 'user']] as $change) {
            $this->put('/admin/users/'.$actor->id, $this->data(['email' => $actor->email, ...$change]))
                ->assertSessionHasErrors(['user' => $message]);
        }
        $this->assertDatabaseHas('users', ['id' => $actor->id, 'is_active' => true, 'role' => 'admin']);
    }

    public function test_deleting_other_users_preserves_their_content_and_removes_sessions_and_reset_tokens(): void
    {
        $actor = $this->admin();
        $target = $this->admin();
        $section = AboutSection::create(['slug' => 'about-takshasheela', 'content' => ['hero_title' => 'Keep this content'], 'updated_by' => $target->id]);
        Password::createToken($target);
        $this->sessionRow($target, 'deleted-session');
        config(['session.driver' => 'database']);
        $this->asAccount($actor)->delete('/admin/users/'.$target->id)->assertRedirect('/admin/users')->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $target->email]);
        $this->assertDatabaseMissing('sessions', ['id' => 'deleted-session']);
        $this->assertNull($section->fresh()->updated_by);
        $this->assertSame('Keep this content', $section->fresh()->content['hero_title']);
        $this->assertDatabaseHas('users', ['id' => $actor->id]);
    }

    private function asAccount(User $user): static
    {
        // Each simulated browser account needs its own authenticated-session hash.
        return $this->withSession(['password_hash_web' => $user->getAuthPassword()])->actingAs($user);
    }

    private function sessionRow(User $user, string $id): void
    {
        DB::table('sessions')->insert(['id' => $id, 'user_id' => $user->id, 'payload' => '', 'last_activity' => time()]);
    }
}
