<?php

declare(strict_types=1);

// Serve only accommodation raster images, without exposing other Laravel storage.
$path = $_GET['path'] ?? '';
$root = realpath(__DIR__.'/backend/storage/app/public');
$file = is_string($path) && preg_match('~^accommodations/[a-z0-9/_-]+\.(?:jpe?g|png|webp)$~i', $path) && $root
    ? realpath($root.'/'.$path)
    : false;

if (! $file || ! str_starts_with($file, $root.DIRECTORY_SEPARATOR.'accommodations'.DIRECTORY_SEPARATOR) || ! is_file($file)) {
    http_response_code(404);
    exit;
}

$mime = (new finfo(FILEINFO_MIME_TYPE))->file($file);
if (! in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
    http_response_code(404);
    exit;
}

header('Content-Type: '.$mime);
header('Content-Length: '.filesize($file));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=3600');
readfile($file);
