<?php

namespace App\Http\Resources\Shipments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'shipment_id' => $this->shipment_id,
            'order_id' => $this->order_id,
            'tracking_provider' => $this->tracking_provider,
            'provider_shipment_id' => $this->provider_shipment_id,
            'courier_code' => $this->courier_code,
            'courier_name' => $this->courier_name,
            'tracking_number' => $this->tracking_number,
            'shipment_status' => $this->shipment_status,
            'shipped_at' => $this->shipped_at,
            'delivered_at' => $this->delivered_at,
            'last_synced_at' => $this->last_synced_at,
            'tracking_events' => $this->whenLoaded('trackingEvents', fn () => ShipmentTrackingEventResource::collection($this->trackingEvents)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
