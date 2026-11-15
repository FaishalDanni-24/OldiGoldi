<?php

namespace App\Http\Resources\Payments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentWebhookEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'webhook_id' => $this->webhook_id,
            'provider' => $this->provider,
            'provider_event_id' => $this->provider_event_id,
            'payment_id' => $this->payment_id,
            'event_type' => $this->event_type,
            'payload' => $this->payload,
            'processing_status' => $this->processing_status,
            'received_at' => $this->received_at,
            'processed_at' => $this->processed_at,
            'error_message' => $this->error_message,
        ];
    }
}
