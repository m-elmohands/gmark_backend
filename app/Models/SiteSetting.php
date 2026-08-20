<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SiteSetting extends Model implements HasMedia, TranslatableContract
{
    use InteractsWithMedia, Translatable;

    public array $translatedAttributes = ['site_name', 'tagline', 'site_description', 'address', 'meta_title', 'meta_description'];

    protected $fillable = ['email', 'phone', 'whatsapp', 'social_links'];

    protected function casts(): array
    {
        return ['social_links' => 'array'];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->useDisk(config('media-library.disk_name'))->singleFile()->acceptsMimeTypes(['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml']);
    }
}
