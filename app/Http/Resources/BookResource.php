<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'slug' => $this->slug,
            'isbn' => $this->isbn,
            'published_at' => $this->published_at,
            'cover_image' => $this->cover_image,
            'price' => $this->price,
            'stock' => $this->stock,
            'sold' => $this->sold,
            'star_rating' => $this->star_rating,
            'star_rating_count' => $this->star_rating_count,
            'language' => $this->language,
            'page_count' => $this->page_count,
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'publisher' => new PublisherResource($this->whenLoaded('publisher')),
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'quantity' => $this->when(isset($this->quantity), $this->quantity),
        ];
    }
}
