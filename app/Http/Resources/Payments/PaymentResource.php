<?php

namespace App\Http\Resources\Payments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'payment_id' => $this->payment_id,
            'order_id' => $this->order_id,
            'provider' => $this->provider,
            'provider_order_id' => $this->provider_order_id,
            'provider_transaction_id' => $this->provider_transaction_id,
            'payment_method' => $this->payment_method,
            'amount' => $this->amount,
            'payment_status' => $this->payment_status,
            'payment_url' => $this->payment_url,
            'expires_at' => $this->expires_at,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
