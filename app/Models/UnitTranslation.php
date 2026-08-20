<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title', 'short_description', 'description', 'unit_type', 'floor',
        'price_display', 'features', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return ['features' => 'array'];
    }
}
