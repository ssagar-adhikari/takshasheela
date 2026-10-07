<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class AccommodationSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = json_decode(file_get_contents(__DIR__.'/data/accommodations.json'), true, 512, JSON_THROW_ON_ERROR);

        foreach ($defaults as $values) {
            if (Accommodation::where('slug', $values['slug'])->exists()) {
                continue;
            }

            $source = $values['image'];
            unset($values['image']);
            $values['image_path'] = $this->importImage($values['slug'], $source);

            Accommodation::create($values);
        }
    }

    private function importImage(string $slug, string $source): string
    {
        $path = 'accommodations/'.$slug.'/defaults/'.basename($source);
        $sourcePath = dirname(base_path()).'/'.$source;

        if (! is_file($sourcePath) || ! Storage::disk('public')->put($path, file_get_contents($sourcePath))) {
            throw new RuntimeException('Could not import accommodation image: '.$source);
        }

        return $path;
    }
}
