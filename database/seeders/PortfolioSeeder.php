<?php

namespace Database\Seeders;

use App\Enums\Availability;
use App\Enums\ContentStatus;
use App\Models\Area;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Throwable;

class PortfolioSeeder extends Seeder
{
    private const FALLBACK_PNG = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

    private array $images = [
        'exterior' => ['local' => 'the-grove.webp', 'remote' => 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1800&q=85'],
        'living' => ['local' => 'residence-interior.webp', 'remote' => 'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=1800&q=85'],
        'bedroom' => ['local' => 'residence-interior.webp', 'remote' => 'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1800&q=85'],
        'minimal' => ['local' => 'residence-interior.webp', 'remote' => 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1800&q=85'],
        'coast' => ['local' => 'coast-house.webp', 'remote' => 'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?auto=format&fit=crop&w=1800&q=85'],
        'bright' => ['local' => 'residence-interior.webp', 'remote' => 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1800&q=85'],
        'kitchen' => ['local' => 'residence-interior.webp', 'remote' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1800&q=85'],
        'terrace' => ['local' => 'coast-house.webp', 'remote' => 'https://images.unsplash.com/photo-1600607688969-a5bfcd646154?auto=format&fit=crop&w=1800&q=85'],
        'lake' => ['local' => 'lake-quarter.webp', 'remote' => null],
        'desert' => ['local' => 'desert-house.webp', 'remote' => null],
    ];

    public function run(): void
    {
        foreach ($this->portfolio() as $areaData) {
            $units = $areaData['units'];
            $media = $areaData['media'];
            unset($areaData['units'], $areaData['media']);

            $areaData = $this->withTranslations($areaData, $this->arabicAreas()[$areaData['slug']]);

            $area = Area::query()->updateOrCreate(['slug' => $areaData['slug']], $areaData);
            $this->syncImages($area, $media, "area-{$area->slug}");

            foreach ($units as $unitData) {
                $unitMedia = $unitData['media'];
                unset($unitData['media']);
                $unitData = $this->withTranslations($unitData, $this->arabicUnits()[$unitData['slug']]);
                $unit = Unit::query()->updateOrCreate(['slug' => $unitData['slug']], ['area_id' => $area->id, ...$unitData]);
                $this->syncImages($unit, $unitMedia, "unit-{$unit->slug}");
            }
        }
    }

    private function withTranslations(array $data, array $arabic): array
    {
        $translated = array_flip([
            'name', 'title', 'short_description', 'description', 'location', 'highlights',
            'amenities', 'unit_type', 'floor', 'price_display', 'features', 'seo_title', 'seo_description',
        ]);
        $english = array_intersect_key($data, $translated);

        return [...array_diff_key($data, $translated), 'en' => $english, 'ar' => $arabic];
    }

    private function arabicAreas(): array
    {
        return [
            'the-grove' => ['name' => 'ذا جروف', 'location' => 'القاهرة الجديدة', 'short_description' => 'مجتمع سكني هادئ تصوغه الحدائق والضوء الطبيعي وإيقاع حياة أكثر راحة.', 'description' => 'يجمع ذا جروف بين الخصوصية والتواصل، حيث تنسجم العمارة مع المساحات الخضراء وتمنح مساراته تجربة يومية هادئة وسلسة.', 'highlights' => ['حدائق متدرجة', 'عمارة تحتفي بالضوء', 'مجتمع يسهل التنقل فيه'], 'amenities' => ['صالة للسكان', 'مسارات حدائق', 'استوديو عافية', 'أمن على مدار الساعة'], 'seo_title' => 'وحدات ذا جروف في القاهرة الجديدة', 'seo_description' => 'اكتشف الشقق والدوبلكس المطلة على الحدائق في ذا جروف بالقاهرة الجديدة.'],
            'park-residences' => ['name' => 'بارك ريزيدنسز', 'location' => 'غرب القاهرة', 'short_description' => 'منازل عصرية تنتظم حول مساحات خضراء رحبة وتفاصيل يومية مشتركة.', 'description' => 'تخلق بارك ريزيدنسز علاقة مباشرة بين المنزل والطبيعة عبر إطلالات مدروسة وتراسات مظللة ومساحات صديقة للمشاة.', 'highlights' => ['واجهة على الحديقة', 'تراسات مظللة', 'مساحات مجتمعية'], 'amenities' => ['صالة النادي', 'مساحات لعب', 'مسار للدراجات', 'خدمة كونسيرج'], 'seo_title' => 'بارك ريزيدنسز في غرب القاهرة', 'seo_description' => 'اكتشف المنازل العصرية المطلة على الحدائق في غرب القاهرة.'],
            'coast-house' => ['name' => 'كوست هاوس', 'location' => 'الساحل الشمالي', 'short_description' => 'منازل تواجه البحر وتجمع بين الأفق المفتوح ودفء الخامات والفخامة الهادئة.', 'description' => 'صُمم كوست هاوس حول ضوء البحر والنسيم وحركة سهلة بين المساحات الداخلية والخارجية.', 'highlights' => ['أفق مفتوح', 'تراسات بحرية', 'خامات طبيعية'], 'amenities' => ['شاطئ خاص', 'منصة للغروب', 'نادي شاطئي', 'خدمة تنظيف عند الطلب'], 'seo_title' => 'كوست هاوس في الساحل الشمالي', 'seo_description' => 'اكتشف الفيلات والشاليهات المطلة على البحر في كوست هاوس.'],
            'lake-quarter' => ['name' => 'ليك كوارتر', 'location' => 'العاصمة الإدارية الجديدة', 'short_description' => 'منازل هادئة تطل على الماء وتجمع بين المساحات الرحبة والتخطيط الحضري المتوازن.', 'description' => 'يحيط ليك كوارتر ببحيرة هادئة ومسارات خضراء، ليمنح السكان إطلالات مفتوحة وحياة يومية متصلة بالطبيعة.', 'highlights' => ['إطلالات على البحيرة', 'ممشى أخضر', 'عمارة معاصرة'], 'amenities' => ['نادي على البحيرة', 'مسار للجري', 'منطقة أطفال', 'خدمة كونسيرج'], 'seo_title' => 'ليك كوارتر في العاصمة الإدارية', 'seo_description' => 'استكشف الشقق والبنتهاوس المطلة على البحيرة في العاصمة الإدارية الجديدة.'],
            'desert-house' => ['name' => 'ديزرت هاوس', 'location' => 'الشيخ زايد', 'short_description' => 'مجموعة فلل منخفضة الارتفاع تستلهم هدوء الصحراء وحدائقها المتدرجة.', 'description' => 'يعيد ديزرت هاوس تفسير منزل الفناء بكتل حجرية هادئة وظلال عميقة وحدائق تتحمل المناخ المحلي.', 'highlights' => ['فلل منخفضة الكثافة', 'أفنية مظللة', 'مناظر طبيعية محلية'], 'amenities' => ['نادي خاص', 'حدائق صحراوية', 'مسارات للدراجات', 'أمن على مدار الساعة'], 'seo_title' => 'فلل ديزرت هاوس في الشيخ زايد', 'seo_description' => 'اكتشف فلل وتاون هاوس ديزرت هاوس في الشيخ زايد.'],
            'palm-court' => ['name' => 'بالم كورت', 'location' => 'شرق القاهرة', 'short_description' => 'مجموعة مستقبلية من منازل الأفنية لا تزال في مرحلة التطوير التصميمي.', 'description' => 'يستكشف بالم كورت الحدائق المظللة والممرات الحميمة كأساس لمجتمع سكني مترابط.', 'highlights' => ['حياة حول الفناء', 'ممرات مظللة'], 'amenities' => ['حديقة مجتمعية'], 'seo_title' => 'بالم كورت', 'seo_description' => 'مجموعة مستقبلية من المنازل في شرق القاهرة.'],
        ];
    }

    private function arabicUnits(): array
    {
        $common = fn (string $title, string $type, string $floor, array $features) => ['title' => $title, 'unit_type' => $type, 'floor' => $floor, 'short_description' => "منزل {$type} بتصميم داخلي مدروس واتصال واضح بالمساحات الخارجية.", 'description' => "صُمم {$title} كتتابع من المساحات الهادئة والمترابطة، تنفتح مناطق المعيشة فيه على الطبيعة والضوء بينما تحافظ الغرف الخاصة على الراحة والخصوصية.", 'features' => $features, 'seo_title' => "{$title} | جي مارك", 'seo_description' => "استكشف مواصفات وصور {$title}."];

        return [
            'garden-residence-a1' => $common('جاردن ريزيدنس A1', 'شقة', 'الدور الأرضي', ['حديقة خاصة', 'واجهات زجاجية ممتدة', 'تشطيبات من الحجر الطبيعي']),
            'sky-residence-b4' => $common('سكاي ريزيدنس B4', 'دوبلكس', 'الدوران الرابع والخامس', ['تراس بانورامي', 'معيشة بارتفاع مزدوج', 'دخول مباشر بالمصعد']),
            'courtyard-home-c2' => $common('كورت يارد هوم C2', 'تاون هاوس', 'أرضي وأول', ['فناء خاص', 'صالة عائلية', 'مكانان مغطّيان للسيارات']),
            'park-view-p3' => $common('بارك فيو P3', 'شقة', 'الدور الثالث', ['إطلالة مباشرة على الحديقة', 'شرفة جانبية', 'مساحات تخزين مدمجة']),
            'canopy-duplex-d7' => $common('كانوبي دوبلكس D7', 'دوبلكس', 'الدوران السادس والسابع', ['تراس على السطح', 'غرفة مكتب مرنة', 'جناح رئيسي يطل على الحديقة']),
            'horizon-villa-v1' => $common('هورايزون فيلا V1', 'فيلا', 'أرضي وأول', ['حمام سباحة خاص', 'صالة سطح تطل على البحر', 'جناح للضيوف']),
            'dune-chalet-c6' => $common('ديون شاليه C6', 'شاليه', 'الدور الأول', ['شرفة بحرية واسعة', 'تشطيبات جاهزة', 'مخزن لمستلزمات الشاطئ']),
            'waterside-residence-l2' => $common('ووتر سايد ريزيدنس L2', 'شقة', 'الدور الثاني', ['إطلالة مباشرة على البحيرة', 'تراس عميق', 'مطبخ مفتوح']),
            'garden-loft-l5' => $common('جاردن لوفت L5', 'لوفت', 'الدور الخامس', ['سقف مرتفع', 'شرفة مزروعة', 'مساحة عمل مرنة']),
            'pavilion-penthouse-l9' => $common('بافيليون بنتهاوس L9', 'بنتهاوس', 'الدور التاسع', ['تراس يلتف حول الوحدة', 'صالة خاصة', 'إطلالة بانورامية على المياه']),
            'wadi-villa-d1' => $common('وادي فيلا D1', 'فيلا', 'أرضي وأول', ['فناء مركزي', 'حمام سباحة خاص', 'مجلس للضيوف']),
            'courtyard-villa-d2' => $common('كورت يارد فيلا D2', 'فيلا', 'أرضي وأول', ['حديقة داخلية', 'جناح عائلي', 'ثلاثة أماكن للسيارات']),
            'sunset-townhouse-d4' => $common('صن ست تاون هاوس D4', 'تاون هاوس', 'أرضي وأول', ['تراس غربي', 'حديقة خاصة', 'غرفة متعددة الاستخدام']),
        ];
    }

    private function syncImages(Model $model, array $items, string $prefix): void
    {
        /** @var Area|Unit $model */
        $model->clearMediaCollection('cover');
        $model->clearMediaCollection('gallery');

        foreach ($items as $index => $item) {
            $collection = $index === 0 ? 'cover' : 'gallery';
            $source = $this->images[$item['image']];
            $useRemoteMedia = filter_var(env('GMARK_SEED_REMOTE_MEDIA', false), FILTER_VALIDATE_BOOL)
                && filled($source['remote']);
            $seedSource = $useRemoteMedia ? $source['remote'] : $source['local'];
            $properties = ['alt_text' => $item['alt'], 'seed_source' => $seedSource];

            try {
                if ($useRemoteMedia) {
                    $model->addMediaFromUrl($source['remote'], ['image/jpeg', 'image/png', 'image/webp'])
                        ->usingName($item['alt'])->usingFileName("{$prefix}-{$index}.jpg")
                        ->withCustomProperties($properties)->toMediaCollection($collection);

                    continue;
                }

                $model->addMedia(database_path("seeders/assets/portfolio/{$source['local']}"))
                    ->preservingOriginal()
                    ->usingName($item['alt'])->usingFileName("{$prefix}-{$index}.webp")
                    ->withCustomProperties($properties)->toMediaCollection($collection);
            } catch (Throwable $exception) {
                $model->addMediaFromBase64(self::FALLBACK_PNG, 'image/png')
                    ->usingName($item['alt'])->usingFileName("{$prefix}-{$index}.png")
                    ->withCustomProperties([...$properties, 'fallback' => true])->toMediaCollection($collection);
                $this->command?->warn("Used a local placeholder for {$prefix}-{$index}: {$exception->getMessage()}");
            }
        }
    }

    private function portfolio(): array
    {
        $published = now()->subDays(14);

        return [
            [
                'name' => 'The Grove', 'slug' => 'the-grove', 'location' => 'New Cairo',
                'short_description' => 'A composed residential setting shaped by gardens, natural light, and a slower rhythm.',
                'description' => 'The Grove balances privacy with connection. Architecture settles into layered planting, while every route through the neighbourhood is designed to feel calm and intuitive.',
                'latitude' => 30.013056, 'longitude' => 31.491389,
                'highlights' => ['Layered gardens', 'Light-led architecture', 'Walkable setting'],
                'amenities' => ['Residents lounge', 'Garden paths', 'Wellness studio', '24-hour security'],
                'status' => ContentStatus::Published, 'published_at' => $published, 'is_featured' => true, 'display_order' => 1,
                'seo_title' => 'The Grove Residences in New Cairo', 'seo_description' => 'Explore garden-led apartments and duplexes at The Grove, New Cairo.',
                'media' => [$this->photo('exterior', 'Modern residences opening onto a landscaped pool'), $this->photo('living', 'Warm contemporary living room at The Grove'), $this->photo('terrace', 'Shaded private terrace overlooking landscaped gardens')],
                'units' => [
                    $this->unit('Garden Residence A1', 'garden-residence-a1', 'GM-A1', 'Apartment', 3, 2.5, 184, 62, 'Ground floor', Availability::Available, ['Private landscaped garden', 'Floor-to-ceiling glazing', 'Natural stone finishes'], ['living', 'bedroom']),
                    $this->unit('Sky Residence B4', 'sky-residence-b4', 'GM-B4', 'Duplex', 4, 3.5, 268, 48, 'Levels 4–5', Availability::OnRequest, ['Panoramic terrace', 'Double-height living room', 'Private lift access'], ['bright', 'terrace']),
                    $this->unit('Courtyard Home C2', 'courtyard-home-c2', 'GM-C2', 'Townhouse', 3, 3, 231, 91, 'Ground + first', Availability::Reserved, ['Private courtyard', 'Family lounge', 'Two covered parking spaces'], ['minimal', 'kitchen']),
                ],
            ],
            [
                'name' => 'Park Residences', 'slug' => 'park-residences', 'location' => 'West Cairo',
                'short_description' => 'Contemporary residences arranged around generous parkland and shared everyday rituals.',
                'description' => 'Park Residences creates a close relationship between home and landscape through framed views, shaded terraces, and pedestrian-first spaces.',
                'latitude' => 30.028611, 'longitude' => 30.974444,
                'highlights' => ['Park frontage', 'Shaded terraces', 'Community spaces'], 'amenities' => ['Club lounge', 'Play lawns', 'Cycling route', 'Concierge'],
                'status' => ContentStatus::Published, 'published_at' => $published, 'is_featured' => true, 'display_order' => 2,
                'media' => [$this->photo('minimal', 'Minimal Park Residences home framed by landscape'), $this->photo('bright', 'Light-filled open plan interior')],
                'units' => [
                    $this->unit('Park View P3', 'park-view-p3', 'GM-P3', 'Apartment', 2, 2, 142, 24, 'Third floor', Availability::Available, ['Direct park view', 'Corner balcony', 'Integrated storage'], ['bright', 'living']),
                    $this->unit('Canopy Duplex D7', 'canopy-duplex-d7', 'GM-D7', 'Duplex', 4, 4, 286, 55, 'Levels 6–7', Availability::ComingSoon, ['Roof terrace', 'Flexible study', 'Park-facing master suite'], ['terrace', 'kitchen']),
                ],
            ],
            [
                'name' => 'Coast House', 'slug' => 'coast-house', 'location' => 'North Coast',
                'short_description' => 'Sea-facing homes pairing open horizons with tactile, quietly luxurious interiors.',
                'description' => 'Coast House is built around sea light, cross-breezes, and an easy movement between indoors and out.',
                'latitude' => 30.987222, 'longitude' => 28.773333,
                'highlights' => ['Open horizons', 'Sea terraces', 'Natural materials'], 'amenities' => ['Private beach', 'Sunset deck', 'Beach club', 'Housekeeping on request'],
                'status' => ContentStatus::Published, 'published_at' => $published, 'is_featured' => true, 'display_order' => 3,
                'media' => [$this->photo('coast', 'Contemporary Coast House residence beneath a clear sky'), $this->photo('terrace', 'Coastal terrace designed for outdoor living')],
                'units' => [
                    $this->unit('Horizon Villa V1', 'horizon-villa-v1', 'GM-V1', 'Villa', 5, 5.5, 412, 380, 'Ground + first', Availability::Available, ['Private pool', 'Sea-facing roof lounge', 'Guest suite'], ['coast', 'exterior']),
                    $this->unit('Dune Chalet C6', 'dune-chalet-c6', 'GM-C6', 'Chalet', 3, 2, 156, 36, 'First floor', Availability::Sold, ['Wide sea balcony', 'Turnkey interiors', 'Beach storage'], ['living', 'bedroom']),
                ],
            ],
            [
                'name' => 'Lake Quarter', 'slug' => 'lake-quarter', 'location' => 'New Administrative Capital',
                'short_description' => 'Waterfront homes balancing open views, generous space, and a measured urban rhythm.',
                'description' => 'Lake Quarter wraps a calm body of water with planted promenades, giving every home an open outlook and an everyday connection to landscape.',
                'latitude' => 30.007413, 'longitude' => 31.722633,
                'highlights' => ['Lake views', 'Green promenade', 'Contemporary architecture'],
                'amenities' => ['Lake club', 'Running loop', 'Children area', 'Concierge'],
                'status' => ContentStatus::Published, 'published_at' => $published, 'is_featured' => false, 'display_order' => 4,
                'seo_title' => 'Lake Quarter in the New Administrative Capital', 'seo_description' => 'Explore waterfront apartments and penthouses at Lake Quarter.',
                'media' => [$this->photo('lake', 'Lake Quarter residences reflected in calm water'), $this->photo('terrace', 'Generous terrace looking toward the lake')],
                'units' => [
                    $this->unit('Waterside Residence L2', 'waterside-residence-l2', 'GM-L2', 'Apartment', 2, 2, 148, 31, 'Second floor', Availability::Available, ['Direct lake view', 'Deep terrace', 'Open kitchen'], ['lake', 'living']),
                    $this->unit('Garden Loft L5', 'garden-loft-l5', 'GM-L5', 'Loft', 2, 2.5, 171, 28, 'Fifth floor', Availability::Available, ['Raised ceiling', 'Planted balcony', 'Flexible workspace'], ['minimal', 'kitchen']),
                    $this->unit('Pavilion Penthouse L9', 'pavilion-penthouse-l9', 'GM-L9', 'Penthouse', 4, 4.5, 338, 126, 'Ninth floor', Availability::OnRequest, ['Wraparound terrace', 'Private lounge', 'Panoramic water view'], ['lake', 'bright']),
                ],
            ],
            [
                'name' => 'Desert House', 'slug' => 'desert-house', 'location' => 'Sheikh Zayed',
                'short_description' => 'A low-rise villa collection inspired by desert calm and layered courtyard gardens.',
                'description' => 'Desert House reinterprets the courtyard home through quiet stone volumes, deep shade, and climate-aware native gardens.',
                'latitude' => 30.057932, 'longitude' => 30.948802,
                'highlights' => ['Low-density villas', 'Shaded courtyards', 'Native landscape'],
                'amenities' => ['Private clubhouse', 'Desert gardens', 'Cycling paths', '24-hour security'],
                'status' => ContentStatus::Published, 'published_at' => $published, 'is_featured' => false, 'display_order' => 5,
                'seo_title' => 'Desert House Villas in Sheikh Zayed', 'seo_description' => 'Explore courtyard villas and townhouses at Desert House, Sheikh Zayed.',
                'media' => [$this->photo('desert', 'Limestone Desert House villas framed by native planting'), $this->photo('minimal', 'Quiet contemporary Desert House interior')],
                'units' => [
                    $this->unit('Wadi Villa D1', 'wadi-villa-d1', 'GM-D1', 'Villa', 5, 5.5, 438, 410, 'Ground + first', Availability::Available, ['Central courtyard', 'Private pool', 'Guest majlis'], ['desert', 'living']),
                    $this->unit('Courtyard Villa D2', 'courtyard-villa-d2', 'GM-D2', 'Villa', 4, 4, 356, 295, 'Ground + first', Availability::Reserved, ['Interior garden', 'Family suite', 'Three parking spaces'], ['desert', 'bedroom']),
                    $this->unit('Sunset Townhouse D4', 'sunset-townhouse-d4', 'GM-D4', 'Townhouse', 3, 3, 244, 118, 'Ground + first', Availability::ComingSoon, ['West-facing terrace', 'Private garden', 'Multipurpose room'], ['terrace', 'kitchen']),
                ],
            ],
            [
                'name' => 'Palm Court', 'slug' => 'palm-court', 'location' => 'East Cairo',
                'short_description' => 'A future collection of courtyard homes currently in design development.',
                'description' => 'Palm Court explores shaded gardens and intimate pedestrian lanes as the foundation for a close-knit residential setting.',
                'highlights' => ['Courtyard living', 'Shaded lanes'], 'amenities' => ['Community garden'],
                'status' => ContentStatus::Draft, 'published_at' => null, 'is_featured' => false, 'display_order' => 6,
                'media' => [$this->photo('kitchen', 'Material study for the future Palm Court residences')], 'units' => [],
            ],
        ];
    }

    private function photo(string $image, string $alt): array
    {
        return compact('image', 'alt');
    }

    private function unit(string $title, string $slug, string $reference, string $type, float $bedrooms, float $bathrooms, float $indoor, float $outdoor, string $floor, Availability $availability, array $features, array $images): array
    {
        return [
            'title' => $title, 'slug' => $slug, 'reference_code' => $reference,
            'short_description' => "{$type} residence with carefully planned interiors and a strong connection to outdoor space.",
            'description' => "{$title} is planned as a sequence of calm, connected spaces. Living areas open naturally toward landscape and daylight, while private rooms are set apart for comfort and quiet.",
            'unit_type' => $type, 'bedrooms' => $bedrooms, 'bathrooms' => $bathrooms, 'indoor_area' => $indoor, 'outdoor_area' => $outdoor,
            'area_unit' => 'sqm', 'floor' => $floor, 'availability' => $availability, 'features' => $features,
            'status' => ContentStatus::Published, 'published_at' => now()->subDays(10),
            'seo_title' => "{$title} | GMark", 'seo_description' => "Explore specifications and media for {$title}.",
            'media' => array_map(fn (string $image) => $this->photo($image, "{$title} residential interior"), $images),
        ];
    }
}
