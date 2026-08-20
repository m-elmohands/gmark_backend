<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Location;
use App\Models\SiteSetting;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeders_create_a_realistic_and_publicly_browsable_portfolio(): void
    {
        Storage::fake('public');

        $this->seed();

        $admin = User::query()->where('is_active', true)->firstOrFail();
        $this->assertSame(6, Area::query()->count());
        $this->assertSame(13, Unit::query()->count());
        $this->assertDatabaseCount('media', 38);
        $this->assertTrue(Area::query()->where('slug', 'the-grove')->firstOrFail()->hasMedia('cover'));
        $this->assertTrue(Unit::query()->where('slug', 'horizon-villa-v1')->firstOrFail()->hasMedia('gallery'));
        $this->assertNotNull($admin->email_verified_at);
        $this->assertSame(6, Location::query()->count());
        $this->assertSame('القاهرة الجديدة', Location::query()->where('slug', 'new-cairo')->firstOrFail()->translate('ar')->name);
        $this->assertNotNull(Area::query()->where('slug', 'the-grove')->firstOrFail()->location_id);
        $this->assertSame('جي مارك', SiteSetting::query()->firstOrFail()->translate('ar')->site_name);

        $this->getJson('/api/v1/settings', ['Accept-Language' => 'ar'])
            ->assertOk()
            ->assertJsonPath('data.site_name', 'جي مارك')
            ->assertJsonPath('data.address', 'القاهرة الجديدة، القاهرة، مصر');

        $this->getJson('/api/v1/areas', ['Accept-Language' => 'en'])
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonMissing(['slug' => 'palm-court']);

        $this->getJson('/api/v1/areas/the-grove', ['Accept-Language' => 'ar'])
            ->assertOk()
            ->assertJsonPath('data.name', 'ذا جروف')
            ->assertJsonPath('data.locale', 'ar')
            ->assertJsonPath('data.url', '/ar/areas/the-grove');
    }
}
