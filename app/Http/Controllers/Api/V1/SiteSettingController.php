<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiteSettingResource;
use App\Repositories\Contracts\SiteSettingRepositoryInterface;
use Illuminate\Http\JsonResponse;

class SiteSettingController extends Controller
{
    public function __construct(private readonly SiteSettingRepositoryInterface $settings) {}

    public function show(): SiteSettingResource|JsonResponse
    {
        $settings = $this->settings->get();

        return $settings ? new SiteSettingResource($settings) : response()->json(['message' => 'Settings not found.'], 404);
    }
}
