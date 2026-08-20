<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5)->index();
            $table->string('name', 160);
            $table->string('short_description', 500);
            $table->text('description');
            $table->string('location')->nullable();
            $table->json('highlights')->nullable();
            $table->json('amenities')->nullable();
            $table->string('seo_title', 70)->nullable();
            $table->string('seo_description', 170)->nullable();
            $table->unique(['area_id', 'locale']);
        });

        Schema::create('unit_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5)->index();
            $table->string('title', 180);
            $table->string('short_description', 500);
            $table->text('description');
            $table->string('unit_type', 100)->nullable()->index();
            $table->string('floor', 60)->nullable();
            $table->string('price_display', 120)->nullable();
            $table->json('features')->nullable();
            $table->string('seo_title', 70)->nullable();
            $table->string('seo_description', 170)->nullable();
            $table->unique(['unit_id', 'locale']);
        });

        DB::table('areas')->orderBy('id')->each(function (object $area): void {
            DB::table('area_translations')->insert([
                'area_id' => $area->id, 'locale' => 'en', 'name' => $area->name,
                'short_description' => $area->short_description, 'description' => $area->description,
                'location' => $area->location, 'highlights' => $area->highlights, 'amenities' => $area->amenities,
                'seo_title' => $area->seo_title, 'seo_description' => $area->seo_description,
            ]);
        });

        DB::table('units')->orderBy('id')->each(function (object $unit): void {
            DB::table('unit_translations')->insert([
                'unit_id' => $unit->id, 'locale' => 'en', 'title' => $unit->title,
                'short_description' => $unit->short_description, 'description' => $unit->description,
                'unit_type' => $unit->unit_type, 'floor' => $unit->floor, 'price_display' => $unit->price_display,
                'features' => $unit->features, 'seo_title' => $unit->seo_title, 'seo_description' => $unit->seo_description,
            ]);
        });

        Schema::table('areas', fn (Blueprint $table) => $table->dropColumn([
            'name', 'short_description', 'description', 'location', 'highlights', 'amenities', 'seo_title', 'seo_description',
        ]));
        Schema::table('units', function (Blueprint $table) {
            $table->dropIndex(['unit_type']);
            $table->dropColumn(['title', 'short_description', 'description', 'unit_type', 'floor', 'price_display', 'features', 'seo_title', 'seo_description']);
        });
    }

    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->string('name', 160)->nullable();
            $table->string('short_description', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->json('highlights')->nullable();
            $table->json('amenities')->nullable();
            $table->string('seo_title', 70)->nullable();
            $table->string('seo_description', 170)->nullable();
        });
        Schema::table('units', function (Blueprint $table) {
            $table->string('title', 180)->nullable();
            $table->string('short_description', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('unit_type', 100)->nullable();
            $table->string('floor', 60)->nullable();
            $table->string('price_display', 120)->nullable();
            $table->json('features')->nullable();
            $table->string('seo_title', 70)->nullable();
            $table->string('seo_description', 170)->nullable();
        });

        Schema::dropIfExists('unit_translations');
        Schema::dropIfExists('area_translations');
    }
};
