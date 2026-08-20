<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'site_name' => $this->site_name,
            'tagline' => $this->tagline,
            'site_description' => $this->site_description,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'social_links' => $this->social_links ?? [],
            'meta' => ['title' => $this->meta_title, 'description' => $this->meta_description],
            'logo' => $this->getFirstMedia('logo') ? new MediaResource($this->getFirstMedia('logo')) : null,
        ];
    }
}
