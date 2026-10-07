<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email');
            $table->string('phone', 50)->nullable();
            $table->string('enquiry_type', 100)->index();
            $table->string('enquiry_type_label', 150);
            $table->string('interest')->nullable();
            $table->text('message');
            $table->boolean('consent')->default(true);
            $table->string('status', 20)->default('new')->index();
            $table->timestamp('emailed_at')->nullable();
            $table->text('mail_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
