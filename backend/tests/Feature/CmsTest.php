<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DemoSiteSettingsSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CmsTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $user = User::factory()->create(['password' => 'CorrectPassword123']);
        $user->forceFill(['role' => 'admin'])->save();

        return $user;
    }

    public function test_guests_are_redirected_to_login_and_public_registration_is_unavailable(): void
    {
        $this->get('/')->assertOk()->assertSee('A journey awaits.');
        foreach (['/admin', '/admin/settings', '/admin/profile'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
        $this->get('/login')->assertOk()->assertSee('Welcome back');
        $this->get('/register')->assertNotFound();
    }

    public function test_administrators_can_login_and_logout(): void
    {
        $user = $this->administrator();
        $this->post('/login', ['email' => $user->email, 'password' => 'CorrectPassword123', 'remember' => 1])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
        $this->get('/admin')->assertOk()->assertSee('Welcome back')->assertHeader('Cache-Control', 'no-store, private');
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_invalid_credentials_and_regular_users_cannot_login(): void
    {
        $admin = $this->administrator();
        $this->post('/login', ['email' => $admin->email, 'password' => 'invalid'])->assertSessionHasErrors('email');
        $user = User::factory()->create(['password' => 'CorrectPassword123']);
        $this->post('/login', ['email' => $user->email, 'password' => 'CorrectPassword123'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->put('/admin/settings', ['site_name' => 'Unauthorized'])->assertForbidden();
    }

    public function test_login_is_throttled_after_five_failed_attempts(): void
    {
        $this->freezeTime();
        $user = $this->administrator();
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        }
        $this->post('/login', ['email' => $user->email, 'password' => 'CorrectPassword123'])
            ->assertSessionHasErrors(['email' => 'Too many attempts. Try again in 60 seconds.']);
        $this->assertGuest();
    }

    public function test_dashboard_displays_site_details_and_administrator_count(): void
    {
        Setting::create(['key' => 'site_name', 'value' => 'Takshasheela Test']);
        $this->actingAs($this->administrator())->get('/admin')->assertOk()
            ->assertViewHas('administrators', 1)
            ->assertSee('Takshasheela Test')
            ->assertSee('Site details')
            ->assertSee('class="admin-brand-logo"', false)
            ->assertSee('class="logout"', false)
            ->assertDontSee('class="sidebar-note"', false)
            ->assertDontSee('/admin/pages');
    }

    public function test_page_management_routes_have_been_removed(): void
    {
        foreach ([false, true] as $authenticated) {
            if ($authenticated) {
                $this->actingAs($this->administrator());
            }
            foreach (['/admin/pages', '/admin/pages/create', '/admin/pages/1/edit', '/admin/pages/1/preview', '/pages/our-story'] as $url) {
                $this->get($url)->assertNotFound();
            }
            $this->post('/admin/pages')->assertNotFound();
            $this->put('/admin/pages/1')->assertNotFound();
            $this->delete('/admin/pages/1')->assertNotFound();
        }
    }

    public function test_site_settings_are_saved_and_shown_on_the_dashboard(): void
    {
        Storage::fake('public');
        $this->actingAs($this->administrator())->get('/admin/settings')->assertOk()->assertSee('Map embed URL');
        $this->put('/admin/settings', [
            'site_name' => 'Aashram Test',
            'legal_name' => 'Aashram Test Private Limited',
            'tagline' => 'Rest and renew',
            'business_description' => 'A restorative Ayurveda destination.',
            'default_meta_description' => 'Ayurveda and restorative stays.',
            'logo_alt' => 'Aashram Test logo',
            'contact_email' => 'contact@example.com',
            'enquiry_email' => 'enquiries@example.com',
            'contact_phone' => '123456',
            'alternate_phone' => '654321',
            'whatsapp_phone' => '+977 9800000000',
            'address' => 'Aashram Road',
            'city' => 'Kathmandu',
            'postal_code' => '44600',
            'country' => 'Nepal',
            'business_hours' => 'Sunday–Friday: 9:00 AM–6:00 PM
Saturday: By appointment',
            'response_time' => 'Within one working day',
            'map_embed_url' => 'https://maps.example.com/embed/aashram',
            'map_directions_url' => 'https://maps.example.com/directions/aashram',
            'registration_number' => 'REG-1234',
            'facebook_url' => 'https://facebook.com/aashram',
            'instagram_url' => 'https://instagram.com/aashram',
            'youtube_url' => 'https://youtube.com/@aashram',
            'linkedin_url' => 'https://linkedin.com/company/aashram',
            'logo' => UploadedFile::fake()->image('business-logo.png', 900, 300),
        ])->assertSessionHasNoErrors()->assertSessionHas('status');
        $this->assertDatabaseHas('settings', ['key' => 'site_name', 'value' => 'Aashram Test']);
        $this->assertDatabaseHas('settings', ['key' => 'enquiry_email', 'value' => 'enquiries@example.com']);
        $logoPath = Setting::where('key', 'logo_path')->value('value');
        Storage::disk('public')->assertExists($logoPath);
        $this->get('/admin')->assertOk()->assertSee('Aashram Test')->assertSee('contact@example.com');
        $this->put('/admin/settings', ['site_name' => '', 'contact_email' => 'invalid', 'map_embed_url' => 'javascript:alert(1)'])
            ->assertSessionHasErrors(['site_name', 'contact_email', 'map_embed_url']);
        $this->post('/logout')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('Aashram Test')->assertSee('A restorative Ayurveda destination.');
    }

    public function test_profile_updates_require_current_password_and_hash_new_password(): void
    {
        $user = $this->administrator();
        $this->actingAs($user)->get('/admin/profile')->assertOk();
        $data = ['name' => 'New Name', 'email' => 'new@example.com', 'password' => 'NewSecurePassword456', 'password_confirmation' => 'NewSecurePassword456'];
        $this->put('/admin/profile', $data + ['current_password' => 'wrong'])->assertSessionHasErrors('current_password');
        $this->put('/admin/profile', $data + ['current_password' => 'CorrectPassword123'])->assertSessionHasNoErrors();
        $this->assertSame('New Name', $user->fresh()->name);
        $this->assertTrue(Hash::check('NewSecurePassword456', $user->fresh()->password));
        $this->get('/admin')->assertOk();
    }

    public function test_password_reset_emails_are_sent_only_to_admins_without_exposing_accounts(): void
    {
        Notification::fake();
        $admin = $this->administrator();
        $regular = User::factory()->create();
        $this->get('/forgot-password')->assertOk();
        foreach ([$admin->email, $regular->email, 'missing@example.com'] as $email) {
            $this->post('/forgot-password', compact('email'))->assertSessionHas('status', 'If an administrator account exists for that email, a password reset link has been sent.');
        }
        Notification::assertSentTo($admin, ResetPassword::class);
        Notification::assertNotSentTo($regular, ResetPassword::class);
    }

    public function test_password_reset_requires_a_valid_token_and_allows_sign_in_with_new_password(): void
    {
        $user = $this->administrator();
        $data = ['email' => $user->email, 'password' => 'NewSecurePassword123', 'password_confirmation' => 'NewSecurePassword123'];
        $this->post('/reset-password', $data + ['token' => 'invalid'])->assertSessionHasErrors('email');
        $token = Password::createToken($user);
        $this->get('/reset-password/'.$token.'?email='.urlencode($user->email))->assertOk();
        $this->post('/reset-password', $data + ['token' => $token])->assertRedirect('/login');
        $this->assertTrue(Hash::check('NewSecurePassword123', $user->fresh()->password));
        $this->post('/login', ['email' => $user->email, 'password' => 'NewSecurePassword123'])->assertRedirect('/admin');
    }

    public function test_settings_seeder_preserves_existing_content_and_creates_no_default_accounts(): void
    {
        config(['seeding.admin.password' => null]);
        Setting::create(['key' => 'site_name', 'value' => 'Existing name']);
        $this->seed();
        $this->seed();
        $this->assertDatabaseHas('settings', ['key' => 'site_name', 'value' => 'Existing name']);
        $this->assertDatabaseCount('settings', count(config('site.defaults')));
        $this->assertDatabaseCount('users', 0);
    }

    public function test_demo_site_settings_fill_empty_fields_without_overwriting_saved_values(): void
    {
        Setting::create(['key' => 'contact_phone', 'value' => '+977 9999999999']);
        Setting::create(['key' => 'instagram_url', 'value' => '']);

        $this->seed(DemoSiteSettingsSeeder::class);

        $this->assertDatabaseHas('settings', ['key' => 'contact_phone', 'value' => '+977 9999999999']);
        $this->assertDatabaseHas('settings', ['key' => 'instagram_url', 'value' => 'https://www.instagram.com/takshasheela']);
        $this->assertDatabaseHas('settings', ['key' => 'business_hours', 'value' => "Sunday–Friday: 8:00 AM–6:00 PM\nSaturday: By appointment"]);
    }

    public function test_administrator_command_creates_an_account_and_rejects_duplicate_emails(): void
    {
        $this->artisan('cms:create-admin', ['email' => 'owner@example.com', '--name' => 'Owner'])
            ->expectsQuestion('Password (at least 12 characters, including letters and numbers)', 'CorrectPassword123')
            ->expectsQuestion('Confirm password', 'CorrectPassword123')
            ->expectsOutput('Administrator created: owner@example.com')->assertSuccessful();
        $this->assertDatabaseHas('users', ['email' => 'owner@example.com', 'role' => 'admin']);
        $this->artisan('cms:create-admin', ['email' => 'owner@example.com', '--name' => 'Owner'])
            ->expectsQuestion('Password (at least 12 characters, including letters and numbers)', 'CorrectPassword123')
            ->expectsQuestion('Confirm password', 'CorrectPassword123')->expectsOutput('The email has already been taken.')->assertFailed();
        $this->assertDatabaseCount('users', 1);
    }
}
