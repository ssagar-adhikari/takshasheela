<?php

namespace App\Support;

use Illuminate\Support\Str;

class Seo
{
    public static function url(string $url = '/'): string
    {
        // Use the configured public origin, never a tracking query or request host.
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $basePath = rtrim(parse_url(config('app.url'), PHP_URL_PATH) ?: '', '/');
        if ($basePath !== '' && ($path === $basePath || str_starts_with($path, $basePath.'/'))) {
            $path = substr($path, strlen($basePath));
        }

        return rtrim(config('app.url'), '/').'/'.ltrim($path, '/');
    }

    public static function text(?string $value): string
    {
        $decoded = html_entity_decode($value ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return Str::squish(html_entity_decode(strip_tags($decoded), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    public static function metadata(string $title, string $description, iterable $settings, array $data): array
    {
        $settings = collect($settings);
        $name = self::text($settings->get('site_name'));
        $title = self::text($title) ?: $name;
        $pageTitle = $title === $name ? $name : $title.' | '.$name;
        $description = Str::limit(self::text($description) ?: self::text($settings->get('default_meta_description')), 160);
        $canonical = self::url(request()->url());
        $image = self::url('/assets/images/hero.webp');
        $imageAlt = $name;
        $parent = null;
        $entity = null;
        $article = request()->routeIs('chronicles.show') ? ($data['article'] ?? null) : null;
        $offering = request()->routeIs('programs.show', 'therapies.show', 'trainings.show', 'wellness.offerings.show') ? ($data['offering'] ?? null) : null;
        $category = request()->routeIs('programs.*', 'therapies.*', 'trainings.*', 'wellness.*') ? ($data['category'] ?? null) : null;
        $product = request()->routeIs('products.show') ? ($data['product'] ?? null) : null;
        $room = request()->routeIs('accommodations.show') ? ($data['accommodation'] ?? null) : null;

        if ($offering) {
            $canonical = self::url($offering->publicUrl());
            $parent = [$category->name, self::url($category->publicUrl())];
            $entity = ['@type' => 'Service', 'name' => $offering->name, 'provider' => ['@id' => self::url().'#organization']];
        } elseif ($category) {
            $canonical = self::url($category->publicUrl());
        } elseif ($product) {
            $parent = ['Products', self::url(route('products.index'))];
            $entity = ['@type' => 'Product', 'name' => $product->name, 'category' => $product->category];
            // Only include an offer when the displayed price has an explicit supported currency.
            if (preg_match('/^(NPR|USD|EUR|GBP)\s+([0-9]+(?:,[0-9]{3})*(?:\.[0-9]{1,2})?)$/', trim($product->price ?? ''), $price)) {
                $entity['offers'] = ['@type' => 'Offer', 'url' => $canonical, 'priceCurrency' => $price[1], 'price' => str_replace(',', '', $price[2])];
            }
        } elseif ($room) {
            $parent = ['Accommodations', self::url(route('accommodations.index'))];
            $entity = ['@type' => 'Accommodation', 'name' => $room->name];
        } elseif ($article) {
            $parent = [$article->type === 'news' ? 'News & Events' : 'Blogs', self::url(route($article->type === 'news' ? 'news.index' : 'blogs.index'))];
            $entity = ['@type' => $article->type === 'news' ? 'Article' : 'BlogPosting', 'headline' => $article->title,
                'publisher' => ['@id' => self::url().'#organization'], 'mainEntityOfPage' => ['@id' => $canonical.'#webpage']];
            if ($article->published_at) {
                $entity['datePublished'] = $article->published_at->toAtomString();
            }
            if ($article->updated_at) {
                $entity['dateModified'] = $article->updated_at->toAtomString();
            }
        }

        $record = $offering ?? $product ?? $room ?? $article;
        if ($record?->imageUrl()) {
            $image = self::url($record->imageUrl());
            $imageAlt = $record->image_alt ?: $title;
        } elseif ($category?->heroImageUrl()) {
            $image = self::url($category->heroImageUrl());
            $imageAlt = $category->hero_image_alt ?: $title;
        } else {
            $path = $data['hero']['poster_path'] ?? $data['content']['hero_image'] ?? null;
            if ($path) {
                $image = self::url(str_starts_with($path, 'assets/') ? $path : 'storage/'.$path);
                $imageAlt = $data['hero']['poster_alt'] ?? $data['content']['hero_image_alt'] ?? $title;
            }
        }

        $organization = ['@type' => 'Organization', '@id' => self::url().'#organization', 'name' => $name,
            'url' => self::url(), 'logo' => self::url($settings->get('logo_url')), 'description' => self::text($settings->get('business_description'))];
        foreach (['email' => 'contact_email', 'telephone' => 'contact_phone', 'legalName' => 'legal_name'] as $property => $key) {
            if ($settings->get($key)) {
                $organization[$property] = $settings->get($key);
            }
        }
        $address = array_filter(['streetAddress' => $settings->get('address'), 'addressLocality' => $settings->get('city'),
            'postalCode' => $settings->get('postal_code'), 'addressCountry' => $settings->get('country')]);
        if ($address) {
            $organization['address'] = ['@type' => 'PostalAddress', ...$address];
        }
        $socials = $settings->only(['facebook_url', 'instagram_url', 'youtube_url', 'linkedin_url'])->filter()->values()->all();
        if ($socials) {
            $organization['sameAs'] = $socials;
        }

        $page = ['@type' => request()->routeIs('contact') ? 'ContactPage' : (request()->routeIs('about', 'team', 'approach') ? 'AboutPage' : 'WebPage'),
            '@id' => $canonical.'#webpage', 'url' => $canonical, 'name' => $pageTitle, 'description' => $description,
            'inLanguage' => 'en', 'isPartOf' => ['@id' => self::url().'#website']];
        $graph = [$organization, ['@type' => 'WebSite', '@id' => self::url().'#website', 'url' => self::url(),
            'name' => $name, 'publisher' => ['@id' => self::url().'#organization'], 'inLanguage' => 'en']];

        if (! request()->routeIs('home')) {
            $crumbs = [['Home', self::url()]];
            if ($parent) {
                $crumbs[] = $parent;
            }
            if ($offering?->subcategory) {
                $crumbs[] = [$offering->subcategory->name, $parent[1].'#'.$offering->subcategory->slug];
            }
            $crumbs[] = [$title, $canonical];
            $graph[] = ['@type' => 'BreadcrumbList', '@id' => $canonical.'#breadcrumb', 'itemListElement' => array_map(
                fn ($crumb, $index) => ['@type' => 'ListItem', 'position' => $index + 1, 'name' => $crumb[0], 'item' => $crumb[1]],
                $crumbs, array_keys($crumbs)
            )];
            $page['breadcrumb'] = ['@id' => $canonical.'#breadcrumb'];
        }
        if ($entity) {
            $entity = [...$entity, '@id' => $canonical.'#entity', 'url' => $canonical, 'description' => $description];
            if ($record?->imageUrl()) {
                $entity['image'] = $image;
            }
            $graph[] = $entity;
            $page['mainEntity'] = ['@id' => $canonical.'#entity'];
        }
        $graph[] = $page;

        return compact('pageTitle', 'description', 'canonical', 'image', 'imageAlt', 'article') + [
            'type' => $article ? 'article' : 'website',
            'json' => json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
        ];
    }
}
