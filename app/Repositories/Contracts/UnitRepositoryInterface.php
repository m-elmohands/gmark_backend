<?php

namespace App\Repositories\Contracts;

use App\Models\Unit;

interface UnitRepositoryInterface
{
    public function findPublishedBySlug(string $slug): Unit;

    public function loadRelated(Unit $unit, int $limit = 4): Unit;
}
