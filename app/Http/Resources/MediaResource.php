<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $isImage = str_starts_with($this->mime_type, 'image/');
        $url = $isImage && $this->hasGeneratedConversion('web')
            ? $this->getFullUrl('web')
            : $this->getFullUrl();

        return [
            'id' => (string) $this->id,
            'type' => $isImage ? 'image' : 'video',
            'alt' => $this->getCustomProperty('alt_text') ?: $this->name,
            'caption' => $this->getCustomProperty('caption'),
            'sort_order' => $this->order_column,
            'is_cover' => $this->collection_name === 'cover',
            'image' => $isImage ? [
                'src' => $url,
                'width' => $this->getCustomProperty('width'),
                'height' => $this->getCustomProperty('height'),
                'blur_data_url' => null,
                'sources' => [],
            ] : null,
            'video' => $isImage ? null : [
                'src' => $url,
                'mime_type' => $this->mime_type,
                'duration_seconds' => $this->getCustomProperty('duration_seconds'),
                'poster' => $this->getCustomProperty('poster_url'),
                'provider' => 'upload',
            ],
        ];
    }
}
