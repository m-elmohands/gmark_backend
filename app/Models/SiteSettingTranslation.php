<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSettingTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['site_name', 'tagline', 'site_description', 'address', 'meta_title', 'meta_description'];
}
