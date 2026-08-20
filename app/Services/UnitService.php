<?php

namespace App\Services;

use App\Models\Unit;
use App\Repositories\Contracts\UnitRepositoryInterface;

readonly class UnitService
{
    public function __construct(private UnitRepositoryInterface $units) {}

    public function getPublished(string $slug): Unit
    {
        return $this->units->loadRelated($this->units->findPublishedBySlug($slug));
    }
}
