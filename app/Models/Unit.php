<?php

namespace App\Models;

use App\Enums\Availability;
use App\Enums\ContentStatus;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Database\Factories\UnitFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Unit extends Model implements HasMedia, TranslatableContract
{
    /** @use HasFactory<UnitFactory> */
    use HasFactory, InteractsWithMedia, SoftDeletes, Translatable;

    public array $translatedAttributes = [
        'title', 'short_description', 'description', 'unit_type', 'floor',
        'price_display', 'features', 'seo_title', 'seo_description',
    ];

    protected static function booted(): void
    {
        static::creating(function (Unit $unit): void {
            if (! $unit->slug) {
                $unit->slug = Str::slug($unit->title);
            }
            $unit->created_by ??= Auth::id();
            $unit->updated_by ??= Auth::id();
        });

        static::updating(function (Unit $unit): void {
            $unit->updated_by = Auth::id() ?? $unit->updated_by;
        });

        static::saving(function (Unit $unit): void {
            if ($unit->status === ContentStatus::Published && ! $unit->published_at) {
                $unit->published_at = now();
            }
        });
    }

    protected $fillable = [
        'area_id', 'slug', 'reference_code', 'bedrooms', 'bathrooms', 'indoor_area',
        'outdoor_area', 'area_unit', 'availability', 'status', 'published_at',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'availability' => Availability::class,
            'published_at' => 'datetime',
            'bedrooms' => 'decimal:1',
            'bathrooms' => 'decimal:1',
            'indoor_area' => 'decimal:2',
            'outdoor_area' => 'decimal:2',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->useDisk(config('media-library.disk_name'))
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();

        $this->addMediaCollection('gallery')
            ->useDisk(config('media-library.disk_name'))
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'video/mp4', 'video/webm']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media && ! str_starts_with($media->mime_type, 'image/')) {
            return;
        }

        $this->addMediaConversion('thumb')->width(720)->height(540)->performOnCollections('cover', 'gallery');
        $this->addMediaConversion('web')->width(1920)->performOnCollections('cover', 'gallery');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('units.status', ContentStatus::Published->value)
            ->whereNotNull('units.published_at')
            ->where('units.published_at', '<=', now())
            ->whereHas('area', fn (Builder $area) => $area->published());
    }
}
