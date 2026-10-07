<?php

namespace Tests\Feature;

use App\Models\AboutSection;
use App\Models\HomeSection;
use App\Models\User;
use Database\Seeders\AboutSectionSeeder;
use Database\Seeders\HomeSectionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeSectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed([HomeSectionSeeder::class, AboutSectionSeeder::class]);
    }

    public function test_home_sections_are_seeded_and_about_inclusivity_is_reused(): void
    {
        $this->assertDatabaseCount('home_sections', 2);
        $about = AboutSection::where('slug', 'about-takshasheela')->firstOrFail();
        $content = $about->content;
        $content['blocks']['block-1']['eyebrow'] = 'Shared About eyebrow';
        $content['blocks']['block-1']['title'] = 'Shared Inclusivity heading';
        $content['blocks']['block-1']['body'] = "Shared first paragraph.\n\nShared second paragraph.";
        $about->update(['content' => $content]);

        $this->get('/')
            ->assertOk()
            ->assertSee(config('home.sections.hero.title'))
            ->assertSee(config('home.sections.philosophy.quote'))
            ->assertSee('Shared About eyebrow')
            ->assertSee('Shared Inclusivity heading')
            ->assertSee('Shared first paragraph.')
            ->assertSee('Shared second paragraph.')
            ->assertSee('/storage/'.$content['blocks']['block-1']['image'], false);
    }

    public function test_administrator_can_update_homepage_hero_philosophy_and_poster(): void
    {
        $this->get('/admin/homepage')->assertRedirect('/login');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/homepage')
            ->assertOk()
            ->assertSee('Hero background video')
            ->assertSee('Shared from About Takshasheela');

        $this->put('/admin/homepage', [
            'hero' => [
                'eyebrow' => 'A dynamic welcome',
                'title' => 'A CMS managed journey.',
                'description' => 'A homepage introduction saved in the database.',
                'scroll_note' => 'Bhaktapur, Nepal',
                'poster_alt' => 'A peaceful garden at sunrise',
            ],
            'philosophy' => [
                'quote' => 'A CMS managed philosophy quote.',
                'attribution' => 'The Takshasheela team',
            ],
            'poster' => UploadedFile::fake()->image('homepage-poster.webp', 1600, 900),
        ])->assertSessionHasNoErrors()->assertSessionHas('status');

        $hero = HomeSection::where('slug', 'hero')->firstOrFail();
        $philosophy = HomeSection::where('slug', 'philosophy')->firstOrFail();
        $this->assertSame('A CMS managed journey.', $hero->content['title']);
        $this->assertSame($admin->id, $hero->updated_by);
        $this->assertStringStartsWith('homepage/hero/', $hero->content['poster_path']);
        Storage::disk('public')->assertExists($hero->content['poster_path']);
        $this->assertSame('A CMS managed philosophy quote.', $philosophy->content['quote']);

        $this->get('/')
            ->assertOk()
            ->assertSee('A dynamic welcome')
            ->assertSee('A CMS managed journey.')
            ->assertSee('Bhaktapur, Nepal')
            ->assertSee('A CMS managed philosophy quote.')
            ->assertSee('/storage/'.$hero->content['poster_path'], false);
    }

    public function test_homepage_update_validates_required_content_and_media(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->put('/admin/homepage', [
                'hero' => ['eyebrow' => '', 'title' => '', 'description' => '', 'scroll_note' => '', 'poster_alt' => ''],
                'philosophy' => ['quote' => '', 'attribution' => ''],
                'poster' => UploadedFile::fake()->create('poster.svg', 10, 'image/svg+xml'),
                'video' => UploadedFile::fake()->create('video.php', 10, 'application/x-httpd-php'),
            ])
            ->assertSessionHasErrors(['hero.title', 'philosophy.quote', 'poster', 'video']);
    }
}
