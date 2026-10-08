<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\ChronicleArticle;
use App\Models\Product;
use App\Models\WellnessCategory;
use App\Support\Seo;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = [];
        foreach (['home', 'about', 'ayurveda', 'team', 'approach', 'accommodations.index', 'products.index',
            'blogs.index', 'news.index', 'testimonials', 'gallery', 'contact', 'privacy'] as $route) {
            $urls[] = ['loc' => Seo::url(route($route))];
        }
        foreach ([Accommodation::class => 'accommodations.show', Product::class => 'products.show', ChronicleArticle::class => 'chronicles.show'] as $model => $route) {
            foreach ($model::published()->select('id', 'slug', 'updated_at')->orderBy('id')->cursor() as $record) {
                $urls[] = ['loc' => Seo::url(route($route, $record->slug)), 'lastmod' => $record->updated_at?->toAtomString()];
            }
        }
        foreach (WellnessCategory::published()->with(['offerings' => fn ($query) => $query->published()])->orderBy('id')->get() as $category) {
            $urls[] = ['loc' => Seo::url($category->publicUrl())];
            foreach ($category->offerings as $offering) {
                $offering->setRelation('category', $category);
                $urls[] = ['loc' => Seo::url($offering->publicUrl()), 'lastmod' => $offering->updated_at?->toAtomString()];
            }
        }

        return response()->view('site.sitemap', compact('urls'))->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        // Login pages remain crawlable so their noindex directives can be read.
        return response("User-agent: *\nAllow: /\n\nSitemap: ".Seo::url('/sitemap.xml')."\n")
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
