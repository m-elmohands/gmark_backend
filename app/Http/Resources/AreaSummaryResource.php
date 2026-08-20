<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AreaSummaryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'url' => '/'.app()->getLocale()."/areas/{$this->slug}",
            'locale' => app()->getLocale(),
            'short_description' => $this->short_description,
            'location' => $this->residentialLocation?->name ?? $this->location,
            'location_details' => $this->residentialLocation ? [
                'id' => (string) $this->residentialLocation->id,
                'name' => $this->residentialLocation->name,
                'slug' => $this->residentialLocation->slug,
            ] : null,
            'is_featured' => $this->is_featured,
            'published_unit_count' => $this->whenCounted('units'),
            'cover' => $cover ? new MediaResource($cover) : null,
        ];
    }
}
