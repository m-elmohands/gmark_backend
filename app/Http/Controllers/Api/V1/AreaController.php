<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ListAreasRequest;
use App\Http\Requests\Api\V1\ListUnitsRequest;
use App\Http\Resources\AreaDetailResource;
use App\Http\Resources\AreaSummaryResource;
use App\Http\Resources\UnitSummaryResource;
use App\Services\AreaService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AreaController extends Controller
{
    public function __construct(private readonly AreaService $areas) {}

    public function index(ListAreasRequest $request): AnonymousResourceCollection
    {
        return AreaSummaryResource::collection($this->areas->listPublished($request->filters()));
    }

    public function show(string $slug): AreaDetailResource
    {
        return new AreaDetailResource($this->areas->getPublished($slug));
    }

    public function units(ListUnitsRequest $request, string $slug): AnonymousResourceCollection
    {
        return UnitSummaryResource::collection($this->areas->listPublishedUnits($slug, $request->validated()));
    }
}
