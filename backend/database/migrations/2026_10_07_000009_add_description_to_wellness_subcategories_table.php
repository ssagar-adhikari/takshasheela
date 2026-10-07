<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wellness_subcategories', function (Blueprint $table) {
            $table->text('description')->nullable()->after('nav_description');
        });

        DB::table('wellness_subcategories')->whereNull('description')->update([
            'description' => DB::raw('nav_description'),
        ]);
    }

    public function down(): void
    {
        Schema::table('wellness_subcategories', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
