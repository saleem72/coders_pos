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
            'name' => $this->name,
            'email' => $this->email,
            'isActive' => $this->is_active,
            'avatar' => $this->avatar ? asset('storage/users/avatar/'.$this->avatar) : NULL,
            'isVerified' => $this->is_verified,
            'role' => $this->whenLoaded('role')->name
        ];
    }
}
