<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user_id' => $this->user_id,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'email' => $this->email,
            'payment_method' => $this->payment_method,
            'note' => $this->note,
            'order_items_count' => $this->orderItems->count(),
            'province' => $this->province,
            'district' => $this->district,
            'ward' => $this->ward,
            'star_rating' => $this->star_rating,
            'star_rating_count' => $this->star_rating_count,
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            // 'order_items' => OrderItemResource::collection($this->orderItems),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
