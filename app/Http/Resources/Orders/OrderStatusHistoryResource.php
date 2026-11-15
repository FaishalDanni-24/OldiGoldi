<?php

namespace App\Http\Resources\Orders;

use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'history_id' => $this->history_id,
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
            'old_status' => $this->old_status,
            'new_status' => $this->new_status,
            'note' => $this->note,
            'changed_at' => $this->changed_at,
        ];
    }
}
