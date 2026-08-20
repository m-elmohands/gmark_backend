<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'short_description', 'description', 'location', 'highlights',
        'amenities', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return ['highlights' => 'array', 'amenities' => 'array'];
    }
}
