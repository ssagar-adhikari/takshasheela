<?php

declare(strict_types=1);

function about_content(string $slug): array
{
    $backend = dirname(__DIR__).'/backend';
    $defaults = json_decode(file_get_contents($backend.'/database/seeders/data/about-sections.json'), true, 512, JSON_THROW_ON_ERROR);
    if (! isset($defaults[$slug])) {
        throw new InvalidArgumentException('Unknown About Us section.');
    }

    // Keep the public frontend usable before the Laravel database is installed.
    try {
        if (! is_file($backend.'/vendor/autoload.php')) {
            return $defaults[$slug];
        }
        require_once $backend.'/vendor/autoload.php';
        if (! Illuminate\Container\Container::getInstance()->bound('db')) {
            $cmsApplication = require $backend.'/bootstrap/app.php';
            $cmsApplication->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        }
        $section = App\Models\AboutSection::where('slug', $slug)->first();

        return $section?->content ?? $defaults[$slug];
    } catch (Throwable $exception) {
        error_log('About Us content unavailable: '.$exception->getMessage());

        return $defaults[$slug];
    }
}

function about_image_url(?string $path): ?string
{
    if (! $path) {
        return null;
    }
    if (preg_match('~^assets/images/[a-z0-9_-]+\.(?:jpe?g|png|webp)$~i', $path)) {
        return $path;
    }
    if (preg_match('~^about-us/[a-z0-9/_-]+\.(?:jpe?g|png|webp)$~i', $path)) {
        return 'about-image.php?path='.rawurlencode($path);
    }

    return null;
}

function about_paragraphs(?string $text): void
{
    foreach (preg_split('/\R\s*\R/', trim($text ?? ''), -1, PREG_SPLIT_NO_EMPTY) as $paragraph) {
        echo '<p>'.nl2br(htmlspecialchars($paragraph, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')).'</p>';
    }
}
