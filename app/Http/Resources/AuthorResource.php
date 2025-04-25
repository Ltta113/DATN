<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
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
            'biography' => $this->biography,
            'slug' => $this->slug,
            'photo' => $this->photo,
            'star_rating' => $this->star_rating,
            'star_rating_count' => $this->star_rating_count,
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'birth_date' => $this->birth_date,
            'book_count' => $this->books_count
        ];
    }
}
