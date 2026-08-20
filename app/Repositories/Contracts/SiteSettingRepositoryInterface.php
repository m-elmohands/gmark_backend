<?php

namespace App\Repositories\Contracts;

use App\Models\SiteSetting;

interface SiteSettingRepositoryInterface
{
    public function get(): ?SiteSetting;
}
