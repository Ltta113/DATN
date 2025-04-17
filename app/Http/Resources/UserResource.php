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
            'registration_date' => $this->registration_date,
            'last_login' => $this->last_login,
            'status' => $this->status,
            'avatar' => $this->avatar,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_type' => $this->user_type,
        ];
    }
}
