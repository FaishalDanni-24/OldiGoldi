<?php

namespace App\Http\Resources\Orders;

use App\Http\Resources\Payments\PaymentResource;
use App\Http\Resources\Shipments\ShipmentResource;
use App\Http\Resources\Users\AddressResource;
use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_id' => $this->order_id,
            'order_number' => $this->order_number,
            'customer_id' => $this->customer_id,
            'customer' => $this->whenLoaded('customer', fn () => new UserResource($this->customer)),
            'address_id' => $this->address_id,
            'address' => $this->whenLoaded('address', fn () => new AddressResource($this->address)),
            'order_status' => $this->order_status,
            'subtotal' => $this->subtotal,
            'shipping_cost' => $this->shipping_cost,
            'discount_amount' => $this->discount_amount,
            'total_amount' => $this->total_amount,
            'recipient_name' => $this->recipient_name,
            'recipient_phone' => $this->recipient_phone,
            'shipping_address_line' => $this->shipping_address_line,
            'shipping_district' => $this->shipping_district,
            'shipping_city' => $this->shipping_city,
            'shipping_province' => $this->shipping_province,
            'shipping_postal_code' => $this->shipping_postal_code,
            'customer_note' => $this->customer_note,
            'items' => $this->whenLoaded('items', fn () => OrderItemResource::collection($this->items)),
            'payment' => $this->whenLoaded('payment', fn () => new PaymentResource($this->payment)),
            'payments' => $this->whenLoaded('payments', fn () => PaymentResource::collection($this->payments)),
            'shipments' => $this->whenLoaded('shipments', fn () => ShipmentResource::collection($this->shipments)),
            'status_histories' => $this->whenLoaded('statusHistories', fn () => OrderStatusHistoryResource::collection($this->statusHistories)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
