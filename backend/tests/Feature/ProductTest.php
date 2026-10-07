<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(ProductSeeder::class);
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
            'name' => 'Nourishing Hair Oil',
            'category' => 'Hair care',
            'size' => '100 ml',
            'price' => 'NPR 1,250',
            'image_alt' => 'Bottle of Nourishing Hair Oil',
            'short_description' => 'A botanical hair oil for a calming weekly scalp-care ritual.',
            'description' => 'A nourishing blend of traditional oils and botanicals made for gentle scalp massage.',
            'ingredients_text' => "Coconut oil\nBhringraj\nAmla\nBrahmi",
            'usage' => 'Massage a small amount into the scalp and leave for 30 minutes before washing.',
            'sort_order' => 50,
            'is_published' => 1,
        ], $overrides);
    }

    public function test_defaults_are_seeded_and_read_by_the_public_catalogue_and_details_source(): void
    {
        $this->assertDatabaseCount('products', 4);
        $this->assertSame(
            ['abhyanga-body-oil', 'tulsi-calm-tea', 'amla-chyawanprash', 'shanti-night-serum'],
            Product::orderBy('sort_order')->pluck('slug')->all()
        );

        require_once dirname(base_path()).'/includes/products.php';
        $products = products();

        $this->assertSame('Abhyanga Body Oil', product_by_slug('abhyanga-body-oil')['name']);
        $this->assertStringStartsWith('product-image.php?path=', $products['abhyanga-body-oil']['image']);
        $this->assertSame('Bottle of Abhyanga Body Oil', $products['abhyanga-body-oil']['alt']);
        Storage::disk('public')->assertExists(Product::where('slug', 'abhyanga-body-oil')->value('image_path'));
    }

    public function test_only_administrators_can_manage_products(): void
    {
        foreach (['/admin/products', '/admin/products/create', '/admin/products/1/edit'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }

        $this->actingAs(User::factory()->create());
        $this->get('/admin/products')->assertForbidden();
        $this->post('/admin/products', $this->payload())->assertForbidden();
        $this->put('/admin/products/1', $this->payload())->assertForbidden();
        $this->delete('/admin/products/1')->assertForbidden();
    }

    public function test_administrator_can_create_a_published_product_with_an_uploaded_image(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)
            ->get('/admin/products')
            ->assertOk()
            ->assertSee('Abhyanga Body Oil')
            ->assertSee('Shanti Night Serum');

        $this->get('/admin/products/create')
            ->assertOk()
            ->assertSee('name="image"', false)
            ->assertSee('name="ingredients_text"', false)
            ->assertDontSee('name="slug"', false);

        $this->get('/admin/products/'.Product::where('slug', 'abhyanga-body-oil')->value('id').'/edit')
            ->assertOk()
            ->assertSee('Abhyanga Body Oil')
            ->assertSee('products/abhyanga-body-oil/defaults/product-abhyanga-oil.png');

        $response = $this->post('/admin/products', $this->payload() + [
            'image' => UploadedFile::fake()->image('hair-oil.png', 900, 900),
        ]);

        $product = Product::where('slug', 'nourishing-hair-oil')->firstOrFail();
        $response->assertRedirect(route('admin.products.edit', $product))->assertSessionHasNoErrors();
        $this->assertSame(['Coconut oil', 'Bhringraj', 'Amla', 'Brahmi'], $product->ingredients);
        $this->assertSame($admin->id, $product->updated_by);
        Storage::disk('public')->assertExists($product->image_path);

        require_once dirname(base_path()).'/includes/products.php';
        $this->assertSame('Nourishing Hair Oil', products()['nourishing-hair-oil']['name']);
    }

    public function test_administrator_can_update_publish_order_and_replace_an_image(): void
    {
        $product = Product::where('slug', 'abhyanga-body-oil')->firstOrFail();
        $oldImage = $product->image_path;

        $this->actingAs($this->administrator())->put(
            '/admin/products/'.$product->id,
            $this->payload([
                'name' => 'Updated Abhyanga Oil',
                'price' => 'NPR 1,950',
                'sort_order' => 5,
                'is_published' => 0,
                'ingredients_text' => "Sesame oil\nBala",
            ]) + ['image' => UploadedFile::fake()->image('replacement.webp', 800, 800)]
        )->assertSessionHasNoErrors()->assertSessionHas('status');

        $saved = $product->fresh();
        $this->assertSame('Updated Abhyanga Oil', $saved->name);
        $this->assertSame('abhyanga-body-oil', $saved->slug);
        $this->assertSame('NPR 1,950', $saved->price);
        $this->assertSame(['Sesame oil', 'Bala'], $saved->ingredients);
        $this->assertFalse($saved->is_published);
        Storage::disk('public')->assertExists($saved->image_path);
        Storage::disk('public')->assertMissing($oldImage);

        require_once dirname(base_path()).'/includes/products.php';
        $this->assertArrayNotHasKey('abhyanga-body-oil', products());
    }

    public function test_deleting_a_product_removes_its_uploaded_image(): void
    {
        $product = Product::where('slug', 'tulsi-calm-tea')->firstOrFail();
        $image = $product->image_path;

        $this->actingAs($this->administrator())
            ->delete('/admin/products/'.$product->id)
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('status');

        $this->assertModelMissing($product);
        Storage::disk('public')->assertMissing($image);
    }

    public function test_duplicate_names_receive_unique_slugs_and_unsafe_images_are_rejected(): void
    {
        $this->actingAs($this->administrator());

        $this->post('/admin/products', $this->payload([
            'name' => 'Abhyanga Body Oil',
        ]) + ['image' => UploadedFile::fake()->image('product.jpg')])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', ['slug' => 'abhyanga-body-oil-2']);

        $this->post('/admin/products', $this->payload() + [
            'image' => UploadedFile::fake()->create('product.svg', 5, 'image/svg+xml'),
        ])->assertSessionHasErrors('image');

        require_once dirname(base_path()).'/includes/products.php';
        $this->assertSame('', product_image_url('../../.env'));
        $this->assertDatabaseCount('products', 5);
    }
}
