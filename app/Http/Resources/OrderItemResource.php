<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $orderable = $this->orderable;
        $isCombo = $orderable instanceof \App\Models\Combo;

        return [
            'id' => $this->id,
            'orderable_type' => $isCombo ? 'combo' : 'book',
            'orderable_id' => $orderable->id,
            'name' => $isCombo ? $orderable->name : $orderable->title,
            'image' => $isCombo ? $orderable->image : $orderable->cover_image,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'books' => $isCombo ? $orderable->books->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'price' => $book->price,
                    'images' => [
                        'cover' => $book->cover_image,
                        'gallery' => $book->images ? json_decode($book->images) : [],
                    ],
                ];
            }) : null,
        ];
    }
}
