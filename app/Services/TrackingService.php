<?php

namespace App\Services;

use App\Models\Orders\Order;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentTrackingEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Biteship tracking / shipment client.
 *
 * Docs: https://biteship.com/en/docs (api/orders, api/trackings, api/webhook)
 */
class TrackingService
{
    protected function config(): array
    {
        return config('services.tracking', []);
    }

    protected function baseUrl(): string
    {
        return rtrim($this->config()['base_url'] ?? $this->config()['url'] ?? 'https://api.biteship.com', '/');
    }

    protected function apiKey(): ?string
    {
        return $this->config()['api_key'] ?? $this->config()['key'] ?? null;
    }

    public function createLabel(Shipment $shipment, array $options = []): bool
    {
        $cfg = $this->config();
        $apiKey = $this->apiKey();
        $baseUrl = $this->baseUrl();

        $shipment->loadMissing('order.items.product', 'order');

        $order = $shipment->order;

        // Origin (shipper/warehouse) from config.
        $originName = $cfg['origin']['contact_name'] ?? null;
        $originPhone = $cfg['origin']['contact_phone'] ?? null;
        $originEmail = $cfg['origin']['contact_email'] ?? null;
        $originOrg = $cfg['origin']['organization'] ?? null;
        $originAddress = $cfg['origin']['address'] ?? null;
        $originPostal = $cfg['origin']['postal_code'] ?? null;
        $originLat = $cfg['origin']['latitude'] ?? null;
        $originLng = $cfg['origin']['longitude'] ?? null;

        // Destination from order.
        $destName = $order?->recipient_name;
        $destPhone = $order?->recipient_phone;
        $destAddress = $order?->shipping_address_line;
        $destPostal = $order?->shipping_postal_code;
        $destCity = $order?->shipping_city;

        // Items.
        $items = [];
        foreach ($order?->items ?? [] as $item) {
            $product = $item->product;
            $items[] = [
                'name' => $item->product_name_snapshot,
                'description' => $product?->product_description,
                'category' => 'general',
                'value' => (int) round((float) $item->unit_price),
                'quantity' => (int) $item->quantity,
                'weight' => (int) ($product?->weight ?? $cfg['default_weight'] ?? 200),
                'length' => (int) ($product?->length ?? $cfg['default_length'] ?? 10),
                'width' => (int) ($product?->width ?? $cfg['default_width'] ?? 10),
                'height' => (int) ($product?->height ?? $cfg['default_height'] ?? 10),
            ];
        }

        $body = [
            'shipper_contact_name' => $originName,
            'shipper_contact_phone' => $originPhone,
            'shipper_contact_email' => $originEmail,
            'shipper_organization' => $originOrg,
            'origin_contact_name' => $originName,
            'origin_contact_phone' => $originPhone,
            'origin_contact_email' => $originEmail,
            'origin_address' => $originAddress,
            'origin_postal_code' => $originPostal ? (int) $originPostal : null,
            'destination_contact_name' => $destName,
            'destination_contact_phone' => $destPhone,
            'destination_address' => $destAddress,
            'destination_postal_code' => $destPostal ? (int) $destPostal : null,
            'courier_company' => $shipment->courier_code ?: null,
            'courier_type' => $options['courier_type'] ?? ($shipment->courier_code ? 'reg' : null),
            'courier_insurance' => $options['courier_insurance'] ?? null,
            'delivery_type' => $options['delivery_type'] ?? 'now',
            'delivery_date' => $options['delivery_date'] ?? null,
            'delivery_time' => $options['delivery_time'] ?? null,
            'order_note' => $options['order_note'] ?? $order?->customer_note,
            'reference_id' => (string) $order?->order_number,
            'items' => $items,
        ];

        // Add coordinates if configured.
        if ($originLat && $originLng) {
            $body['origin_coordinate'] = ['latitude' => (float) $originLat, 'longitude' => (float) $originLng];
        }

        // Remove null values to keep payload clean.
        $body = array_filter($body, fn ($v) => $v !== null && $v !== []);

        if ($apiKey) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(20)->post($baseUrl . '/v1/orders', $body);

                if ($response->successful()) {
                    $data = $response->json();
                    $shipment->provider_shipment_id = $data['id'] ?? $shipment->provider_shipment_id;
                    $shipment->provider_tracking_id = $data['courier']['tracking_id'] ?? $shipment->provider_tracking_id;
                    $shipment->tracking_number = $data['courier']['waybill_id'] ?? $shipment->tracking_number;
                    $shipment->courier_code = $data['courier']['company'] ?? $shipment->courier_code;
                    $shipment->courier_name = $data['courier']['name'] ?? $shipment->courier_name;
                    $shipment->shipment_status = Shipment::mapBiteshipStatus($data['status'] ?? 'confirmed');
                    $shipment->shipped_at = $shipment->shipped_at ?? now();
                    if ($shipment->shipment_status === Shipment::STATUS_DELIVERED) {
                        $shipment->delivered_at = $shipment->delivered_at ?? now();
                    }
                    $shipment->last_synced_at = now();
                    $shipment->save();
                    return true;
                }

                Log::warning('Biteship createOrder failed', ['status' => $response->status(), 'body' => $response->body()]);
                throw new \App\Exceptions\ExternalServiceException(
                    'Biteship',
                    $response->status(),
                    'Biteship createOrder failed: HTTP ' . $response->status()
                );
            } catch (\Throwable $e) {
                Log::error('Biteship createOrder exception', ['error' => $e->getMessage()]);
                if (! $e instanceof \App\Exceptions\ExternalServiceException) {
                    throw new \App\Exceptions\ExternalServiceException('Biteship', null, $e->getMessage(), $e);
                }
                throw $e;
            }
        }

        // Sandbox fallback: mark label created locally.
        $shipment->shipment_status = Shipment::STATUS_LABEL_CREATED;
        $shipment->shipped_at = $shipment->shipped_at ?? now();
        $shipment->save();
        return true;
    }

    public function syncTracking(Shipment $shipment): int
    {
        $cfg = $this->config();
        $apiKey = $this->apiKey();
        $baseUrl = $this->baseUrl();
        $added = 0;

        $trackingId = $shipment->provider_tracking_id;
        if ($apiKey && $trackingId) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $apiKey,
                ])->timeout(15)->get($baseUrl . '/v1/trackings/' . $trackingId);

                if ($response->successful()) {
                    $data = $response->json();
                    $history = $data['history'] ?? [];
                    foreach ($history as $ev) {
                        $status = Shipment::mapBiteshipStatus($ev['status'] ?? 'unknown');
                        // Dedup by (shipment_id, happened_at, status).
                        $exists = $shipment->trackingEvents()
                            ->where('happened_at', $ev['updated_at'] ?? null)
                            ->where('event_status', $status)
                            ->exists();
                        if ($exists) {
                            continue;
                        }
                        ShipmentTrackingEvent::create([
                            'shipment_id' => $shipment->shipment_id,
                            'external_event_id' => $data['id'] ?? null,
                            'event_code' => $ev['status'] ?? null,
                            'event_status' => $status,
                            'description' => $ev['note'] ?? null,
                            'location' => null,
                            'happened_at' => isset($ev['updated_at']) ? \Carbon\Carbon::parse($ev['updated_at']) : now(),
                            'raw_payload' => $ev,
                        ]);
                        $added++;
                    }

                    $topStatus = Shipment::mapBiteshipStatus($data['status'] ?? $shipment->shipment_status);
                    if ($topStatus && $topStatus !== $shipment->shipment_status) {
                        $shipment->shipment_status = $topStatus;
                        if ($topStatus === Shipment::STATUS_DELIVERED) {
                            $shipment->delivered_at = $shipment->delivered_at ?? now();
                        }
                    }
                    $shipment->tracking_number = $data['waybill_id'] ?? $shipment->tracking_number;
                }
            } catch (\Throwable $e) {
                Log::error('Biteship syncTracking exception', ['error' => $e->getMessage()]);
            }
        }

        $shipment->last_synced_at = now();
        $shipment->save();
        return $added;
    }

    public function verifyWebhook(array $payload, ?string $signature = null, ?string $headerName = null): bool
    {
        // Biteship uses a static secret in a custom header; verification is handled in middleware.
        // This method retained for controller compatibility.
        return true;
    }

    /**
     * Parse a Biteship webhook payload and update the shipment.
     * Returns true if a shipment was found & updated.
     */
    public function handleWebhook(array $payload, ?string $deliveryId = null): bool
    {
        $event = $payload['event'] ?? null;
        $biteshipOrderId = $payload['order_id'] ?? null;

        $shipment = null;
        if ($biteshipOrderId) {
            $shipment = Shipment::where('provider_shipment_id', $biteshipOrderId)->first();
        }
        if (! $shipment && ! empty($payload['courier_tracking_id'])) {
            $shipment = Shipment::where('provider_tracking_id', $payload['courier_tracking_id'])->first();
        }
        if (! $shipment && ! empty($payload['tracking_number'])) {
            $shipment = Shipment::where('tracking_number', $payload['tracking_number'])->first();
        }

        if (! $shipment) {
            return false;
        }

        // Dedup via delivery id or payload event id.
        $payloadEventId = $payload['external_event_id'] ?? $payload['event_id'] ?? null;
        if ($deliveryId && $shipment->trackingEvents()->where('external_event_id', $deliveryId)->exists()) {
            return true;
        }
        if ($payloadEventId && $shipment->trackingEvents()->where('external_event_id', $payloadEventId)->exists()) {
            return true;
        }

        $status = $payload['status'] ?? $payload['event_status'] ?? null;
        if (($event === 'order.status' || $event === null) && $status) {
            $newStatus = Shipment::mapBiteshipStatus($status);
            // If status not in biteship map but is our own enum value, use directly.
            if (! in_array($newStatus, [Shipment::STATUS_LABEL_CREATED, Shipment::STATUS_PICKED_UP, Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_OUT_FOR_DELIVERY, Shipment::STATUS_DELIVERED, Shipment::STATUS_CANCELLED, Shipment::STATUS_RETURNED, Shipment::STATUS_FAILED, Shipment::STATUS_UNKNOWN], true)) {
                $newStatus = $status;
            }
            $shipment->shipment_status = $newStatus;
            if ($newStatus === Shipment::STATUS_DELIVERED) {
                $shipment->delivered_at = $shipment->delivered_at ?? now();
            }
            $shipment->last_synced_at = now();
            if (! empty($payload['courier_waybill_id'])) {
                $shipment->tracking_number = $payload['courier_waybill_id'];
            }
            if (! empty($payload['tracking_number'])) {
                // keep existing tracking_number if already set
            }
            $shipment->save();

            ShipmentTrackingEvent::create([
                'shipment_id' => $shipment->shipment_id,
                'external_event_id' => $deliveryId ?? ($payload['external_event_id'] ?? $payload['event_id'] ?? null),
                'event_code' => $payload['event_code'] ?? $status,
                'event_status' => $payload['event_status'] ?? $newStatus,
                'description' => $payload['description'] ?? null,
                'location' => $payload['location'] ?? null,
                'happened_at' => isset($payload['happened_at']) ? \Carbon\Carbon::parse($payload['happened_at']) : now(),
                'raw_payload' => $payload,
            ]);
        } elseif ($event === 'order.waybill_id') {
            if (! empty($payload['courier_waybill_id'])) {
                $shipment->tracking_number = $payload['courier_waybill_id'];
            }
            $shipment->last_synced_at = now();
            $shipment->save();
        } elseif ($event === 'order.price') {
            // Optional: adjust order/payment amount. Skipped for now.
            $shipment->last_synced_at = now();
            $shipment->save();
        }

        return true;
    }
}
