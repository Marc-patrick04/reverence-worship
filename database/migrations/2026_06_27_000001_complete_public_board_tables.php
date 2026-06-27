<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('public_board')) {
            Schema::create('public_board', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('content');
                $table->string('type')->default('update');
                $table->dateTime('event_date')->nullable();
                $table->boolean('is_published')->default(false);
                $table->boolean('is_pinned')->default(false);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        } else {
            $hadPublishingColumn = Schema::hasColumn('public_board', 'is_published');
            Schema::table('public_board', function (Blueprint $table) {
                if (!Schema::hasColumn('public_board', 'type')) {
                    $table->string('type')->default('update');
                }
                if (!Schema::hasColumn('public_board', 'event_date')) {
                    $table->dateTime('event_date')->nullable();
                }
                if (!Schema::hasColumn('public_board', 'is_published')) {
                    $table->boolean('is_published')->default(false);
                }
            });
            if (!$hadPublishingColumn) {
                DB::table('public_board')->update(['is_published' => true]);
            }
        }

        if (!Schema::hasTable('landing_youtube_videos')) {
            Schema::create('landing_youtube_videos', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('youtube_id', 100);
                $table->boolean('is_published')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('landing_featured_images')) {
            Schema::create('landing_featured_images', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('image_path');
                $table->text('description')->nullable();
                $table->boolean('is_published')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // These tables may predate this migration, so preserve their content.
    }
};
