<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->json('social_links')->nullable();
            $table->timestamps();
        });
        Schema::create('site_setting_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_setting_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('site_name');
            $table->string('tagline')->nullable();
            $table->text('site_description')->nullable();
            $table->string('address')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->unique(['site_setting_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_setting_translations');
        Schema::dropIfExists('site_settings');
    }
};
