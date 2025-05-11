<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComboResource extends JsonResource
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
            'price' => $this->price,
            'image' => $this->image,
            'slug' => $this->slug,
            'description' => $this->description,
            'discount' => $this->discount,
            'stock' => $this->stock,
            'sold' => $this->sold,
            'books' => BookResource::collection($this->whenLoaded('books')->load(
                'authors',
                'categories'
            )),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'star_rating' => $this->star_rating,
            'star_rating_count' => $this->star_rating_count,
        ];
    }
}
