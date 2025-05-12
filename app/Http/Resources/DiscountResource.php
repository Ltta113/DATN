<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'percent' => $this->percent,
            'books' => BookResource::collection($this->whenLoaded('books')),
            'starts_at' => $this->starts_at,
            'expires_at' => $this->expires_at,
            'type' => $this->type,
            'value' => $this->value,
            'banner' => $this->banner,
        ];
    }
}
