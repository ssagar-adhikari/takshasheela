<?php

declare(strict_types=1);

function product_defaults(): array
{
    $source = dirname(__DIR__).'/backend/database/seeders/data/products.json';
    $records = json_decode(file_get_contents($source), true, 512, JSON_THROW_ON_ERROR);
    $products = [];

    foreach ($records as $record) {
        $slug = $record['slug'];
        unset($record['slug'], $record['is_published'], $record['sort_order']);
        $record['alt'] = $record['image_alt'];
        $record['short'] = $record['short_description'];
        $record['use'] = $record['usage'];
        unset($record['image_alt'], $record['short_description'], $record['usage']);
        $products[$slug] = $record;
    }

    return $products;
}

function products(): array
{
    $defaults = product_defaults();
    $backend = dirname(__DIR__).'/backend';

    try {
        if (! is_file($backend.'/vendor/autoload.php')) {
            return $defaults;
        }

        require_once $backend.'/vendor/autoload.php';
        if (! Illuminate\Container\Container::getInstance()->bound('db')) {
            $cmsApplication = require $backend.'/bootstrap/app.php';
            $cmsApplication->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        }

        $records = App\Models\Product::published()->orderBy('sort_order')->orderBy('name')->get();
        $products = [];

        foreach ($records as $record) {
            $products[$record->slug] = [
                'name' => $record->name,
                'category' => $record->category,
                'size' => $record->size,
                'price' => $record->price,
                'image' => product_image_url($record->image_path),
                'alt' => $record->image_alt,
                'short' => $record->short_description,
                'description' => $record->description,
                'ingredients' => $record->ingredients,
                'use' => $record->usage,
            ];
        }

        return $products;
    } catch (Throwable $exception) {
        error_log('Product content unavailable: '.$exception->getMessage());

        return $defaults;
    }
}

function product_image_url(?string $path): string
{
    if (! $path) {
        return '';
    }
    if (preg_match('~^assets/images/[a-z0-9_-]+\.(?:jpe?g|png|webp)$~i', $path)) {
        return $path;
    }
    if (preg_match('~^products/[a-z0-9/_-]+\.(?:jpe?g|png|webp)$~i', $path)) {
        return 'product-image.php?path='.rawurlencode($path);
    }

    return '';
}

function product_by_slug(string $slug): ?array
{
    $catalogue = products();

    return isset($catalogue[$slug]) ? ['slug' => $slug] + $catalogue[$slug] : null;
}
