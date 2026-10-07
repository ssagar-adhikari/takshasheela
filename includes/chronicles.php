<?php

declare(strict_types=1);

function chronicle_defaults(): array
{
    return json_decode(file_get_contents(dirname(__DIR__).'/backend/database/seeders/data/chronicles.json'), true, 512, JSON_THROW_ON_ERROR);
}

function chronicle_articles(?string $type = null): array
{
    $defaults = chronicle_defaults();
    $backend = dirname(__DIR__).'/backend';

    try {
        chronicle_boot_cms($backend);
        $query = App\Models\ChronicleArticle::published()->orderBy('sort_order')->orderBy('title');
        if ($type !== null) {
            $query->where('type', $type);
        }

        $articles = [];
        foreach ($query->get() as $record) {
            $articles[$record->slug] = [
                'type' => $record->type,
                'title' => $record->title,
                'category' => $record->category,
                'excerpt' => $record->excerpt,
                'body' => $record->body,
                'image' => chronicle_image_url($record->image_path),
                'alt' => $record->image_alt,
                'published_at' => $record->published_at?->format('F j, Y'),
                'featured' => $record->is_featured,
            ];
        }

        return $articles;
    } catch (Throwable $exception) {
        error_log('Chronicle articles unavailable: '.$exception->getMessage());
        $articles = [];
        foreach ($defaults['articles'] as $record) {
            if ($type !== null && $record['type'] !== $type) {
                continue;
            }
            $slug = $record['slug'];
            unset($record['slug'], $record['is_published'], $record['sort_order']);
            $record['alt'] = $record['image_alt'];
            $record['featured'] = $record['is_featured'];
            unset($record['image_alt'], $record['is_featured']);
            $articles[$slug] = $record;
        }

        return $articles;
    }
}

function chronicle_by_slug(string $slug): ?array
{
    $articles = chronicle_articles();

    return isset($articles[$slug]) ? ['slug' => $slug] + $articles[$slug] : null;
}

function chronicle_featured(array $articles): ?array
{
    foreach ($articles as $slug => $article) {
        if ($article['featured']) {
            return ['slug' => $slug] + $article;
        }
    }
    $slug = array_key_first($articles);

    return $slug === null ? null : ['slug' => $slug] + $articles[$slug];
}

function chronicle_testimonials(): array
{
    $defaults = chronicle_defaults();
    $backend = dirname(__DIR__).'/backend';

    try {
        chronicle_boot_cms($backend);

        return App\Models\Testimonial::published()->orderBy('sort_order')->orderBy('guest_name')->get()
            ->map(fn ($record) => [
                'title' => $record->title,
                'quote' => $record->quote,
                'guest_name' => $record->guest_name,
                'guest_location' => $record->guest_location,
                'rating' => $record->rating,
                'initials' => $record->initials(),
            ])->all();
    } catch (Throwable $exception) {
        error_log('Testimonials unavailable: '.$exception->getMessage());

        return array_map(function (array $record) {
            $record['initials'] = chronicle_initials($record['guest_name']);
            unset($record['is_published'], $record['sort_order']);

            return $record;
        }, $defaults['testimonials']);
    }
}

function chronicle_gallery(): array
{
    $defaults = chronicle_defaults();
    $backend = dirname(__DIR__).'/backend';

    try {
        chronicle_boot_cms($backend);

        return App\Models\GalleryItem::published()->orderBy('sort_order')->get()
            ->map(fn ($record) => [
                'image' => chronicle_image_url($record->image_path),
                'alt' => $record->image_alt,
                'caption' => $record->caption,
            ])->all();
    } catch (Throwable $exception) {
        error_log('Gallery unavailable: '.$exception->getMessage());

        return array_map(fn (array $record) => [
            'image' => $record['image'],
            'alt' => $record['image_alt'],
            'caption' => $record['caption'],
        ], $defaults['gallery']);
    }
}

function chronicle_boot_cms(string $backend): void
{
    if (! is_file($backend.'/vendor/autoload.php')) {
        throw new RuntimeException('Laravel is not installed.');
    }

    require_once $backend.'/vendor/autoload.php';
    if (! Illuminate\Container\Container::getInstance()->bound('db')) {
        $cmsApplication = require $backend.'/bootstrap/app.php';
        $cmsApplication->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    }
}

function chronicle_image_url(?string $path): string
{
    if (! $path) {
        return '';
    }
    if (preg_match('~^assets/images/[a-z0-9_-]+\.(?:jpe?g|png|webp)$~i', $path)) {
        return $path;
    }
    if (preg_match('~^(?:chronicles|gallery)/[a-z0-9/_-]+\.(?:jpe?g|png|webp)$~i', $path)) {
        return 'chronicle-image.php?path='.rawurlencode($path);
    }

    return '';
}

function chronicle_initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY);

    return mb_strtoupper(implode('', array_map(fn (string $part) => mb_substr($part, 0, 1), array_slice($parts, 0, 2))));
}

function chronicle_paragraphs(string $body): void
{
    foreach (preg_split('/\R\s*\R/', trim($body), -1, PREG_SPLIT_NO_EMPTY) as $paragraph) {
        echo '<p>'.nl2br(htmlspecialchars($paragraph, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')).'</p>';
    }
}
