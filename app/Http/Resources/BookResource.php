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
            'language' => $this->language,
            'page_count' => $this->page_count,
            'publisher' => new PublisherResource($this->whenLoaded('publisher')),
            'authors' => AuthorResource::collection($this->whenLoaded('book_authors')),
            'categories' => CategoryResource::collection($this->whenLoaded('book_categories')),
        ];
    }
}
