<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chronicle_articles', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->index();
            $table->string('title');
            $table->string('slug', 180)->unique();
            $table->string('category', 150);
            $table->text('excerpt');
            $table->longText('body');
            $table->string('image_path')->nullable();
            $table->string('image_alt');
            $table->timestamp('published_at')->nullable()->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('quote');
            $table->string('guest_name', 150);
            $table->string('guest_location', 150)->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->string('image_alt');
            $table->string('caption')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('chronicle_articles');
    }
};
