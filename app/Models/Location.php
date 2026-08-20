<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    public array $translatedAttributes = ['name', 'description'];

    protected $fillable = ['slug', 'latitude', 'longitude', 'is_active', 'display_order'];

    protected function casts(): array
    {
        return ['latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'is_active' => 'boolean'];
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }
}
