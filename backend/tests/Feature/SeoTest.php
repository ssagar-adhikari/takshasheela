<?php

namespace Tests\Feature;

use App\Models\ChronicleArticle;
use App\Models\Product;
use App\Models\Setting;
use App\Models\WellnessCategory;
use App\Models\WellnessOffering;
use App\Support\Seo;
use Database\Seeders\AboutSectionSeeder;
use Database\Seeders\AccommodationSeeder;
use Database\Seeders\ChronicleSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\WellnessSeeder;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'https://retreat.example']);
        $this->seed([AboutSectionSeeder::class, AccommodationSeeder::class, ProductSeeder::class, ChronicleSeeder::class, WellnessSeeder::class]);
    }

    private function document(string $html): DOMXPath
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return new DOMXPath($document);
    }

    private function graph(string $html): array
    {
        $xpath = $this->document($html);
        $scripts = $xpath->query('//script[@type="application/ld+json"]');
        $this->assertCount(1, $scripts);

        return json_decode($scripts->item(0)->textContent, true, 512, JSON_THROW_ON_ERROR)['@graph'];
    }

    public function test_sitemap_contains_unique_canonical_public_pages_with_valid_metadata(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $urls = array_map(fn ($entry) => (string) $entry->loc, iterator_to_array($xml->url, false));
        $this->assertGreaterThan(30, count($urls));
        $this->assertCount(count(array_unique($urls)), $urls);
        $this->assertContains('https://retreat.example/programs/panchakarma-rejuvenation', $urls);
        $this->assertNotContains('https://retreat.example/wellness/programs', $urls);
        $this->assertNotContains('https://retreat.example/login', $urls);
        $this->assertNotContains('https://retreat.example/admin', $urls);

        foreach ($urls as $url) {
            $this->assertStringStartsWith('https://retreat.example/', $url);
            $html = $this->get(parse_url($url, PHP_URL_PATH))->assertOk()->getContent();
            $xpath = $this->document($html);
            $this->assertSame($url, $xpath->evaluate('string(//link[@rel="canonical"]/@href)'));
            $this->assertCount(1, $xpath->query('//title'));
            $this->assertCount(1, $xpath->query('//h1'));
            $description = $xpath->evaluate('string(//meta[@name="description"]/@content)');
            $this->assertNotEmpty($description);
            $this->assertSame(strip_tags($description), $description);
            $this->assertSame($url, $xpath->evaluate('string(//meta[@property="og:url"]/@content)'));
            $this->assertSame('summary_large_image', $xpath->evaluate('string(//meta[@name="twitter:card"]/@content)'));
            $this->graph($html);
        }
    }

    public function test_drafts_and_offerings_under_unpublished_parents_are_excluded_from_sitemap(): void
    {
        Product::where('slug', 'abhyanga-body-oil')->update(['is_published' => false]);
        $article = ChronicleArticle::firstOrFail();
        $article->update(['is_published' => false]);
        $offering = WellnessOffering::where('slug', 'panchakarma-rejuvenation')->firstOrFail();
        $offering->subcategory->update(['is_published' => false]);
        WellnessCategory::where('slug', 'trainings')->update(['is_published' => false]);

        $this->get('/sitemap.xml')->assertOk()
            ->assertDontSee('/products/abhyanga-body-oil')
            ->assertDontSee('/chronicles/'.$article->slug)
            ->assertDontSee('/programs/panchakarma-rejuvenation')
            ->assertDontSee('/trainings');
    }

    public function test_canonical_removes_tracking_and_enquiry_parameters_and_uses_configured_origin(): void
    {
        $this->get('/contact?interest=Abhyanga%20Body%20Oil&utm_source=test')->assertOk()
            ->assertSee('<link rel="canonical" href="https://retreat.example/contact">', false);
        $this->get('/wellness/programs/panchakarma-rejuvenation')->assertOk()
            ->assertSee('<link rel="canonical" href="https://retreat.example/programs/panchakarma-rejuvenation">', false);
        $this->get('/wellness/programs')->assertOk()
            ->assertSee('<link rel="canonical" href="https://retreat.example/programs">', false);
    }

    public function test_detail_schema_uses_cms_content_and_does_not_leak_to_listing_pages(): void
    {
        $article = ChronicleArticle::where('type', 'blog')->firstOrFail();
        $article->update(['published_at' => '2026-01-15 10:00:00']);
        $graph = collect($this->graph($this->get('/chronicles/'.$article->slug)->assertOk()->getContent()));
        $schema = $graph->firstWhere('@type', 'BlogPosting');
        $this->assertSame($article->title, $schema['headline']);
        $this->assertSame($article->published_at->toAtomString(), $schema['datePublished']);
        $this->assertSame(3, count($graph->firstWhere('@type', 'BreadcrumbList')['itemListElement']));

        $graph = collect($this->graph($this->get('/products/abhyanga-body-oil')->assertOk()->getContent()));
        $schema = $graph->firstWhere('@type', 'Product');
        $this->assertSame('1850', $schema['offers']['price']);
        $this->assertSame('NPR', $schema['offers']['priceCurrency']);
        $this->assertArrayNotHasKey('aggregateRating', $schema);
        $this->assertArrayNotHasKey('availability', $schema['offers']);

        foreach (['/', '/products', '/blogs', '/news'] as $url) {
            $graph = collect($this->graph($this->get($url)->assertOk()->getContent()));
            $this->assertNull($graph->firstWhere('@type', 'Product'));
            $this->assertNull($graph->firstWhere('@type', 'BlogPosting'));
        }
    }

    public function test_cms_metadata_is_plain_text_and_json_is_safe(): void
    {
        Setting::create(['key' => 'site_name', 'value' => 'Retreat </script><script>alert(1)</script>']);
        Product::where('slug', 'abhyanga-body-oil')->update([
            'name' => 'Oil "special" </script><script>alert(2)</script>',
            'short_description' => '<p>Care &amp; rest.</p>',
            'price' => 'Contact us',
        ]);
        $html = $this->get('/products/abhyanga-body-oil')->assertOk()->getContent();
        $xpath = $this->document($html);
        $this->assertSame('Care & rest.', $xpath->evaluate('string(//meta[@name="description"]/@content)'));
        $this->assertStringNotContainsString('<script>alert(2)</script>', $html);
        $schema = collect($this->graph($html))->firstWhere('@type', 'Product');
        $this->assertArrayNotHasKey('offers', $schema);
    }

    public function test_robots_advertises_sitemap_and_auth_pages_are_noindex(): void
    {
        $this->assertFileDoesNotExist(public_path('robots.txt'));
        $this->get('/robots.txt')->assertOk()->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Sitemap: https://retreat.example/sitemap.xml');
        foreach (['/login', '/forgot-password', '/reset-password/example-token'] as $url) {
            $this->get($url)->assertOk()->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        }
    }

    public function test_legacy_urls_permanently_redirect(): void
    {
        foreach (['/index.php' => '/', '/stay.php' => '/accommodations', '/journal.php' => '/blogs',
            '/product.php?slug=abhyanga-body-oil' => '/products/abhyanga-body-oil'] as $old => $new) {
            $this->get($old)->assertStatus(301)->assertRedirect($new);
        }
    }

    public function test_canonical_url_supports_a_configured_subdirectory(): void
    {
        config(['app.url' => 'https://retreat.example/aashram']);
        $this->assertSame('https://retreat.example/aashram/products', Seo::url('http://localhost/aashram/products?utm_source=test'));
        $this->assertSame('https://retreat.example/aashram/sitemap.xml', Seo::url('/sitemap.xml'));
    }
}
