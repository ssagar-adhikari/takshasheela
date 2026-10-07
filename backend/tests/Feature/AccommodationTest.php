<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use App\Models\User;
use Database\Seeders\AccommodationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccommodationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(AccommodationSeeder::class);
    }

    private function administrator(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'admin'])->save();

        return $user;
    }

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Garden Suite',
            'category' => 'Quiet garden comfort',
            'heading' => 'Rest beside the garden.',
            'image_alt' => 'Garden suite at Takshasheela',
            'short_description' => 'A private room beside the garden for a peaceful restorative stay.',
            'description' => 'The Garden Suite offers a quiet base for therapies, reflection, and deep rest.',
            'highlights_text' => "Private garden view\nQuiet relaxation space\nDaily hospitality support",
            'features_text' => "Comfortable sleeping space\nFresh linen\nPrivate bathroom",
            'ideal_for' => 'Guests looking for privacy and easy access to the garden.',
            'stay_note' => 'Tell us about accessibility or room setup needs when enquiring.',
            'sort_order' => 30,
            'is_published' => 1,
        ], $overrides);
    }

    public function test_defaults_are_seeded_and_read_by_the_public_listing_and_details_source(): void
    {
        $this->assertDatabaseCount('accommodations', 2);
        $this->assertSame(['standard-room', 'deluxe-room'], Accommodation::orderBy('sort_order')->pluck('slug')->all());

        require_once dirname(base_path()).'/includes/accommodations.php';
        $rooms = accommodations();

        $this->assertSame(['standard-room', 'deluxe-room'], array_keys($rooms));
        $this->assertSame('Standard Room', accommodation_by_slug('standard-room')['name']);
        $this->assertStringStartsWith('accommodation-image.php?path=', $rooms['standard-room']['image']);
        Storage::disk('public')->assertExists(Accommodation::where('slug', 'standard-room')->value('image_path'));
    }

    public function test_only_administrators_can_manage_accommodations(): void
    {
        foreach (['/admin/accommodations', '/admin/accommodations/create', '/admin/accommodations/1/edit'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }

        $this->actingAs(User::factory()->create());
        $this->get('/admin/accommodations')->assertForbidden();
        $this->post('/admin/accommodations', $this->payload())->assertForbidden();
        $this->put('/admin/accommodations/1', $this->payload())->assertForbidden();
        $this->delete('/admin/accommodations/1')->assertForbidden();
    }

    public function test_administrator_can_create_a_published_accommodation_with_an_uploaded_image(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get('/admin/accommodations')
            ->assertOk()
            ->assertSee('Standard Room')
            ->assertSee('Deluxe Room');

        $this->get('/admin/accommodations/create')
            ->assertOk()
            ->assertSee('name="image"', false)
            ->assertSee('name="highlights_text"', false)
            ->assertDontSee('name="slug"', false);

        $this->get('/admin/accommodations/'.Accommodation::where('slug', 'standard-room')->value('id').'/edit')
            ->assertOk()
            ->assertSee('Standard Room')
            ->assertSee('accommodations/standard-room/defaults/standard-room.jpg');

        $response = $this->post('/admin/accommodations', $this->payload() + [
            'image' => UploadedFile::fake()->image('garden.jpg', 1200, 800),
        ]);

        $accommodation = Accommodation::where('slug', 'garden-suite')->firstOrFail();
        $response->assertRedirect(route('admin.accommodations.edit', $accommodation))->assertSessionHasNoErrors();
        $this->assertSame(['Private garden view', 'Quiet relaxation space', 'Daily hospitality support'], $accommodation->highlights);
        $this->assertSame($admin->id, $accommodation->updated_by);
        Storage::disk('public')->assertExists($accommodation->image_path);

        require_once dirname(base_path()).'/includes/accommodations.php';
        $this->assertSame('Garden Suite', accommodations()['garden-suite']['name']);
    }

    public function test_administrator_can_update_publish_order_and_replace_an_image(): void
    {
        $accommodation = Accommodation::where('slug', 'standard-room')->firstOrFail();
        $oldImage = $accommodation->image_path;

        $this->actingAs($this->administrator())->put(
            '/admin/accommodations/'.$accommodation->id,
            $this->payload([
                'name' => 'Updated Standard Room',
                'sort_order' => 5,
                'is_published' => 0,
                'features_text' => "New feature one\nNew feature two",
            ]) + ['image' => UploadedFile::fake()->image('replacement.webp', 900, 600)]
        )->assertSessionHasNoErrors()->assertSessionHas('status');

        $saved = $accommodation->fresh();
        $this->assertSame('Updated Standard Room', $saved->name);
        $this->assertSame('standard-room', $saved->slug);
        $this->assertSame(['New feature one', 'New feature two'], $saved->features);
        $this->assertFalse($saved->is_published);
        Storage::disk('public')->assertExists($saved->image_path);
        Storage::disk('public')->assertMissing($oldImage);

        require_once dirname(base_path()).'/includes/accommodations.php';
        $this->assertArrayNotHasKey('standard-room', accommodations());
    }

    public function test_deleting_an_accommodation_removes_its_uploaded_image(): void
    {
        $accommodation = Accommodation::where('slug', 'deluxe-room')->firstOrFail();
        $image = $accommodation->image_path;

        $this->actingAs($this->administrator())
            ->delete('/admin/accommodations/'.$accommodation->id)
            ->assertRedirect(route('admin.accommodations.index'))
            ->assertSessionHas('status');

        $this->assertModelMissing($accommodation);
        Storage::disk('public')->assertMissing($image);
    }

    public function test_duplicate_names_receive_unique_slugs_and_unsafe_images_are_rejected(): void
    {
        $this->actingAs($this->administrator());

        $this->post('/admin/accommodations', $this->payload([
            'name' => 'Standard Room',
        ]) + ['image' => UploadedFile::fake()->image('room.jpg')])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('accommodations', ['slug' => 'standard-room-2']);

        $this->post('/admin/accommodations', $this->payload() + [
            'image' => UploadedFile::fake()->create('room.svg', 5, 'image/svg+xml'),
        ])->assertSessionHasErrors('image');

        $this->assertSame('', accommodation_image_url('../../.env'));
        $this->assertDatabaseCount('accommodations', 3);
    }
}
