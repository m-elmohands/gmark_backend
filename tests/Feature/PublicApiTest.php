<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Models\Area;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_area_index_returns_only_published_areas(): void
    {
        $published = Area::factory()->published()->create(['name' => 'The Grove', 'slug' => 'the-grove']);
        Area::factory()->create(['name' => 'Hidden Area', 'slug' => 'hidden-area']);

        $response = $this->getJson('/api/v1/areas', ['Accept-Language' => 'en']);

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', (string) $published->id)
            ->assertJsonPath('data.0.slug', 'the-grove');
    }

    public function test_unit_is_private_when_its_parent_area_is_not_published(): void
    {
        $area = Area::factory()->create(['status' => ContentStatus::Draft]);
        $unit = Unit::factory()->published()->for($area)->create();

        $this->getJson("/api/v1/units/{$unit->slug}", ['Accept-Language' => 'en'])->assertNotFound();
    }

    public function test_unit_detail_serializes_only_public_media_collections(): void
    {
        Storage::fake('public');

        $area = Area::factory()->published()->create();
        $unit = Unit::factory()->published()->for($area)->create(['slug' => 'garden-a1']);

        $unit->addMediaFromBase64($this->png(), 'image/png')
            ->usingFileName('gallery.png')
            ->withCustomProperties(['alt_text' => 'Gallery image'])
            ->toMediaCollection('gallery');
        $unit->addMediaFromBase64($this->png(), 'image/png')
            ->usingFileName('internal.png')
            ->toMediaCollection('internal');

        $this->getJson('/api/v1/units/garden-a1', ['Accept-Language' => 'en'])
            ->assertOk()
            ->assertJsonCount(1, 'data.media')
            ->assertJsonPath('data.media.0.alt', 'Gallery image');
    }

    public function test_collection_rejects_out_of_range_page_size(): void
    {
        $this->getJson('/api/v1/areas?per_page=25', ['Accept-Language' => 'en'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('per_page');
    }

    public function test_published_scope_excludes_future_publication(): void
    {
        Area::factory()->create([
            'status' => ContentStatus::Published,
            'published_at' => now()->addDay(),
        ]);

        $this->getJson('/api/v1/areas', ['Accept-Language' => 'en'])->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_locale_middleware_rejects_an_unsupported_language(): void
    {
        $this->getJson('/api/v1/areas', ['Accept-Language' => 'fr'])
            ->assertStatus(406)
            ->assertHeader('Content-Type', 'application/json');
    }

    private function png(): string
    {
        return 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';
    }
}
