<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AdminUserSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['seeding.admin' => [
            'name' => 'CMS Administrator',
            'email' => 'ADMIN@example.com',
            'password' => 'SeederPassword123',
        ]]);
    }

    public function test_database_seeder_creates_an_administrator_who_can_login(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@example.com')->firstOrFail();
        $this->assertSame('admin', $user->role);
        $this->assertTrue(Hash::check('SeederPassword123', $user->password));
        $this->post('/login', ['email' => 'admin@example.com', 'password' => 'SeederPassword123'])
            ->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    public function test_reseeding_does_not_replace_existing_credentials_or_promote_users(): void
    {
        $user = User::factory()->create(['email' => 'admin@example.com', 'password' => 'OriginalPassword123']);
        config(['seeding.admin.password' => 'old-password']);
        $this->seed(AdminUserSeeder::class);
        $this->seed(AdminUserSeeder::class);
        $this->assertDatabaseCount('users', 1);
        $this->assertSame('user', $user->fresh()->role);
        $this->assertTrue(Hash::check('OriginalPassword123', $user->fresh()->password));
    }

    public function test_missing_credentials_do_not_create_a_default_account(): void
    {
        config(['seeding.admin.password' => null]);
        $this->seed(AdminUserSeeder::class);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_weak_password_is_rejected(): void
    {
        config(['seeding.admin.password' => 'password']);
        try {
            $this->seed(AdminUserSeeder::class);
            $this->fail('A weak password was accepted.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('password', $exception->errors());
        }
        $this->assertDatabaseCount('users', 0);
    }
}
