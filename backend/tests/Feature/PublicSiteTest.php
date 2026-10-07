<?php

namespace Tests\Feature;

use App\Mail\ContactEnquiry;
use App\Models\AboutSection;
use App\Models\Accommodation;
use App\Models\ChronicleArticle;
use App\Models\Enquiry;
use App\Models\Product;
use App\Models\Setting;
use Database\Seeders\AboutSectionSeeder;
use Database\Seeders\AccommodationSeeder;
use Database\Seeders\ChronicleSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\WellnessSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([AboutSectionSeeder::class, WellnessSeeder::class, AccommodationSeeder::class, ProductSeeder::class, ChronicleSeeder::class]);
        foreach ([
            'site_name' => 'Takshasheela Ayurveda Aashram',
            'tagline' => 'Nature heals — we guide',
            'contact_email' => 'info@takshasheela.com',
            'contact_phone' => '',
            'address' => 'Kathmandu, Nepal',
        ] as $key => $value) {
            Setting::create(compact('key', 'value'));
        }
    }

    public function test_all_public_laravel_pages_render(): void
    {
        $urls = [
            '/', '/about', '/ayurveda', '/team', '/approach',
            '/programs', '/programs/panchakarma-rejuvenation',
            '/therapies', '/therapies/panchakarma-detox-therapy',
            '/trainings', '/trainings/ayurveda-foundations',
            '/accommodations', '/accommodations/standard-room',
            '/products', '/products/abhyanga-body-oil',
            '/blogs', '/news', '/chronicles/what-happens-on-a-takshasheela-healing-retreat',
            '/testimonials', '/gallery', '/contact', '/privacy',
        ];

        foreach ($urls as $url) {
            $this->assertSame(200, $this->get($url)->status(), 'Failed public URL: '.$url);
        }
    }

    public function test_product_detail_uses_the_visible_detail_page_navigation_theme(): void
    {
        $this->get('/products/abhyanga-body-oil')
            ->assertOk()
            ->assertSee('body class="site-theme-detail"', false)
            ->assertSee('Wellness Programs');
    }

    public function test_cms_content_and_settings_are_visible_on_the_public_frontend(): void
    {
        $about = AboutSection::where('slug', 'about-takshasheela')->firstOrFail();
        $content = $about->content;
        $content['hero_title'] = 'A CMS managed About heading';
        $about->update(['content' => $content]);

        Setting::where('key', 'tagline')->update(['value' => 'A CMS managed footer tagline']);
        foreach ([
            'business_description' => 'A CMS managed business description',
            'logo_path' => 'site-settings/logo/cms-logo.png',
            'logo_alt' => 'CMS managed logo',
            'contact_phone' => '+977 1 5550000',
            'alternate_phone' => '+977 1 5550001',
            'whatsapp_phone' => '+977 9800000000',
            'business_hours' => 'Sunday–Friday: 9:00 AM–6:00 PM',
            'response_time' => 'Within one working day',
            'map_embed_url' => 'https://maps.example.com/embed/takshasheela',
            'map_directions_url' => 'https://maps.example.com/directions/takshasheela',
            'instagram_url' => 'https://instagram.com/takshasheela',
        ] as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Product::where('slug', 'abhyanga-body-oil')->update(['name' => 'CMS Managed Body Oil']);

        $this->get('/about')->assertOk()->assertSee('A CMS managed About heading');
        $this->get('/')->assertOk()->assertSee('A CMS managed footer tagline')->assertSee('A CMS managed business description')->assertSee('CMS Managed Body Oil')->assertSee('/storage/site-settings/logo/cms-logo.png', false);
        $this->get('/contact')->assertOk()->assertSee('+977 1 5550000')->assertSee('Within one working day')->assertSee('https://maps.example.com/embed/takshasheela', false)->assertSee('https://instagram.com/takshasheela', false)->assertSee('class="social-icon"', false)->assertSee('<svg', false);
    }

    public function test_draft_content_is_hidden_and_cannot_be_opened_directly(): void
    {
        $room = Accommodation::where('slug', 'standard-room')->firstOrFail();
        $product = Product::where('slug', 'abhyanga-body-oil')->firstOrFail();
        $article = ChronicleArticle::where('slug', 'what-happens-on-a-takshasheela-healing-retreat')->firstOrFail();

        $room->update(['is_published' => false]);
        $product->update(['is_published' => false]);
        $article->update(['is_published' => false]);

        $this->get('/accommodations')->assertDontSee($room->name);
        $this->get('/products')->assertDontSee($product->name);
        $this->get('/blogs')->assertDontSee($article->title);
        $this->get('/accommodations/'.$room->slug)->assertNotFound();
        $this->get('/products/'.$product->slug)->assertNotFound();
        $this->get('/chronicles/'.$article->slug)->assertNotFound();
    }

    public function test_contact_form_uses_dynamic_interests_and_sends_with_laravel_mail(): void
    {
        Mail::fake();
        Setting::updateOrCreate(['key' => 'enquiry_email'], ['value' => 'enquiries@takshasheela.com']);

        $this->get('/contact?interest=Abhyanga%20Body%20Oil')
            ->assertOk()
            ->assertSee('value="product" selected', false)
            ->assertSee('Abhyanga Body Oil');

        $this->post('/contact', [
            'name' => 'Maya Rai',
            'email' => 'maya@example.com',
            'phone' => '+977 9800000000',
            'enquiry_type' => 'product',
            'interest' => 'Abhyanga Body Oil',
            'message' => 'Please tell me whether this product is available.',
            'consent' => '1',
            'website' => '',
        ])->assertRedirect(route('contact'))->assertSessionHas('status');

        $this->assertDatabaseHas('enquiries', [
            'name' => 'Maya Rai',
            'email' => 'maya@example.com',
            'enquiry_type' => 'product',
            'enquiry_type_label' => 'Product',
            'interest' => 'Abhyanga Body Oil',
            'status' => 'new',
            'consent' => true,
        ]);
        $this->assertNotNull(Enquiry::firstOrFail()->emailed_at);

        Mail::assertSent(ContactEnquiry::class, function (ContactEnquiry $mail) {
            $replyTo = $mail->envelope()->replyTo[0];
            $mail->assertSeeInHtml('Please tell me whether this product is available.');

            return $mail->hasTo('enquiries@takshasheela.com')
                && $mail->enquiry->interest === 'Abhyanga Body Oil'
                && $mail->siteName === 'Takshasheela Ayurveda Aashram'
                && $replyTo->address === 'maya@example.com'
                && $replyTo->name === 'Maya Rai';
        });
    }

    public function test_legacy_php_urls_redirect_to_laravel_routes(): void
    {
        $this->get('/stay.php')->assertRedirect('/accommodations');
        $this->get('/product.php?slug=abhyanga-body-oil')->assertRedirect(route('products.show', 'abhyanga-body-oil'));
        $this->get('/chronicle.php?slug=what-happens-on-a-takshasheela-healing-retreat')
            ->assertRedirect(route('chronicles.show', 'what-happens-on-a-takshasheela-healing-retreat'));
    }
}
