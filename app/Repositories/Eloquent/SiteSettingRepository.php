<?php

namespace App\Repositories\Eloquent;

use App\Models\SiteSetting;
use App\Repositories\Contracts\SiteSettingRepositoryInterface;

class SiteSettingRepository implements SiteSettingRepositoryInterface
{
    public function get(): ?SiteSetting
    {
        return SiteSetting::query()->with(['translations', 'media'])->first();
    }
}
