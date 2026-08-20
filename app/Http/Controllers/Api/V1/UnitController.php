<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitDetailResource;
use App\Services\UnitService;

class UnitController extends Controller
{
    public function __construct(private readonly UnitService $units) {}

    public function show(string $slug): UnitDetailResource
    {
        return new UnitDetailResource($this->units->getPublished($slug));
    }
}
