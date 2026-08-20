<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['slug' => 'new-cairo', 'latitude' => 30.0074, 'longitude' => 31.4913, 'en' => ['name' => 'New Cairo', 'description' => 'A refined eastern Cairo destination known for planned communities, education, and generous green space.'], 'ar' => ['name' => 'القاهرة الجديدة', 'description' => 'وجهة راقية شرق القاهرة تتميز بالمجتمعات المخططة والمؤسسات التعليمية والمساحات الخضراء الرحبة.']],
            ['slug' => 'west-cairo', 'latitude' => 30.0131, 'longitude' => 30.9764, 'en' => ['name' => 'West Cairo', 'description' => 'Contemporary neighborhoods with direct access to Sheikh Zayed, October City, and major cultural destinations.'], 'ar' => ['name' => 'غرب القاهرة', 'description' => 'أحياء عصرية ترتبط مباشرة بالشيخ زايد ومدينة أكتوبر وأبرز الوجهات الثقافية.']],
            ['slug' => 'north-coast', 'latitude' => 30.9500, 'longitude' => 28.8500, 'en' => ['name' => 'North Coast', 'description' => 'Egypt’s Mediterranean retreat, shaped by clear water, long beaches, and seasonal coastal living.'], 'ar' => ['name' => 'الساحل الشمالي', 'description' => 'وجهة مصر المتوسطية التي تجمع بين المياه الصافية والشواطئ الممتدة وأسلوب الحياة الساحلي.']],
            ['slug' => 'new-administrative-capital', 'latitude' => 30.0132, 'longitude' => 31.7656, 'en' => ['name' => 'New Administrative Capital', 'description' => 'A future-focused capital with connected districts, landmark architecture, and expansive public landscapes.'], 'ar' => ['name' => 'العاصمة الإدارية الجديدة', 'description' => 'عاصمة مستقبلية تضم أحياء مترابطة وعمارة بارزة ومساحات عامة واسعة.']],
            ['slug' => 'sheikh-zayed', 'latitude' => 30.0497, 'longitude' => 30.9769, 'en' => ['name' => 'Sheikh Zayed', 'description' => 'A calm, low-density city offering established services, landscaped communities, and easy western access.'], 'ar' => ['name' => 'الشيخ زايد', 'description' => 'مدينة هادئة منخفضة الكثافة تجمع بين الخدمات المتكاملة والمجتمعات الخضراء وسهولة الوصول.']],
            ['slug' => 'east-cairo', 'latitude' => 30.0850, 'longitude' => 31.6000, 'en' => ['name' => 'East Cairo', 'description' => 'A growing residential corridor connecting established neighborhoods with Cairo’s newest destinations.'], 'ar' => ['name' => 'شرق القاهرة', 'description' => 'محور سكني متنامٍ يربط الأحياء الراسخة بأحدث وجهات القاهرة.']],
        ];

        foreach ($locations as $order => $data) {
            $location = Location::query()->updateOrCreate(['slug' => $data['slug']], [
                'latitude' => $data['latitude'], 'longitude' => $data['longitude'],
                'display_order' => $order, 'is_active' => true,
                'en' => $data['en'], 'ar' => $data['ar'],
            ]);
            Area::query()->where('slug', match ($data['slug']) {
                'new-cairo' => 'the-grove', 'west-cairo' => 'park-residences', 'north-coast' => 'coast-house',
                'new-administrative-capital' => 'lake-quarter', 'sheikh-zayed' => 'desert-house', default => 'palm-court',
            })->update(['location_id' => $location->id]);
        }
    }
}
