<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'role_id' => $this->role_id,
            'role' => $this->whenLoaded('role', fn () => new RoleResource($this->role)),
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'account_status' => $this->account_status,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
