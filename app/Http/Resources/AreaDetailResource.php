<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AreaDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge((new AreaSummaryResource($this->resource))->toArray($request), [
            'description' => $this->description,
            'coordinates' => $this->latitude !== null && $this->longitude !== null ? [
                'latitude' => (float) $this->latitude,
                'longitude' => (float) $this->longitude,
            ] : null,
            'highlights' => $this->highlights ?? [],
            'amenities' => $this->amenities ?? [],
            'media' => MediaResource::collection($this->getMedia('cover')->concat($this->getMedia('gallery'))),
            'units' => UnitSummaryResource::collection($this->whenLoaded('units')),
            'seo' => [
                'title' => $this->seo_title ?: $this->name,
                'description' => $this->seo_description ?: $this->short_description,
                'canonical_url' => url('/'.app()->getLocale()."/areas/{$this->slug}"),
                'image' => $this->getFirstMediaUrl('cover') ?: null,
                'noindex' => false,
            ],
            'published_at' => $this->published_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ]);
    }
}
