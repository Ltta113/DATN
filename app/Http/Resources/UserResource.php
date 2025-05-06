<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'full_name' => $this->full_name,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'birth_day' => $this->birth_day,
            'status' => $this->status,
            'avatar' => $this->avatar,
            'district' => $this->district,
            'province' => $this->province,
            'ward' => $this->ward,
            'wallet' => $this->wallet->balance ?? 0,
            'bookmarks' => BookResource::collection($this->bookmarks->where('status', '<>', 'deleted')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
