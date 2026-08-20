<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::query()->updateOrCreate(['id' => 1], [
            'email' => 'hello@gmark.com', 'phone' => '+20 2 0000 0000', 'whatsapp' => '+20 100 000 0000',
            'social_links' => ['instagram' => 'https://instagram.com/gmark', 'linkedin' => 'https://linkedin.com/company/gmark'],
            'en' => ['site_name' => 'GMark', 'tagline' => 'Places to live. Space to become.', 'site_description' => 'A curated portfolio of distinctive residential destinations.', 'address' => 'New Cairo, Cairo, Egypt', 'meta_title' => 'GMark — Residential Destinations', 'meta_description' => 'Explore distinctive residential areas and thoughtfully designed homes by GMark.'],
            'ar' => ['site_name' => 'جي مارك', 'tagline' => 'أماكن للحياة. ومساحة للتطور.', 'site_description' => 'مجموعة مختارة من الوجهات السكنية المميزة.', 'address' => 'القاهرة الجديدة، القاهرة، مصر', 'meta_title' => 'جي مارك — وجهات سكنية', 'meta_description' => 'استكشف المناطق السكنية المميزة والمنازل المصممة بعناية من جي مارك.'],
        ]);
    }
}
