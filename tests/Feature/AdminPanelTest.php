<?php

namespace Tests\Feature;

use App\Filament\Resources\Areas\Pages\EditArea;
use App\Filament\Resources\Locations\Pages\EditLocation;
use App\Filament\Resources\SiteSettings\Pages\ManageSiteSetting;
use App\Filament\Resources\Units\Pages\EditUnit;
use App\Models\Area;
use App\Models\Location;
use App\Models\SiteSetting;
use App\Models\Unit;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_the_admin_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_public_web_session_does_not_cause_an_admin_forbidden_response(): void
    {
        $publicUser = User::factory()->create(['is_admin' => false]);

        $this->actingAs($publicUser, 'web')
            ->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_only_active_users_can_access_the_admin_panel(): void
    {
        $panel = Filament::getPanel('admin');
        $active = User::factory()->create(['is_active' => true]);
        $inactive = User::factory()->create(['is_active' => false]);

        $this->assertTrue($active->canAccessPanel($panel));
        $this->assertFalse($inactive->canAccessPanel($panel));
    }

    public function test_active_admin_can_render_area_management_screens(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $area = Area::factory()->create();

        $this->actingAs($admin, 'admin')->get('/admin/areas')->assertOk();
        $this->actingAs($admin, 'admin')->get("/admin/areas/{$area->id}/edit")->assertOk();
    }

    public function test_admin_can_render_translated_location_management_screens(): void
    {
        $admin = User::factory()->create(['is_active' => true, 'is_admin' => true]);
        $location = Location::factory()->create();

        $this->actingAs($admin, 'admin')->get('/admin/locations')->assertOk();
        $this->actingAs($admin, 'admin')->get("/admin/locations/{$location->id}/edit")->assertOk();

        Livewire::test(EditLocation::class, ['record' => $location->getRouteKey()])
            ->assertSchemaStateSet([
                'name:en' => $location->translate('en')->name,
                'name:ar' => $location->translate('ar')->name,
            ]);
    }

    public function test_site_settings_navigation_opens_the_single_settings_form(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true]);
        $settings = SiteSetting::query()->create([
            'email' => 'hello@gmark.com',
            'en' => ['site_name' => 'GMark'],
            'ar' => ['site_name' => 'جي مارك'],
        ]);

        $this->actingAs($admin, 'admin')->get('/admin/site-settings')
            ->assertOk()
            ->assertDontSee('Create Site Setting');

        Livewire::test(ManageSiteSetting::class)->assertSchemaStateSet([
            'site_name:en' => 'GMark',
            'site_name:ar' => 'جي مارك',
            'email' => 'hello@gmark.com',
        ]);
    }

    public function test_admin_can_switch_interface_locale(): void
    {
        $admin = User::factory()->create(['is_active' => true]);

        $this->actingAs($admin, 'admin')
            ->from('/admin')
            ->get('/admin/locale/ar')
            ->assertRedirect('/admin')
            ->assertSessionHas('admin_locale', 'ar');

        $this->actingAs($admin, 'admin')
            ->withSession(['admin_locale' => 'ar'])
            ->get('/admin/areas')
            ->assertOk()
            ->assertSee('المناطق السكنية');
    }

    public function test_edit_forms_load_both_translation_values(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $area = Area::factory()->create([
            'en' => ['name' => 'Lake Quarter', 'short_description' => 'English area summary', 'description' => 'English area description'],
            'ar' => ['name' => 'حي البحيرة', 'short_description' => 'ملخص المنطقة بالعربية', 'description' => 'وصف المنطقة بالعربية'],
        ]);
        $unit = Unit::factory()->create([
            'area_id' => $area->id,
            'en' => ['title' => 'Waterside Residence', 'short_description' => 'English unit summary', 'description' => 'English unit description'],
            'ar' => ['title' => 'وحدة الواجهة المائية', 'short_description' => 'ملخص الوحدة بالعربية', 'description' => 'وصف الوحدة بالعربية'],
        ]);

        $this->actingAs($admin, 'admin');

        Livewire::test(EditArea::class, ['record' => $area->getRouteKey()])
            ->assertSchemaStateSet([
                'name:en' => 'Lake Quarter',
                'name:ar' => 'حي البحيرة',
            ]);

        Livewire::test(EditUnit::class, ['record' => $unit->getRouteKey()])
            ->assertSchemaStateSet([
                'title:en' => 'Waterside Residence',
                'title:ar' => 'وحدة الواجهة المائية',
            ]);
    }
}
