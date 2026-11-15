<?php

namespace App\Http\Resources\Shipments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentTrackingEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'tracking_event_id' => $this->tracking_event_id,
            'shipment_id' => $this->shipment_id,
            'external_event_id' => $this->external_event_id,
            'event_code' => $this->event_code,
            'event_status' => $this->event_status,
            'description' => $this->description,
            'location' => $this->location,
            'happened_at' => $this->happened_at,
            'raw_payload' => $this->raw_payload,
            'created_at' => $this->created_at,
        ];
    }
}
