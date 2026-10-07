<?php

namespace Tests\Feature;

use App\Models\AboutSection;
use App\Models\User;
use Database\Seeders\AboutSectionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class AboutSectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AboutSectionSeeder::class);
    }

    private function administrator(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'admin'])->save();

        return $user;
    }

    private function section(string $slug = 'about-takshasheela'): AboutSection
    {
        return AboutSection::where('slug', $slug)->firstOrFail();
    }

    private function payload(AboutSection $section): array
    {
        $content = $section->content;
        unset($content['hero_image']);
        foreach ($content['blocks'] as &$block) {
            unset($block['kind'], $block['image']);
        }

        return ['content' => $content];
    }

    public function test_all_four_menus_are_seeded_and_render_separate_upload_forms(): void
    {
        $this->assertDatabaseCount('about_sections', 4);
        $this->actingAs($this->administrator());
        foreach (config('about.sections') as $slug => $menu) {
            $response = $this->get('/admin/about-us/'.$slug)->assertOk()
                ->assertSee($menu['label'])->assertSee('multipart/form-data', false)
                ->assertSee('type="file"', false)->assertSee('name="hero_image"', false);
            if ($slug === 'our-team') {
                $response->assertSee('data-remove-team-member', false)->assertSeeInOrder([
                    'data-team-members', 'data-add-team-member', 'data-team-member-template',
                ], false);
            }
            Storage::disk('public')->assertExists($this->section($slug)->content['hero_image']);
        }
        $this->get('/admin/about-us/unknown')->assertNotFound();
    }

    public function test_guests_and_regular_users_cannot_edit_any_about_section(): void
    {
        foreach (array_keys(config('about.sections')) as $slug) {
            $this->get('/admin/about-us/'.$slug)->assertRedirect('/login');
            $this->put('/admin/about-us/'.$slug, [])->assertRedirect('/login');
        }
        $this->actingAs(User::factory()->create());
        foreach (array_keys(config('about.sections')) as $slug) {
            $this->get('/admin/about-us/'.$slug)->assertForbidden();
            $this->put('/admin/about-us/'.$slug, [])->assertForbidden();
        }
    }

    public function test_saving_one_section_preserves_other_menus_and_existing_images(): void
    {
        $admin = $this->administrator();
        $originals = AboutSection::all()->keyBy('slug')->map->content->all();
        $section = $this->section();
        $data = $this->payload($section);
        $data['content']['hero_title'] = 'An updated story';
        $data['content']['blocks']['block-1']['body'] = 'A new introduction.';
        $this->actingAs($admin)->put('/admin/about-us/'.$section->slug, $data)->assertSessionHasNoErrors()->assertSessionHas('status');
        $this->assertSame('An updated story', $section->fresh()->content['hero_title']);
        $this->assertSame($admin->id, $section->fresh()->updated_by);
        $this->assertSame($originals[$section->slug]['hero_image'], $section->fresh()->content['hero_image']);
        foreach ($originals as $slug => $content) {
            if ($slug !== $section->slug) {
                $this->assertSame($content, $this->section($slug)->content);
            }
        }
        require_once dirname(base_path()).'/includes/about-content.php';
        $this->assertSame('An updated story', about_content($section->slug)['hero_title']);
    }

    public function test_uploads_are_stored_and_replaced_images_are_removed(): void
    {
        $section = $this->section();
        $oldHero = $section->content['hero_image'];
        $oldImage = $section->content['blocks']['block-1']['image'];
        $data = $this->payload($section) + [
            'hero_image' => UploadedFile::fake()->image('hero.jpg', 800, 400),
            'block_images' => ['block-1' => UploadedFile::fake()->image('story.png', 600, 500)],
        ];
        $this->actingAs($this->administrator())->put('/admin/about-us/'.$section->slug, $data)->assertSessionHasNoErrors();
        $saved = $section->fresh()->content;
        Storage::disk('public')->assertExists($saved['hero_image']);
        Storage::disk('public')->assertExists($saved['blocks']['block-1']['image']);
        Storage::disk('public')->assertMissing($oldHero);
        Storage::disk('public')->assertMissing($oldImage);
        $this->assertStringStartsWith('about-us/'.$section->slug.'/', $saved['hero_image']);
        $this->assertNotSame('hero.jpg', basename($saved['hero_image']));
        $this->get('/admin/about-us/'.$section->slug)->assertSee('/storage/'.$saved['hero_image']);
    }

    public function test_removing_an_image_keeps_files_used_by_other_blocks(): void
    {
        $section = $this->section('our-team');
        $image = $section->content['blocks']['block-3']['image'];
        $this->assertSame($image, $section->content['blocks']['block-6']['image']);
        $this->actingAs($this->administrator())->put('/admin/about-us/our-team', $this->payload($section) + [
            'remove_images' => ['block-3' => 1], 'remove_hero_image' => 1,
        ])->assertSessionHasNoErrors();
        $this->assertNull($section->fresh()->content['blocks']['block-3']['image']);
        $this->assertNull($section->fresh()->content['hero_image']);
        Storage::disk('public')->assertExists($image);
        $this->put('/admin/about-us/our-team', $this->payload($section->fresh()) + [
            'remove_images' => ['block-6' => 1],
        ])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($image);
    }

    public function test_invalid_uploads_and_client_supplied_paths_are_rejected_without_changes(): void
    {
        $section = $this->section();
        $original = $section->content;
        $this->actingAs($this->administrator());
        foreach ([
            UploadedFile::fake()->create('file.php', 1, 'application/x-httpd-php'),
            UploadedFile::fake()->createWithContent('bad.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
            UploadedFile::fake()->image('large.jpg')->size(2049),
            UploadedFile::fake()->image('wide.png', 6001, 1),
        ] as $file) {
            $this->put('/admin/about-us/'.$section->slug, $this->payload($section) + ['hero_image' => $file])
                ->assertSessionHasErrors('hero_image');
        }
        $data = $this->payload($section);
        $data['content']['blocks']['block-1']['image'] = '../../.env';
        $this->put('/admin/about-us/'.$section->slug, $data)->assertSessionHasErrors('content.blocks.block-1');
        $this->assertSame($original, $section->fresh()->content);
    }

    public function test_oversized_image_batches_are_rejected_without_writing_files(): void
    {
        $section = $this->section('our-team');
        $before = Storage::disk('public')->allFiles();
        $data = $this->payload($section) + [
            'hero_image' => UploadedFile::fake()->image('hero.jpg')->size(2000),
            'block_images' => [
                'block-3' => UploadedFile::fake()->image('one.jpg')->size(2000),
                'block-4' => UploadedFile::fake()->image('two.jpg')->size(2000),
                'block-5' => UploadedFile::fake()->image('three.jpg')->size(2000),
            ],
        ];
        $this->actingAs($this->administrator())->put('/admin/about-us/our-team', $data)->assertSessionHasErrors('images');
        $this->assertSame($before, Storage::disk('public')->allFiles());
    }

    public function test_missing_content_blocks_and_unknown_upload_keys_are_rejected(): void
    {
        $section = $this->section();
        $data = $this->payload($section);
        unset($data['content']['blocks']['block-1']);
        $data['content']['hero_title'] = '';
        $data['block_images']['invalid'] = UploadedFile::fake()->image('image.jpg');
        $this->actingAs($this->administrator())->put('/admin/about-us/'.$section->slug, $data)
            ->assertSessionHasErrors(['content.hero_title', 'content.blocks.block-1', 'block_images']);
    }

    public function test_team_members_can_be_added_and_deleted_with_their_images(): void
    {
        $section = $this->section('our-team');
        $originalCount = count(array_filter($section->content['blocks'], fn (array $block) => $block['kind'] === 'person'));
        $memberKey = 'member-new-person-12345';
        $data = $this->payload($section);
        $data['content']['blocks'][$memberKey] = [
            'eyebrow' => 'Ayurveda Practitioner',
            'title' => 'New Team Member',
            'body' => 'A newly added team biography.',
            'image_alt' => 'Portrait of New Team Member',
        ];
        $data['block_images'][$memberKey] = UploadedFile::fake()->image('new-member.jpg', 500, 500);

        $this->actingAs($this->administrator())
            ->put('/admin/about-us/our-team', $data)
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        $saved = $section->fresh()->content;
        $this->assertSame('person', $saved['blocks'][$memberKey]['kind']);
        $this->assertSame('New Team Member', $saved['blocks'][$memberKey]['title']);
        $this->assertCount($originalCount + 1, array_filter($saved['blocks'], fn (array $block) => $block['kind'] === 'person'));
        $image = $saved['blocks'][$memberKey]['image'];
        Storage::disk('public')->assertExists($image);

        require_once dirname(base_path()).'/includes/about-content.php';
        $this->assertSame('New Team Member', about_content('our-team')['blocks'][$memberKey]['title']);

        $deleteData = $this->payload($section->fresh());
        unset($deleteData['content']['blocks'][$memberKey], $deleteData['content']['blocks']['block-3']);
        $this->put('/admin/about-us/our-team', $deleteData)->assertSessionHasNoErrors();

        $saved = $section->fresh()->content;
        $this->assertArrayNotHasKey($memberKey, $saved['blocks']);
        $this->assertArrayNotHasKey('block-3', $saved['blocks']);
        $this->assertCount($originalCount - 1, array_filter($saved['blocks'], fn (array $block) => $block['kind'] === 'person'));
        Storage::disk('public')->assertMissing($image);
    }

    public function test_team_member_keys_are_validated(): void
    {
        $section = $this->section('our-team');
        $data = $this->payload($section);
        $data['content']['blocks']['../../invalid'] = ['eyebrow' => '', 'title' => 'Invalid', 'body' => '', 'image_alt' => ''];
        $this->actingAs($this->administrator())->put('/admin/about-us/our-team', $data)
            ->assertSessionHasErrors('content.blocks');
        $this->assertArrayNotHasKey('../../invalid', $section->fresh()->content['blocks']);
    }

    public function test_failed_database_save_cleans_up_new_files_and_preserves_previous_images(): void
    {
        $section = $this->section();
        $before = Storage::disk('public')->allFiles();
        $this->actingAs($this->administrator());
        $this->withoutExceptionHandling();
        AboutSection::updating(function () {
            throw new RuntimeException('Simulated database failure');
        });
        try {
            $this->put('/admin/about-us/'.$section->slug, $this->payload($section) + [
                'hero_image' => UploadedFile::fake()->image('replacement.jpg'),
            ]);
            $this->fail('Expected the save to fail.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated database failure', $exception->getMessage());
        } finally {
            AboutSection::flushEventListeners();
        }
        $this->assertSame($before, Storage::disk('public')->allFiles());
        $this->assertSame($section->content, $section->fresh()->content);
    }

    public function test_reseeding_preserves_content_and_custom_uploads(): void
    {
        $section = $this->section();
        $content = $section->content;
        $content['hero_title'] = 'Keep my changes';
        $content['hero_image'] = UploadedFile::fake()->image('custom.png')->store('about-us/'.$section->slug, 'public');
        $section->update(['content' => $content]);
        $this->seed(AboutSectionSeeder::class);
        $this->assertDatabaseCount('about_sections', 4);
        $this->assertSame($content, $section->fresh()->content);
        Storage::disk('public')->assertExists($content['hero_image']);
    }

    public function test_frontend_text_is_escaped_and_image_paths_are_restricted(): void
    {
        require_once dirname(base_path()).'/includes/about-content.php';
        ob_start();
        about_paragraphs('<script>alert(1)</script>');
        $rendered = ob_get_clean();
        $this->assertStringContainsString('&lt;script&gt;', $rendered);
        $this->assertStringNotContainsString('<script>', $rendered);
        $this->assertNull(about_image_url('../../.env'));
        $this->assertNull(about_image_url('https://example.com/image.jpg'));
        $this->assertStringStartsWith('about-image.php?path=', about_image_url('about-us/our-team/image.jpg'));
    }
}
