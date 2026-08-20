<?php

namespace App\Services;

use App\Models\Area;
use App\Repositories\Contracts\AreaRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

readonly class AreaService
{
    public function __construct(private AreaRepositoryInterface $areas) {}

    public function listPublished(array $filters): LengthAwarePaginator
    {
        return $this->areas->paginatePublished($filters);
    }

    public function getPublished(string $slug): Area
    {
        return $this->areas->findPublishedBySlug($slug, true);
    }

    public function listPublishedUnits(string $slug, array $filters): LengthAwarePaginator
    {
        return $this->areas->paginatePublishedUnits($this->areas->findPublishedBySlug($slug), $filters);
    }
}
