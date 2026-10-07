<?php

namespace Tests\Feature;

use App\Mail\ContactEnquiry;
use App\Models\Enquiry;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\AccommodationSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\WellnessSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class EnquiryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([WellnessSeeder::class, AccommodationSeeder::class, ProductSeeder::class]);
        Setting::create(['key' => 'site_name', 'value' => 'Takshasheela Test']);
        Setting::create(['key' => 'contact_email', 'value' => 'admin@example.com']);
    }

    public function test_submission_remains_saved_when_admin_email_fails(): void
    {
        Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('SMTP unavailable'));

        $this->post('/contact', $this->validSubmission())
            ->assertRedirect(route('contact'))
            ->assertSessionHas('status');

        $enquiry = Enquiry::firstOrFail();
        $this->assertSame('Sita Sharma', $enquiry->name);
        $this->assertNull($enquiry->emailed_at);
        $this->assertStringContainsString('SMTP unavailable', $enquiry->mail_error);
    }

    public function test_administrator_can_review_update_resend_and_delete_enquiries(): void
    {
        Mail::fake();
        $enquiry = Enquiry::create([
            'name' => 'Sita Sharma',
            'email' => 'sita@example.com',
            'phone' => '+977 9800000000',
            'enquiry_type' => 'general',
            'enquiry_type_label' => 'General enquiry',
            'message' => 'I would like to discuss a private consultation.',
            'consent' => true,
        ]);

        $this->get(route('admin.enquiries.index'))->assertRedirect('/login');

        $this->actingAs($this->administrator())
            ->get(route('admin.enquiries.index'))
            ->assertOk()
            ->assertSee('Sita Sharma')
            ->assertSee('General enquiry')
            ->assertSee('All statuses')
            ->assertSee('All email delivery')
            ->assertSee('Apply filters');

        $this->get(route('admin.enquiries.index', ['status' => 'new']))
            ->assertOk()
            ->assertSee('Sita Sharma');
        $this->get(route('admin.enquiries.index', ['mail' => 'failed']))
            ->assertOk()
            ->assertDontSee('Sita Sharma');

        $this->get(route('admin.enquiries.show', $enquiry))
            ->assertOk()
            ->assertSee('I would like to discuss a private consultation.')
            ->assertDontSee('ADMIN EMAIL')
            ->assertDontSee('Notification not sent');
        $this->assertDatabaseHas('enquiries', ['id' => $enquiry->id, 'status' => 'read']);

        $this->patch(route('admin.enquiries.status', $enquiry), ['status' => 'archived'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');
        $this->assertDatabaseHas('enquiries', ['id' => $enquiry->id, 'status' => 'archived']);

        $this->post(route('admin.enquiries.resend', $enquiry))->assertSessionHas('status');
        Mail::assertSent(ContactEnquiry::class, fn (ContactEnquiry $mail) => $mail->hasTo('admin@example.com'));
        $this->assertNotNull($enquiry->fresh()->emailed_at);

        $this->delete(route('admin.enquiries.destroy', $enquiry))
            ->assertRedirect(route('admin.enquiries.index'));
        $this->assertDatabaseMissing('enquiries', ['id' => $enquiry->id]);
    }

    private function validSubmission(): array
    {
        return [
            'name' => 'Sita Sharma',
            'email' => 'sita@example.com',
            'phone' => '',
            'enquiry_type' => 'general',
            'interest' => '',
            'message' => 'I would like to discuss a private consultation.',
            'consent' => '1',
            'website' => '',
        ];
    }

    private function administrator(): User
    {
        $user = User::factory()->create(['password' => 'CorrectPassword123']);
        $user->forceFill(['role' => 'admin'])->save();

        return $user;
    }
}
