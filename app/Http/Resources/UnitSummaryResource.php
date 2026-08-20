<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cover = $this->getFirstMedia('cover') ?? $this->getFirstMedia('gallery');

        return [
            'id' => (string) $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'url' => '/'.app()->getLocale()."/units/{$this->slug}",
            'locale' => app()->getLocale(),
            'reference_code' => $this->reference_code,
            'short_description' => $this->short_description,
            'unit_type' => $this->unit_type,
            'bedrooms' => $this->bedrooms === null ? null : (float) $this->bedrooms,
            'bathrooms' => $this->bathrooms === null ? null : (float) $this->bathrooms,
            'indoor_area' => $this->indoor_area === null ? null : (float) $this->indoor_area,
            'area_unit' => $this->area_unit,
            'availability' => $this->availability?->value,
            'price_display' => $this->price_display,
            'cover' => $cover ? new MediaResource($cover) : null,
        ];
    }
}
