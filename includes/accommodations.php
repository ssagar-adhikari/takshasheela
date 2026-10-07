<?php

declare(strict_types=1);

function accommodation_defaults(): array
{
    $source = dirname(__DIR__).'/backend/database/seeders/data/accommodations.json';
    $records = json_decode(file_get_contents($source), true, 512, JSON_THROW_ON_ERROR);
    $accommodations = [];

    foreach ($records as $record) {
        $slug = $record['slug'];
        unset($record['slug'], $record['is_published'], $record['sort_order']);
        $record['alt'] = $record['image_alt'];
        $record['short'] = $record['short_description'];
        unset($record['image_alt'], $record['short_description']);
        $accommodations[$slug] = $record;
    }

    return $accommodations;
}

function accommodations(): array
{
    $defaults = accommodation_defaults();
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

        $records = App\Models\Accommodation::published()->orderBy('sort_order')->orderBy('name')->get();
        $accommodations = [];

        foreach ($records as $record) {
            $accommodations[$record->slug] = [
                'name' => $record->name,
                'category' => $record->category,
                'heading' => $record->heading,
                'image' => accommodation_image_url($record->image_path),
                'alt' => $record->image_alt,
                'short' => $record->short_description,
                'description' => $record->description,
                'highlights' => $record->highlights,
                'features' => $record->features,
                'ideal_for' => $record->ideal_for,
                'stay_note' => $record->stay_note,
            ];
        }

        return $accommodations;
    } catch (Throwable $exception) {
        error_log('Accommodation content unavailable: '.$exception->getMessage());

        return $defaults;
    }
}

function accommodation_image_url(?string $path): string
{
    if (! $path) {
        return '';
    }
    if (preg_match('~^assets/images/[a-z0-9_-]+\.(?:jpe?g|png|webp)$~i', $path)) {
        return $path;
    }
    if (preg_match('~^accommodations/[a-z0-9/_-]+\.(?:jpe?g|png|webp)$~i', $path)) {
        return 'accommodation-image.php?path='.rawurlencode($path);
    }

    return '';
}

function accommodation_by_slug(string $slug): ?array
{
    $rooms = accommodations();

    return isset($rooms[$slug]) ? ['slug' => $slug] + $rooms[$slug] : null;
}
