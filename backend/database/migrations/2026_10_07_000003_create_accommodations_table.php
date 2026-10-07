<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 160)->unique();
            $table->string('category', 150);
            $table->string('heading');
            $table->string('image_path')->nullable();
            $table->string('image_alt');
            $table->text('short_description');
            $table->longText('description');
            $table->json('highlights');
            $table->json('features');
            $table->text('ideal_for');
            $table->text('stay_note');
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodations');
    }
};
