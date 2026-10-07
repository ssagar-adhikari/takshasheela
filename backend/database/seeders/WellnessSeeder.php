<?php

namespace Database\Seeders;

use App\Models\WellnessCategory;
use App\Models\WellnessOffering;
use App\Models\WellnessSubcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class WellnessSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'programs' => [
                'name' => 'Packages',
                'nav_description' => 'Immersive retreat programs',
                'hero_eyebrow' => 'Wellness programs',
                'hero_title' => 'Explore our natural healing packages.',
                'hero_description' => 'At Takshasheela Ayurveda Aashram, we offer powerful, soul-nourishing retreats inspired by authentic Ayurvedic tradition.',
                'hero_image' => 'assets/images/water-hero.png',
                'hero_image_alt' => 'A peaceful water setting at Takshasheela',
                'section_eyebrow' => 'Packages',
                'section_title' => 'Personalised paths to healing and renewal.',
                'information_eyebrow' => null,
                'information_title' => null,
                'information_body' => null,
                'cta_eyebrow' => 'Choose your retreat',
                'cta_title' => 'Speak with our team about the package that best matches your needs.',
                'cta_body' => 'Tell us what you need. We will help you choose a thoughtful next step.',
                'notice' => 'Every program begins with assessment. Treatments and schedules may be adapted for your needs and safety.',
                'secondary_title' => 'Your daily rhythm',
                'secondary_key' => 'rhythm',
                'source' => 'packages',
                'sort_order' => 10,
            ],
            'therapies' => [
                'name' => 'Services',
                'nav_description' => 'Focused Ayurvedic care',
                'hero_eyebrow' => 'Ayurvedic healing services',
                'hero_title' => 'Explore our Ayurvedic healing services.',
                'hero_description' => 'Powerful, soul-nourishing services inspired by authentic Ayurvedic tradition and selected after assessment.',
                'hero_image' => 'assets/images/therapy.jpg',
                'hero_image_alt' => 'A restorative Ayurvedic therapy',
                'section_eyebrow' => 'Our services',
                'section_title' => 'Authentic therapies and personalised support.',
                'information_eyebrow' => 'Important to know',
                'information_title' => 'Therapy follows assessment.',
                'information_body' => "Every treatment is selected after a practitioner considers your constitution, health needs, and present condition.\n\nAyurvedic wellness care is complementary and does not replace diagnosis or treatment from a qualified medical professional.",
                'cta_eyebrow' => 'Explore personalised care',
                'cta_title' => 'Tell us what brings you here and our team will guide you.',
                'cta_body' => 'Every therapy starts with a conversation and assessment.',
                'notice' => 'Ayurvedic wellness care is complementary and does not replace diagnosis or treatment from a qualified medical professional.',
                'secondary_title' => 'How it begins',
                'secondary_key' => 'process',
                'source' => 'services',
                'sort_order' => 20,
            ],
            'trainings' => [
                'name' => 'Training',
                'nav_description' => 'Experiential learning programs',
                'hero_eyebrow' => 'Wellness training',
                'hero_title' => 'Learn, practise, and share with confidence.',
                'hero_description' => 'Experiential training that brings traditional wellbeing principles into a clear, practical learning environment.',
                'hero_image' => 'assets/images/founders.jpg',
                'hero_image_alt' => 'Experienced teachers guiding a learning program',
                'section_eyebrow' => 'Our training',
                'section_title' => 'Grounded learning for everyday life and practice.',
                'information_eyebrow' => 'Learning approach',
                'information_title' => 'Understanding grows through practice.',
                'information_body' => "Each course combines clear teaching with observation, reflection, discussion, and guided experience.\n\nTraining is educational and does not qualify participants to diagnose medical conditions or replace regulated professional credentials.",
                'cta_eyebrow' => 'Find your course',
                'cta_title' => 'Tell us about your experience and learning goals, and we will help you choose.',
                'cta_body' => 'Share your background, preferred dates, and learning goals.',
                'notice' => 'Course schedules and content may be adapted to the group. Please ask us about dates, prerequisites, and availability.',
                'secondary_title' => 'Learning format',
                'secondary_key' => 'format',
                'source' => 'trainings',
                'sort_order' => 30,
            ],
        ];

        $subcategories = [
            'programs' => [
                'cleansing-rejuvenation' => ['name' => 'Cleansing & Rejuvenation', 'description' => 'Immersive Ayurvedic cleansing and renewal programs', 'offerings' => ['panchakarma-rejuvenation', 'ayurvedic-wellness-immersion']],
                'mind-body-retreats' => ['name' => 'Mind & Body Retreats', 'description' => 'Restorative retreats for balance and mindful living', 'offerings' => ['mind-body-balance-retreat']],
            ],
            'therapies' => [
                'cleansing-therapies' => ['name' => 'Cleansing Therapies', 'description' => 'Practitioner-guided Ayurvedic cleansing care', 'offerings' => ['panchakarma-detox-therapy']],
                'personalised-wellness-care' => ['name' => 'Personalised Wellness Care', 'description' => 'Retreat and healing services shaped around individual needs', 'offerings' => ['ayurvedic-wellness-retreats', 'personalized-healing-programs']],
            ],
            'trainings' => [
                'ayurveda-education' => ['name' => 'Ayurveda Education', 'description' => 'Foundational and practical Ayurveda learning', 'offerings' => ['ayurveda-foundations', 'ayurvedic-lifestyle-nutrition']],
                'mindfulness-yoga-education' => ['name' => 'Mindfulness & Yoga Education', 'description' => 'Training in mindful practice, yoga, and facilitation', 'offerings' => ['mindfulness-yoga-facilitation']],
            ],
        ];

        foreach ($categories as $slug => $metadata) {
            $source = $metadata['source'];
            $secondaryKey = $metadata['secondary_key'];
            $secondaryTitle = $metadata['secondary_title'];
            $notice = $metadata['notice'];
            $heroSource = $metadata['hero_image'];
            unset($metadata['source'], $metadata['secondary_key'], $metadata['secondary_title'], $metadata['notice'], $metadata['hero_image']);

            $category = WellnessCategory::where('slug', $slug)->first();
            if (! $category) {
                $category = WellnessCategory::create($metadata + [
                    'slug' => $slug,
                    'hero_image_path' => $this->importImage('wellness/categories/'.$slug, $heroSource),
                    'is_published' => true,
                ]);
            }

            $offeringSubcategories = [];
            $position = 0;
            foreach ($subcategories[$slug] as $subcategorySlug => $subcategoryValues) {
                $position++;
                $subcategory = WellnessSubcategory::firstOrCreate(
                    ['wellness_category_id' => $category->id, 'slug' => $subcategorySlug],
                    [
                        'name' => $subcategoryValues['name'],
                        'nav_description' => $subcategoryValues['description'],
                        'description' => $subcategoryValues['description'],
                        'is_published' => true,
                        'sort_order' => $position * 10,
                    ]
                );
                foreach ($subcategoryValues['offerings'] as $offeringSlug) {
                    $offeringSubcategories[$offeringSlug] = $subcategory->id;
                }
            }

            foreach (config('wellness.'.$source, []) as $offeringSlug => $values) {
                $existing = WellnessOffering::where('wellness_category_id', $category->id)->where('slug', $offeringSlug)->first();
                if ($existing) {
                    if ($existing->wellness_subcategory_id !== $offeringSubcategories[$offeringSlug]) {
                        $existing->update(['wellness_subcategory_id' => $offeringSubcategories[$offeringSlug]]);
                    }

                    continue;
                }

                WellnessOffering::create([
                    'wellness_category_id' => $category->id,
                    'wellness_subcategory_id' => $offeringSubcategories[$offeringSlug],
                    'name' => $values['name'],
                    'slug' => $offeringSlug,
                    'eyebrow' => $values['category'],
                    'duration' => $values['duration'] ?? null,
                    'image_path' => $this->importImage('wellness/offerings/'.$offeringSlug, $values['image']),
                    'image_alt' => $values['alt'],
                    'short_description' => $values['short'],
                    'description' => $values['description'],
                    'highlights' => $values['highlights'],
                    'includes' => $values['includes'],
                    'itinerary' => $values['itinerary'] ?? [],
                    'ideal_for' => $values['ideal_for'],
                    'secondary_title' => $secondaryTitle,
                    'secondary_content' => $values[$secondaryKey],
                    'notice' => $notice,
                    'is_published' => true,
                    'sort_order' => (array_search($offeringSlug, array_keys(config('wellness.'.$source)), true) + 1) * 10,
                ]);
            }
        }
    }

    private function importImage(string $directory, string $source): string
    {
        $path = $directory.'/defaults/'.basename($source);
        $sourcePath = dirname(base_path()).'/'.$source;

        if (! is_file($sourcePath) || ! Storage::disk('public')->put($path, file_get_contents($sourcePath))) {
            throw new RuntimeException('Could not import wellness image: '.$source);
        }

        return $path;
    }
}
