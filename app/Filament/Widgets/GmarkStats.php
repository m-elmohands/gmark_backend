<?php

namespace App\Filament\Widgets;

use App\Enums\ContentStatus;
use App\Models\Area;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GmarkStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(__('Areas'), Area::query()->count())->description(__('Residential destinations')),
            Stat::make(__('Units'), Unit::query()->count())->description(__('Homes in the portfolio')),
            Stat::make(__('Published'), Area::query()->where('status', ContentStatus::Published)->count() + Unit::query()->where('status', ContentStatus::Published)->count())->description(__('Visible content')),
            Stat::make(__('Drafts'), Area::query()->where('status', ContentStatus::Draft)->count() + Unit::query()->where('status', ContentStatus::Draft)->count())->description(__('Awaiting publication')),
        ];
    }
}
