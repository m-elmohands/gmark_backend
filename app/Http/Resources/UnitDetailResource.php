<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge((new UnitSummaryResource($this->resource))->toArray($request), [
            'description' => $this->description,
            'outdoor_area' => $this->outdoor_area === null ? null : (float) $this->outdoor_area,
            'floor' => $this->floor,
            'features' => $this->features ?? [],
            'area' => [
                'id' => (string) $this->area->id,
                'name' => $this->area->name,
                'slug' => $this->area->slug,
                'url' => '/'.app()->getLocale()."/areas/{$this->area->slug}",
                'location' => $this->area->residentialLocation?->name ?? $this->area->location,
            ],
            'media' => MediaResource::collection($this->getMedia('cover')->concat($this->getMedia('gallery'))),
            'related_units' => UnitSummaryResource::collection($this->whenLoaded('relatedUnits')),
            'seo' => [
                'title' => $this->seo_title ?: $this->title,
                'description' => $this->seo_description ?: $this->short_description,
                'canonical_url' => url('/'.app()->getLocale()."/units/{$this->slug}"),
                'image' => $this->getFirstMediaUrl('cover') ?: null,
                'noindex' => false,
            ],
            'published_at' => $this->published_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ]);
    }
}
