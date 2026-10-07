<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\WellnessCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer(['layouts.site', 'site.home', 'site.contact', 'layouts.auth', 'layouts.admin'], function ($view) {
            $settings = collect(config('site.defaults'))->merge(Setting::pluck('value', 'key'));
            $settings->put('logo_url', $settings->get('logo_path')
                ? Storage::disk('public')->url($settings->get('logo_path'))
                : asset('assets/images/logo.png'));

            $view->with('siteSettings', $settings);
        });

        View::composer('layouts.site', function ($view) {
            $view->with('wellnessCategories', WellnessCategory::published()
                ->with(['subcategories' => fn ($query) => $query->published()->with(['offerings' => fn ($query) => $query->published()])])
                ->orderBy('sort_order')->orderBy('name')->get());
        });
    }
}
