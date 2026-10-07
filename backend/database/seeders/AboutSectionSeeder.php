<?php

namespace Database\Seeders;

use App\Models\AboutSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class AboutSectionSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = json_decode(file_get_contents(__DIR__.'/data/about-sections.json'), true, 512, JSON_THROW_ON_ERROR);

        foreach ($defaults as $slug => $content) {
            if (AboutSection::where('slug', $slug)->exists()) {
                continue;
            }

            $content['hero_image'] = $this->importImage($slug, $content['hero_image']);
            foreach ($content['blocks'] as &$block) {
                if ($block['image']) {
                    $block['image'] = $this->importImage($slug, $block['image']);
                }
            }
            unset($block);

            AboutSection::create(['slug' => $slug, 'content' => $content]);
        }
    }

    private function importImage(string $slug, string $source): string
    {
        $path = 'about-us/'.$slug.'/defaults/'.basename($source);
        $sourcePath = dirname(base_path()).'/'.$source;
        if (! is_file($sourcePath) || ! Storage::disk('public')->put($path, file_get_contents($sourcePath))) {
            throw new RuntimeException('Could not import About Us image: '.$source);
        }

        return $path;
    }
}
