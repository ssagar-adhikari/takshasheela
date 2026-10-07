<?php

namespace Database\Seeders;

use App\Models\HomeSection;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class HomeSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = config('home.sections');
        $settings = Setting::pluck('value', 'key');
        $sections['hero']['eyebrow'] = $settings['tagline'] ?? $sections['hero']['eyebrow'];
        $sections['hero']['description'] = $settings['business_description'] ?? $sections['hero']['description'];
        $sections['hero']['scroll_note'] = collect([$settings['city'] ?? null, $settings['country'] ?? null])->filter()->implode(', ')
            ?: ($settings['address'] ?? $sections['hero']['scroll_note']);
        $siteName = $settings['site_name'] ?? config('site.defaults.site_name');
        $sections['philosophy']['attribution'] = $siteName.' philosophy';

        foreach ($sections as $slug => $content) {
            HomeSection::firstOrCreate(['slug' => $slug], ['content' => $content]);
        }
    }
}
