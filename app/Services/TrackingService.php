<?php

namespace App\Services;

use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentTrackingEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sandbox client for the Tracking / Courier service.
 */
class TrackingService
{
    protected function config(): array
    {
        return config('services.tracking', []);
    }

    public function createLabel(Shipment $shipment): bool
    {
        $cfg = $this->config();
        $baseUrl = $cfg['url'] ?? null;
        $apiKey = $cfg['key'] ?? null;

        if ($baseUrl && $apiKey) {
            try {
                $response = Http::withToken($apiKey)
                    ->timeout(15)
                    ->post(rtrim($baseUrl, '/') . '/shipments', [
                        'order_number' => $shipment->order?->order_number,
                        'courier_code' => $shipment->courier_code,
                        'recipient' => [
                            'name' => $shipment->order?->recipient_name,
                            'phone' => $shipment->order?->recipient_phone,
                            'address' => $shipment->order?->shipping_address_line,
                            'city' => $shipment->order?->shipping_city,
                            'postal_code' => $shipment->order?->shipping_postal_code,
                        ],
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $shipment->provider_shipment_id = $data['shipment_id'] ?? $shipment->provider_shipment_id;
                    $shipment->tracking_number = $data['tracking_number'] ?? $shipment->tracking_number;
                    $shipment->tracking_provider = $data['provider'] ?? $shipment->tracking_provider;
                    $shipment->shipment_status = Shipment::STATUS_LABEL_CREATED;
                    $shipment->shipped_at = now();
                    $shipment->save();
                    return true;
                }

                Log::warning('Tracking createLabel failed', ['status' => $response->status(), 'body' => $response->body()]);
            } catch (\Throwable $e) {
                Log::error('Tracking createLabel exception', ['error' => $e->getMessage()]);
            }
        }

        // Sandbox fallback: mark label created locally
        $shipment->shipment_status = Shipment::STATUS_LABEL_CREATED;
        $shipment->shipped_at = $shipment->shipped_at ?? now();
        $shipment->save();
        return true;
    }

    public function syncTracking(Shipment $shipment): int
    {
        $cfg = $this->config();
        $baseUrl = $cfg['url'] ?? null;
        $apiKey = $cfg['key'] ?? null;
        $added = 0;

        if ($baseUrl && $apiKey && $shipment->tracking_number) {
            try {
                $response = Http::withToken($apiKey)
                    ->timeout(15)
                    ->get(rtrim($baseUrl, '/') . '/track/' . $shipment->tracking_number);

                if ($response->successful()) {
                    $events = $response->json('events', []);
                    foreach ($events as $ev) {
                        $externalId = $ev['id'] ?? null;
                        if ($externalId && $shipment->trackingEvents()->where('external_event_id', $externalId)->exists()) {
                            continue;
                        }
                        ShipmentTrackingEvent::create([
                            'shipment_id' => $shipment->shipment_id,
                            'external_event_id' => $externalId,
                            'event_code' => $ev['code'] ?? null,
                            'event_status' => $ev['status'] ?? 'unknown',
                            'description' => $ev['description'] ?? null,
                            'location' => $ev['location'] ?? null,
                            'happened_at' => isset($ev['happened_at']) ? \Carbon\Carbon::parse($ev['happened_at']) : now(),
                            'raw_payload' => $ev,
                        ]);
                        $added++;
                    }

                    $latest = $events[0]['status'] ?? null;
                    if ($latest && defined(Shipment::class . '::STATUS_' . strtoupper($latest))) {
                        $shipment->shipment_status = constant(Shipment::class . '::STATUS_' . strtoupper($latest));
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Tracking sync exception', ['error' => $e->getMessage()]);
            }
        }

        $shipment->last_synced_at = now();
        $shipment->save();
        return $added;
    }

    public function verifyWebhook(array $payload, ?string $signature = null): bool
    {
        $secret = $this->config()['webhook_secret'] ?? null;
        if ($secret && $signature) {
            $expected = hash_hmac('sha256', json_encode($payload), $secret);
            return hash_equals($expected, $signature);
        }
        return true;
    }
}
