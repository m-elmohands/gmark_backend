<?php

namespace Tests\Unit;

use App\Enums\ContentStatus;
use App\Models\Area;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ModelArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function test_area_owns_many_units(): void
    {
        $area = Area::factory()->create();
        Unit::factory()->count(2)->for($area)->create();

        $this->assertCount(2, $area->units);
        $this->assertTrue($area->units->every(fn (Unit $unit) => $unit->area->is($area)));
    }

    public function test_publishing_sets_a_publication_timestamp(): void
    {
        $area = Area::factory()->create(['status' => ContentStatus::Published, 'published_at' => null]);

        $this->assertNotNull($area->published_at);
    }

    public function test_only_one_media_item_can_be_the_cover(): void
    {
        Storage::fake('public');

        $area = Area::factory()->create();
        $area->addMediaFromBase64($this->png(), 'image/png')->usingFileName('one.png')->toMediaCollection('cover');
        $area->addMediaFromBase64($this->png(), 'image/png')->usingFileName('two.png')->toMediaCollection('cover');

        $this->assertCount(1, $area->fresh()->getMedia('cover'));
        $this->assertSame('two.png', $area->getFirstMedia('cover')->file_name);
    }

    public function test_area_and_unit_attributes_follow_the_application_locale(): void
    {
        $area = Area::factory()->create([
            'en' => ['name' => 'The Grove', 'short_description' => 'English summary', 'description' => 'English description'],
            'ar' => ['name' => 'ذا جروف', 'short_description' => 'ملخص عربي', 'description' => 'وصف عربي'],
        ]);
        $unit = Unit::factory()->for($area)->create([
            'en' => ['title' => 'Garden Home', 'short_description' => 'English summary', 'description' => 'English description'],
            'ar' => ['title' => 'منزل الحديقة', 'short_description' => 'ملخص عربي', 'description' => 'وصف عربي'],
        ]);

        app()->setLocale('ar');
        $this->assertSame('ذا جروف', $area->fresh()->name);
        $this->assertSame('منزل الحديقة', $unit->fresh()->title);

        app()->setLocale('en');
        $this->assertSame('The Grove', $area->fresh()->name);
        $this->assertSame('Garden Home', $unit->fresh()->title);
    }

    private function png(): string
    {
        return 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';
    }
}
