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
            'books' => $isCombo && $orderable->books ? $orderable->books->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'price' => $book->price,
                ];
            }) : null,
            'combos' => $isCombo && $orderable->combos ? $orderable->combos->map(function ($combo) {
                return [
                    'id' => $combo->id,
                    'name' => $combo->name,
                    'price' => $combo->price,
                ];
            }) : null,
        ];
    }
}
