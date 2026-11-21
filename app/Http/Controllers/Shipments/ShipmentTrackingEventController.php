<?php

namespace App\Http\Controllers\Shipments;

use App\Http\Controllers\Controller;
use App\Http\Resources\Shipments\ShipmentTrackingEventResource;
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

        return response()->json(['data' => ShipmentTrackingEventResource::collection($shipment->trackingEvents()->orderByDesc('happened_at')->get())]);
    }

    public function store(Request $request): JsonResponse
    {
        $raw = $request->getContent();

        // Biteship verification probe when adding the webhook: empty body.
        if (trim($raw) === '') {
            return response()->json(['message' => 'OK'], 200);
        }

        $payload = json_decode($raw, true) ?: $request->all();
        $deliveryId = $request->header('X-Biteship-Delivery-Id');
        $payloadEventId = $payload['external_event_id'] ?? $payload['event_id'] ?? null;

        // Dedup via delivery id or payload event id.
        if ($deliveryId && ShipmentTrackingEvent::where('external_event_id', $deliveryId)->exists()) {
            return response()->json(['duplicate' => true], 200);
        }
        if ($payloadEventId && ShipmentTrackingEvent::where('external_event_id', $payloadEventId)->exists()) {
            return response()->json(['duplicate' => true], 200);
        }

        $handled = $this->tracking->handleWebhook($payload, $deliveryId);

        if (! $handled) {
            return response()->json(['message' => 'Shipment not found.'], 404);
        }

        return response()->json(['message' => 'OK'], 200);
    }
}
