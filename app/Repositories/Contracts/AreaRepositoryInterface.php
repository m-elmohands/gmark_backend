<?php

namespace App\Repositories\Contracts;

use App\Models\Area;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AreaRepositoryInterface
{
    public function paginatePublished(array $filters): LengthAwarePaginator;

    public function findPublishedBySlug(string $slug, bool $withUnits = false): Area;

    public function paginatePublishedUnits(Area $area, array $filters): LengthAwarePaginator;
}
