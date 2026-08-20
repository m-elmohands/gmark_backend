<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()->restrictOnDelete();
            $table->string('title', 180);
            $table->string('slug', 200)->unique();
            $table->string('reference_code', 80)->nullable()->unique();
            $table->string('short_description', 500);
            $table->text('description');
            $table->string('unit_type', 100)->nullable()->index();
            $table->decimal('bedrooms', 4, 1)->nullable();
            $table->decimal('bathrooms', 4, 1)->nullable();
            $table->decimal('indoor_area', 10, 2)->nullable();
            $table->decimal('outdoor_area', 10, 2)->nullable();
            $table->string('area_unit', 10)->default('sqm');
            $table->string('floor', 60)->nullable();
            $table->string('availability', 30)->nullable()->index();
            $table->string('price_display', 120)->nullable();
            $table->json('features')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->string('seo_title', 70)->nullable();
            $table->string('seo_description', 170)->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['area_id', 'status', 'published_at']);
            $table->index(['status', 'availability']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
