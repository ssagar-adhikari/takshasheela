<?php

// Development router for the existing frontend. Keep backend secrets and
// application files inaccessible when the repository root is the document root.
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$file = str_contains($path, "\0") ? false : realpath(__DIR__.$path);
$assetRoot = realpath(__DIR__.'/assets').DIRECTORY_SEPARATOR;
$publicPages = array_map('basename', glob(__DIR__.'/*.php'));
$publicPages = array_diff($publicPages, ['router.php']);

if ($path === '/' || ($file && is_file($file) && (
    (dirname($file) === __DIR__ && in_array(basename($file), [...$publicPages, 'robots.txt'], true)) ||
    (str_starts_with($file, $assetRoot) && preg_match('/\.(?:css|js|png|jpg|jpeg|webp|gif|svg|ico|mp4|webm|woff2?|ttf|avif)$/i', $file))
))) {
    return false;
}

http_response_code(404);
header('Content-Type: text/plain; charset=UTF-8');
echo 'Not found';
