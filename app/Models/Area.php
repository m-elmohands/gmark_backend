<?php

namespace App\Models;

use App\Enums\ContentStatus;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Database\Factories\AreaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Area extends Model implements HasMedia, TranslatableContract
{
    /** @use HasFactory<AreaFactory> */
    use HasFactory, InteractsWithMedia, SoftDeletes, Translatable;

    public array $translatedAttributes = [
        'name', 'short_description', 'description', 'location', 'highlights',
        'amenities', 'seo_title', 'seo_description',
    ];

    protected static function booted(): void
    {
        static::creating(function (Area $area): void {
            if (! $area->slug) {
                $area->slug = Str::slug($area->name);
            }
            $area->created_by ??= Auth::id();
            $area->updated_by ??= Auth::id();
        });

        static::updating(function (Area $area): void {
            $area->updated_by = Auth::id() ?? $area->updated_by;
        });

        static::saving(function (Area $area): void {
            if ($area->status === ContentStatus::Published && ! $area->published_at) {
                $area->published_at = now();
            }
        });
    }

    protected $fillable = [
        'slug', 'location_id', 'latitude', 'longitude', 'status', 'is_featured', 'display_order',
        'published_at', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function residentialLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
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
            ->where('status', ContentStatus::Published->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
