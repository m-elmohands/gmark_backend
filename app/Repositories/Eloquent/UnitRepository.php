<?php

namespace App\Repositories\Eloquent;

use App\Models\Unit;
use App\Repositories\Contracts\UnitRepositoryInterface;

class UnitRepository implements UnitRepositoryInterface
{
    public function findPublishedBySlug(string $slug): Unit
    {
        return Unit::query()->published()->where('slug', $slug)
            ->with(['area.translations', 'area.residentialLocation.translations', 'translations', 'media'])->firstOrFail();
    }

    public function loadRelated(Unit $unit, int $limit = 4): Unit
    {
        $related = Unit::query()->published()->where('area_id', $unit->area_id)
            ->whereKeyNot($unit->id)->with(['media', 'translations'])
            ->latest('published_at')->limit($limit)->get();

        return $unit->setRelation('relatedUnits', $related);
    }
}
