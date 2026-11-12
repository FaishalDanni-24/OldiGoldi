<?php

namespace App\Http\Controllers\Shipments;

use App\Http\Controllers\Controller;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentTrackingEvent;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentTrackingEventController extends Controller
{
    public function __construct(protected TrackingService $tracking) {}

    public function index(Shipment $shipment): JsonResponse
    {
        $user = request()->user();
        if (! $user || ($shipment->order->customer_id !== $user->user_id && $user->role?->role_name !== 'admin')) {
            abort(403);
        }

        return response()->json(['data' => $shipment->trackingEvents()->orderByDesc('happened_at')->get()]);
    }

    public function store(Request $request, string $provider = 'sandbox'): JsonResponse
    {
        $payload = $request->all();
        $signature = $request->header('X-Signature') ?? $request->header('X-Webhook-Signature');

        if (! $this->tracking->verifyWebhook($payload, $signature)) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $shipmentId = $payload['shipment_id'] ?? $payload['tracking_number'] ?? null;
        $shipment = null;
        if ($shipmentId) {
            $shipment = Shipment::where('provider_shipment_id', $shipmentId)
                ->orWhere('tracking_number', $shipmentId)
                ->first();
        }

        if (! $shipment) {
            return response()->json(['message' => 'Shipment not found.'], 404);
        }

        $externalId = $payload['event_id'] ?? $payload['id'] ?? null;
        if ($externalId && $shipment->trackingEvents()->where('external_event_id', $externalId)->exists()) {
            return response()->json(['duplicate' => true]);
        }

        $event = ShipmentTrackingEvent::create([
            'shipment_id' => $shipment->shipment_id,
            'external_event_id' => $externalId,
            'event_code' => $payload['code'] ?? null,
            'event_status' => $payload['status'] ?? 'unknown',
            'description' => $payload['description'] ?? null,
            'location' => $payload['location'] ?? null,
            'happened_at' => isset($payload['happened_at']) ? \Carbon\Carbon::parse($payload['happened_at']) : now(),
            'raw_payload' => $payload,
        ]);

        // update shipment status if provided
        if (! empty($payload['status']) && defined(Shipment::class . '::STATUS_' . strtoupper($payload['status']))) {
            $shipment->shipment_status = constant(Shipment::class . '::STATUS_' . strtoupper($payload['status']));
            $shipment->last_synced_at = now();
            if ($payload['status'] === 'delivered') {
                $shipment->delivered_at = now();
            }
            $shipment->save();
        }

        return response()->json(['data' => $event], 201);
    }
}
