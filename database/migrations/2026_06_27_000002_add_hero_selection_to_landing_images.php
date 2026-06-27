<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('landing_featured_images') && !Schema::hasColumn('landing_featured_images', 'is_hero')) {
            Schema::table('landing_featured_images', function (Blueprint $table) {
                $table->boolean('is_hero')->default(false);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('landing_featured_images') && Schema::hasColumn('landing_featured_images', 'is_hero')) {
            Schema::table('landing_featured_images', function (Blueprint $table) {
                $table->dropColumn('is_hero');
            });
        }
    }
};
