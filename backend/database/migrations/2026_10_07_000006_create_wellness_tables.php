<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wellness_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 140)->unique();
            $table->string('nav_description');
            $table->string('hero_eyebrow', 150);
            $table->string('hero_title');
            $table->text('hero_description');
            $table->string('hero_image_path')->nullable();
            $table->string('hero_image_alt');
            $table->string('section_eyebrow', 150);
            $table->string('section_title');
            $table->string('information_eyebrow', 150)->nullable();
            $table->string('information_title')->nullable();
            $table->text('information_body')->nullable();
            $table->string('cta_eyebrow', 150);
            $table->string('cta_title');
            $table->text('cta_body')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('wellness_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wellness_category_id')->constrained()->restrictOnDelete();
            $table->string('name', 180);
            $table->string('slug', 180);
            $table->string('eyebrow', 150);
            $table->string('duration', 100)->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_alt');
            $table->text('short_description');
            $table->longText('description');
            $table->json('highlights');
            $table->json('includes');
            $table->json('itinerary')->nullable();
            $table->text('ideal_for');
            $table->string('secondary_title', 150);
            $table->text('secondary_content');
            $table->text('notice')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['wellness_category_id', 'slug']);
            $table->index(['wellness_category_id', 'is_published', 'sort_order'], 'wellness_offerings_listing_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wellness_offerings');
        Schema::dropIfExists('wellness_categories');
    }
};
