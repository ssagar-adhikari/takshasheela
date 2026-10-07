<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wellness_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wellness_category_id')->constrained()->restrictOnDelete();
            $table->string('name', 140);
            $table->string('slug', 160);
            $table->string('nav_description')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['wellness_category_id', 'slug']);
            $table->index(['wellness_category_id', 'is_published', 'sort_order'], 'wellness_subcategories_listing_index');
        });

        Schema::table('wellness_offerings', function (Blueprint $table) {
            $table->foreignId('wellness_subcategory_id')->nullable()->after('wellness_category_id')->constrained()->restrictOnDelete();
            $table->index(['wellness_subcategory_id', 'is_published', 'sort_order'], 'wellness_offerings_subcategory_index');
        });

        $definitions = [
            'programs' => [
                ['name' => 'Cleansing & Rejuvenation', 'slug' => 'cleansing-rejuvenation', 'description' => 'Immersive Ayurvedic cleansing and renewal programs', 'offerings' => ['panchakarma-rejuvenation', 'ayurvedic-wellness-immersion']],
                ['name' => 'Mind & Body Retreats', 'slug' => 'mind-body-retreats', 'description' => 'Restorative retreats for balance and mindful living', 'offerings' => ['mind-body-balance-retreat']],
            ],
            'therapies' => [
                ['name' => 'Cleansing Therapies', 'slug' => 'cleansing-therapies', 'description' => 'Practitioner-guided Ayurvedic cleansing care', 'offerings' => ['panchakarma-detox-therapy']],
                ['name' => 'Personalised Wellness Care', 'slug' => 'personalised-wellness-care', 'description' => 'Retreat and healing services shaped around individual needs', 'offerings' => ['ayurvedic-wellness-retreats', 'personalized-healing-programs']],
            ],
            'trainings' => [
                ['name' => 'Ayurveda Education', 'slug' => 'ayurveda-education', 'description' => 'Foundational and practical Ayurveda learning', 'offerings' => ['ayurveda-foundations', 'ayurvedic-lifestyle-nutrition']],
                ['name' => 'Mindfulness & Yoga Education', 'slug' => 'mindfulness-yoga-education', 'description' => 'Training in mindful practice, yoga, and facilitation', 'offerings' => ['mindfulness-yoga-facilitation']],
            ],
        ];

        foreach (DB::table('wellness_categories')->get(['id', 'slug', 'name']) as $category) {
            $groups = $definitions[$category->slug] ?? [[
                'name' => 'General',
                'slug' => 'general',
                'description' => 'Programs and services in '.$category->name,
                'offerings' => [],
            ]];
            $firstSubcategoryId = null;

            foreach ($groups as $position => $group) {
                $subcategoryId = DB::table('wellness_subcategories')->insertGetId([
                    'wellness_category_id' => $category->id,
                    'name' => $group['name'],
                    'slug' => $group['slug'],
                    'nav_description' => $group['description'],
                    'is_published' => true,
                    'sort_order' => ($position + 1) * 10,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $firstSubcategoryId ??= $subcategoryId;

                if ($group['offerings'] !== []) {
                    DB::table('wellness_offerings')
                        ->where('wellness_category_id', $category->id)
                        ->whereIn('slug', $group['offerings'])
                        ->update(['wellness_subcategory_id' => $subcategoryId]);
                }
            }

            DB::table('wellness_offerings')
                ->where('wellness_category_id', $category->id)
                ->whereNull('wellness_subcategory_id')
                ->update(['wellness_subcategory_id' => $firstSubcategoryId]);
        }
    }

    public function down(): void
    {
        Schema::table('wellness_offerings', function (Blueprint $table) {
            $table->dropIndex('wellness_offerings_subcategory_index');
            $table->dropConstrainedForeignId('wellness_subcategory_id');
        });

        Schema::dropIfExists('wellness_subcategories');
    }
};
