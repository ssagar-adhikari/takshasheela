<?php

namespace Database\Seeders;

use App\Models\ChronicleArticle;
use App\Models\GalleryItem;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ChronicleSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = json_decode(file_get_contents(__DIR__.'/data/chronicles.json'), true, 512, JSON_THROW_ON_ERROR);

        foreach ($defaults['articles'] as $values) {
            if (ChronicleArticle::where('slug', $values['slug'])->exists()) {
                continue;
            }

            $source = $values['image'];
            unset($values['image']);
            $values['image_path'] = $this->importImage('chronicles/'.$values['slug'], $source);
            ChronicleArticle::create($values);
        }

        foreach ($defaults['testimonials'] as $values) {
            Testimonial::firstOrCreate([
                'title' => $values['title'],
                'guest_name' => $values['guest_name'],
            ], $values);
        }

        foreach ($defaults['gallery'] as $values) {
            if (GalleryItem::where('sort_order', $values['sort_order'])->exists()) {
                continue;
            }

            $source = $values['image'];
            unset($values['image']);
            $values['image_path'] = $this->importImage('gallery/item-'.$values['sort_order'], $source);
            GalleryItem::create($values);
        }
    }

    private function importImage(string $directory, string $source): string
    {
        $path = $directory.'/defaults/'.basename($source);
        $sourcePath = dirname(base_path()).'/'.$source;

        if (! is_file($sourcePath) || ! Storage::disk('public')->put($path, file_get_contents($sourcePath))) {
            throw new RuntimeException('Could not import Chronicle image: '.$source);
        }

        return $path;
    }
}
