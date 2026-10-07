<?php

namespace Tests\Feature;

use App\Models\ChronicleArticle;
use App\Models\GalleryItem;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\ChronicleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChronicleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(ChronicleSeeder::class);
    }

    private function administrator(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'admin'])->save();

        return $user;
    }

    private function articlePayload(array $overrides = []): array
    {
        return array_replace([
            'type' => 'blog',
            'title' => 'A New Chronicle',
            'category' => 'Reflection',
            'excerpt' => 'A short introduction to a new Chronicle reflection.',
            'body' => "The first paragraph of the Chronicle.\n\nThe second paragraph continues the reflection.",
            'image_alt' => 'A quiet reflective moment',
            'published_at' => '2026-10-07T10:00',
            'sort_order' => 50,
            'is_featured' => 0,
            'is_published' => 1,
        ], $overrides);
    }

    public function test_all_chronicle_sections_are_seeded_and_read_by_the_public_frontend(): void
    {
        $this->assertDatabaseCount('chronicle_articles', 8);
        $this->assertDatabaseCount('testimonials', 3);
        $this->assertDatabaseCount('gallery_items', 8);

        require_once dirname(base_path()).'/includes/chronicles.php';
        $this->assertCount(4, chronicle_articles('blog'));
        $this->assertCount(4, chronicle_articles('news'));
        $this->assertCount(3, chronicle_testimonials());
        $this->assertCount(8, chronicle_gallery());
        $this->assertSame('What Actually Happens on a Takshasheela Healing Retreat', chronicle_featured(chronicle_articles('blog'))['title']);
        $this->assertStringStartsWith('chronicle-image.php?path=', chronicle_articles('blog')['what-happens-on-a-takshasheela-healing-retreat']['image']);
    }

    public function test_only_administrators_can_manage_chronicle_sections(): void
    {
        foreach (['chronicles', 'testimonials', 'gallery'] as $resource) {
            $this->get('/admin/'.$resource)->assertRedirect('/login');
            $this->get('/admin/'.$resource.'/create')->assertRedirect('/login');
        }

        $this->actingAs(User::factory()->create());
        foreach (['chronicles', 'testimonials', 'gallery'] as $resource) {
            $this->get('/admin/'.$resource)->assertForbidden();
            $this->post('/admin/'.$resource, [])->assertForbidden();
            $this->delete('/admin/'.$resource.'/1')->assertForbidden();
        }
    }

    public function test_administrator_can_create_edit_and_publish_a_chronicle_article(): void
    {
        $admin = $this->administrator();
        $this->actingAs($admin)->get('/admin/chronicles')->assertOk()->assertSee('Blogs &amp; News', false);
        $this->get('/admin/chronicles/create?type=news')
            ->assertOk()
            ->assertSee('name="body"', false)
            ->assertDontSee('name="slug"', false);

        $response = $this->post('/admin/chronicles', $this->articlePayload() + [
            'image' => UploadedFile::fake()->image('reflection.jpg', 1200, 800),
        ]);

        $article = ChronicleArticle::where('slug', 'a-new-chronicle')->firstOrFail();
        $response->assertRedirect(route('admin.chronicles.edit', $article))->assertSessionHasNoErrors();
        $this->assertSame($admin->id, $article->updated_by);
        Storage::disk('public')->assertExists($article->image_path);

        $this->get('/admin/chronicles/'.$article->id.'/edit')->assertOk()->assertSee('A New Chronicle');

        require_once dirname(base_path()).'/includes/chronicles.php';
        $this->assertSame('A New Chronicle', chronicle_by_slug('a-new-chronicle')['title']);
    }

    public function test_featured_article_is_unique_per_section_and_drafts_are_hidden(): void
    {
        $article = ChronicleArticle::where('type', 'blog')->where('is_featured', false)->firstOrFail();
        $oldFeatured = ChronicleArticle::where('type', 'blog')->where('is_featured', true)->firstOrFail();
        $oldImage = $article->image_path;

        $this->actingAs($this->administrator())->put('/admin/chronicles/'.$article->id, $this->articlePayload([
            'type' => 'blog',
            'title' => $article->title,
            'is_featured' => 1,
            'is_published' => 0,
        ]) + ['image' => UploadedFile::fake()->image('new.webp', 900, 600)])
            ->assertSessionHasNoErrors();

        $saved = $article->fresh();
        $this->assertTrue($saved->is_featured);
        $this->assertSame($article->slug, $saved->slug);
        $this->assertFalse($saved->is_published);
        $this->assertFalse($oldFeatured->fresh()->is_featured);
        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($saved->image_path);

        require_once dirname(base_path()).'/includes/chronicles.php';
        $this->assertNull(chronicle_by_slug($saved->slug));
    }

    public function test_testimonials_can_be_created_updated_and_hidden_from_public_pages(): void
    {
        $admin = $this->administrator();
        $this->actingAs($admin)->get('/admin/testimonials')->assertOk()->assertSee('Asha Sharma');
        $this->get('/admin/testimonials/create')->assertOk()->assertSee('name="quote"', false);

        $this->post('/admin/testimonials', [
            'title' => 'A Restorative Week',
            'quote' => 'I felt supported from the first conversation to the final day.',
            'guest_name' => 'Maya Rai',
            'guest_location' => 'Bhaktapur, Nepal',
            'rating' => 5,
            'sort_order' => 40,
            'is_published' => 1,
        ])->assertSessionHasNoErrors();

        $testimonial = Testimonial::where('guest_name', 'Maya Rai')->firstOrFail();
        $this->assertSame($admin->id, $testimonial->updated_by);

        require_once dirname(base_path()).'/includes/chronicles.php';
        $this->assertContains('Maya Rai', array_column(chronicle_testimonials(), 'guest_name'));

        $this->put('/admin/testimonials/'.$testimonial->id, [
            'title' => $testimonial->title,
            'quote' => $testimonial->quote,
            'guest_name' => $testimonial->guest_name,
            'guest_location' => $testimonial->guest_location,
            'rating' => 4,
            'sort_order' => 40,
            'is_published' => 0,
        ])->assertSessionHasNoErrors();

        $this->assertNotContains('Maya Rai', array_column(chronicle_testimonials(), 'guest_name'));
    }

    public function test_gallery_images_can_be_added_replaced_and_deleted(): void
    {
        $this->actingAs($this->administrator())->get('/admin/gallery')->assertOk()->assertSee('Gallery images');
        $this->get('/admin/gallery/create')->assertOk()->assertSee('name="image"', false);

        $this->post('/admin/gallery', [
            'image' => UploadedFile::fake()->image('garden.jpg', 1200, 800),
            'image_alt' => 'A peaceful garden',
            'caption' => 'Aashram garden',
            'sort_order' => 90,
            'is_published' => 1,
        ])->assertSessionHasNoErrors();

        $item = GalleryItem::where('caption', 'Aashram garden')->firstOrFail();
        $oldImage = $item->image_path;
        Storage::disk('public')->assertExists($oldImage);

        $this->put('/admin/gallery/'.$item->id, [
            'image' => UploadedFile::fake()->image('garden-new.webp', 900, 600),
            'image_alt' => 'Updated peaceful garden',
            'caption' => 'Updated garden',
            'sort_order' => 5,
            'is_published' => 1,
        ])->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($item->fresh()->image_path);

        $newImage = $item->fresh()->image_path;
        $this->delete('/admin/gallery/'.$item->id)->assertRedirect(route('admin.gallery.index'));
        Storage::disk('public')->assertMissing($newImage);
        $this->assertModelMissing($item);
    }

    public function test_duplicate_titles_receive_unique_slugs_and_invalid_uploads_are_rejected(): void
    {
        $this->actingAs($this->administrator());

        $this->post('/admin/chronicles', $this->articlePayload([
            'title' => 'The Magic of Healing – Coming Home to Wholeness',
        ]) + ['image' => UploadedFile::fake()->image('article.jpg')])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('chronicle_articles', [
            'slug' => 'the-magic-of-healing-coming-home-to-wholeness-2',
        ]);

        $this->post('/admin/chronicles', $this->articlePayload([
            'type' => 'invalid',
        ]) + ['image' => UploadedFile::fake()->create('article.svg', 5, 'image/svg+xml')])
            ->assertSessionHasErrors(['type', 'image']);

        $this->post('/admin/gallery', [
            'image' => UploadedFile::fake()->create('gallery.svg', 5, 'image/svg+xml'),
            'image_alt' => 'Unsafe image',
            'sort_order' => 100,
            'is_published' => 1,
        ])->assertSessionHasErrors('image');

        require_once dirname(base_path()).'/includes/chronicles.php';
        $this->assertSame('', chronicle_image_url('../../.env'));
        $this->assertDatabaseCount('chronicle_articles', 9);
        $this->assertDatabaseCount('gallery_items', 8);
    }
}
