<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WellnessCategory;
use App\Models\WellnessOffering;
use App\Models\WellnessSubcategory;
use Database\Seeders\WellnessSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WellnessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(WellnessSeeder::class);
    }

    private function administrator(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'admin'])->save();

        return $user;
    }

    private function categoryPayload(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Healing Circles',
            'nav_description' => 'Guided small-group experiences',
            'hero_eyebrow' => 'Healing circles',
            'hero_title' => 'Gather, listen, and restore.',
            'hero_description' => 'Small-group experiences grounded in reflection, presence, and compassionate guidance.',
            'hero_image_alt' => 'Guests participating in a healing circle',
            'section_eyebrow' => 'Our circles',
            'section_title' => 'Choose a guided group experience.',
            'information_eyebrow' => 'A shared practice',
            'information_title' => 'Connection supports healing.',
            'information_body' => "Each circle is gently facilitated.\n\nParticipants are invited to share only what feels comfortable.",
            'cta_eyebrow' => 'Join a circle',
            'cta_title' => 'Ask about dates and group availability.',
            'cta_body' => 'Tell us what kind of support you are seeking.',
            'sort_order' => 40,
            'is_published' => 1,
        ], $overrides);
    }

    private function subcategoryPayload(int $categoryId, array $overrides = []): array
    {
        return array_replace([
            'wellness_category_id' => $categoryId,
            'name' => 'Restorative Group Experiences',
            'nav_description' => 'Guided group practices for restoration and connection',
            'description' => 'This subcategory introduces the shared approach used by every restorative group offering.',
            'sort_order' => 10,
            'is_published' => 1,
        ], $overrides);
    }

    private function offeringPayload(int $subcategoryId, array $overrides = []): array
    {
        return array_replace([
            'wellness_subcategory_id' => $subcategoryId,
            'name' => 'Restorative Reflection Circle',
            'eyebrow' => 'Guided group practice',
            'duration' => 'Half day',
            'image_alt' => 'A calm restorative reflection circle',
            'short_description' => 'A supported small-group space for slowing down, listening, and reconnecting.',
            'description' => 'This facilitated circle combines grounding practices, guided reflection, and spacious conversation.',
            'highlights_text' => "Guided reflection\nGrounding practices\nSmall supportive group",
            'includes_text' => "Opening grounding practice\nFacilitated reflection\nTea and quiet integration",
            'itinerary_text' => "Morning | Arrival and grounding | Settle into the space with breath and gentle orientation.\nMidday | Reflection circle | Explore a guided theme with time to listen and share.",
            'ideal_for' => 'Guests who value a supported group setting and a gentle reflective pace.',
            'secondary_title' => 'How the circle works',
            'secondary_content' => 'The facilitator creates a clear structure while allowing each participant to choose their level of involvement.',
            'notice' => 'This wellbeing experience does not replace mental-health or medical treatment.',
            'sort_order' => 10,
            'is_published' => 1,
        ], $overrides);
    }

    public function test_existing_categories_and_child_programs_are_seeded_and_dynamic_on_the_frontend(): void
    {
        $this->assertDatabaseCount('wellness_categories', 3);
        $this->assertDatabaseCount('wellness_subcategories', 6);
        $this->assertDatabaseCount('wellness_offerings', 9);
        $this->assertSame(['programs', 'therapies', 'trainings'], WellnessCategory::orderBy('sort_order')->pluck('slug')->all());

        $program = WellnessOffering::where('slug', 'panchakarma-rejuvenation')->firstOrFail();
        Storage::disk('public')->assertExists($program->image_path);

        $this->get('/')->assertOk()->assertSee('Panchakarma Rejuvenation')->assertSee('Services')->assertSee('Training')->assertSee('Cleansing &amp; Rejuvenation', false);
        $this->get('/programs')->assertOk()->assertSee('Personalised paths to healing and renewal.');
        $this->get('/programs/panchakarma-rejuvenation')->assertOk()->assertSee('Your daily rhythm');
    }

    public function test_wysiwyg_description_formatting_is_sanitized_and_rendered(): void
    {
        $subcategory = WellnessSubcategory::where('slug', 'cleansing-rejuvenation')->firstOrFail();
        $offering = $subcategory->offerings()->where('slug', 'panchakarma-rejuvenation')->firstOrFail();
        $payload = $this->subcategoryPayload($subcategory->wellness_category_id, [
            'name' => $subcategory->name,
            'nav_description' => $subcategory->nav_description,
            'description' => '<p onclick="alert(1)">Shared <strong style="color:red">subcategory</strong> guidance.</p><script>alert(2)</script>',
            'sort_order' => $subcategory->sort_order,
        ]);

        $this->actingAs($this->administrator())
            ->put('/admin/wellness-subcategories/'.$subcategory->id, $payload)
            ->assertSessionHasNoErrors();

        $saved = $subcategory->fresh()->description;
        $this->assertSame('<p>Shared <strong>subcategory</strong> guidance.</p>', $saved);
        $this->get($offering->publicUrl())
            ->assertOk()
            ->assertSee('<strong>subcategory</strong>', false)
            ->assertDontSee('onclick=', false)
            ->assertDontSee('alert(2)')
            ->assertDontSee('onclick=', false);
    }

    public function test_only_administrators_can_manage_wellness_content(): void
    {
        foreach (['/admin/wellness-categories', '/admin/wellness-categories/create', '/admin/wellness-subcategories', '/admin/wellness-subcategories/create', '/admin/wellness-offerings', '/admin/wellness-offerings/create'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }

        $this->actingAs(User::factory()->create());
        $this->get('/admin/wellness-categories')->assertForbidden();
        $this->post('/admin/wellness-categories', $this->categoryPayload())->assertForbidden();
        $this->get('/admin/wellness-subcategories')->assertForbidden();
        $this->post('/admin/wellness-subcategories', $this->subcategoryPayload(1))->assertForbidden();
        $this->get('/admin/wellness-offerings')->assertForbidden();
        $this->post('/admin/wellness-offerings', $this->offeringPayload(1))->assertForbidden();
    }

    public function test_administrator_can_create_a_category_and_child_program_with_automatic_slugs(): void
    {
        $this->actingAs($this->administrator());

        $programsCategory = WellnessCategory::where('slug', 'programs')->firstOrFail();
        $this->get('/admin/wellness-offerings?category='.$programsCategory->id)
            ->assertOk()
            ->assertSee('Panchakarma Rejuvenation')
            ->assertDontSee('View [vendor.pagination.simple-default] not found');

        $this->get('/admin/wellness-categories/create')->assertOk()->assertDontSee('name="slug"', false);
        $this->post('/admin/wellness-categories', $this->categoryPayload() + [
            'image' => UploadedFile::fake()->image('circle-hero.jpg', 1600, 900),
        ])->assertSessionHasNoErrors();

        $category = WellnessCategory::where('slug', 'healing-circles')->firstOrFail();
        Storage::disk('public')->assertExists($category->hero_image_path);

        $this->get('/admin/wellness-subcategories/create?category='.$category->id)->assertOk()->assertDontSee('name="slug"', false);
        $this->post('/admin/wellness-subcategories', $this->subcategoryPayload($category->id))->assertSessionHasNoErrors();
        $subcategory = WellnessSubcategory::where('wellness_category_id', $category->id)->where('slug', 'restorative-group-experiences')->firstOrFail();

        $this->get('/admin/wellness-offerings/create?subcategory='.$subcategory->id)->assertOk()->assertDontSee('name="slug"', false);
        $this->post('/admin/wellness-offerings', $this->offeringPayload($subcategory->id) + [
            'image' => UploadedFile::fake()->image('reflection.jpg', 1200, 800),
        ])->assertSessionHasNoErrors();

        $offering = WellnessOffering::where('wellness_category_id', $category->id)->where('slug', 'restorative-reflection-circle')->firstOrFail();
        $this->assertCount(2, $offering->itinerary);
        Storage::disk('public')->assertExists($offering->image_path);

        $this->get('/')->assertOk()->assertSee('Healing Circles')->assertSee('Restorative Reflection Circle');
        $this->get('/wellness/healing-circles')->assertOk()->assertSee($offering->name);
        $this->get('/wellness/healing-circles/restorative-reflection-circle')
            ->assertOk()
            ->assertSeeInOrder([
                'This subcategory introduces the shared approach used by every restorative group offering.',
                'Restorative Reflection Circle',
                'This facilitated circle combines grounding practices, guided reflection, and spacious conversation.',
            ])
            ->assertSee('How the circle works');
    }

    public function test_updates_keep_urls_stable_and_drafts_are_hidden(): void
    {
        $offering = WellnessOffering::where('slug', 'panchakarma-rejuvenation')->firstOrFail();
        $oldImage = $offering->image_path;

        $this->actingAs($this->administrator())->put(
            '/admin/wellness-offerings/'.$offering->id,
            $this->offeringPayload($offering->wellness_subcategory_id, [
                'name' => 'Updated Panchakarma Journey',
                'is_published' => 0,
            ]) + ['image' => UploadedFile::fake()->image('new-program.webp', 900, 600)]
        )->assertSessionHasNoErrors();

        $saved = $offering->fresh();
        $this->assertSame('panchakarma-rejuvenation', $saved->slug);
        $this->assertFalse($saved->is_published);
        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($saved->image_path);
        $this->get('/programs')->assertDontSee('Updated Panchakarma Journey');
        $this->get('/programs/panchakarma-rejuvenation')->assertNotFound();

        $category = WellnessCategory::where('slug', 'therapies')->firstOrFail();
        $this->put('/admin/wellness-categories/'.$category->id, $this->categoryPayload([
            'name' => 'Updated Services',
            'is_published' => 0,
        ]))->assertSessionHasNoErrors();
        $this->assertSame('therapies', $category->fresh()->slug);
        $this->get('/therapies')->assertNotFound();
    }

    public function test_duplicate_names_get_unique_slugs_and_nonempty_categories_cannot_be_deleted(): void
    {
        $this->actingAs($this->administrator());
        $category = WellnessCategory::where('slug', 'programs')->firstOrFail();
        $subcategory = $category->subcategories()->firstOrFail();

        $this->post('/admin/wellness-offerings', $this->offeringPayload($subcategory->id, [
            'name' => 'Panchakarma Rejuvenation',
        ]) + ['image' => UploadedFile::fake()->image('duplicate.jpg')])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('wellness_offerings', [
            'wellness_category_id' => $category->id,
            'slug' => 'panchakarma-rejuvenation-2',
        ]);

        $this->delete('/admin/wellness-categories/'.$category->id)
            ->assertSessionHasErrors('category');
        $this->assertModelExists($category);

        $this->post('/admin/wellness-offerings', $this->offeringPayload($subcategory->id) + [
            'image' => UploadedFile::fake()->create('unsafe.svg', 5, 'image/svg+xml'),
        ])->assertSessionHasErrors('image');
    }
}
