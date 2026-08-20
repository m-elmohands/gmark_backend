<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('location_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unique(['location_id', 'locale']);
        });

        Schema::table('areas', function (Blueprint $table): void {
            $table->foreignId('location_id')->nullable()->after('slug')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('areas', fn (Blueprint $table) => $table->dropConstrainedForeignId('location_id'));
        Schema::dropIfExists('location_translations');
        Schema::dropIfExists('locations');
    }
};
