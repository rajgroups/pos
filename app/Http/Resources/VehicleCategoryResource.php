<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'type_key' => $this->type_key,
            'service_mode' => $this->service_mode,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'tagline' => $this->tagline,
            'starting_fare' => $this->starting_fare,
            'price_label' => $this->price_label,
            'eta' => $this->eta,
            'image' => $this->image,
            'image_url' => $this->image
                ? asset('storage/' . ltrim($this->image, '/'))
                : null,
            'icon' => $this->icon,
            'icon_url' => $this->icon && str_contains($this->icon, '/')
                ? asset('storage/' . ltrim($this->icon, '/'))
                : null,
            'accent_color' => $this->accent_color,
            'gradient_start' => $this->gradient_start,
            'gradient_end' => $this->gradient_end,
            'max_capacity' => $this->max_capacity,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'pricing' => new VehicleCategoryPricingResource($this->whenLoaded('pricing')),
            'children' => $this->relationLoaded('children')
                ? VehicleCategoryResource::collection($this->children)
                : [],
        ];
    }
}
