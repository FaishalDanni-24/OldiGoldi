<?php

namespace App\Http\Controllers\Shipments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipments\CreateShipmentRequest;
use App\Http\Resources\Shipments\ShipmentResource;
use App\Models\Orders\Order;
use App\Models\Shipments\Shipment;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function __construct(protected TrackingService $tracking) {}

    public function show(Shipment $shipment): JsonResponse
    {
        $user = request()->user();
        if (! $user || ($shipment->order->customer_id !== $user->user_id && $user->role?->role_name !== 'admin')) {
            abort(403);
        }

        return response()->json(['data' => new ShipmentResource($shipment->load('trackingEvents', 'order'))]);
    }

    public function store(CreateShipmentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $order = Order::findOrFail($validated['order_id']);

        $shipment = Shipment::create([
            'order_id' => $order->order_id,
            'tracking_provider' => 'biteship',
            'courier_code' => $validated['courier_company'],
            'courier_name' => null,
            'tracking_number' => null,
            'shipment_status' => Shipment::STATUS_PENDING,
        ]);

        try {
            $this->tracking->createLabel($shipment, [
                'courier_type' => $validated['courier_type'],
                'delivery_type' => $validated['delivery_type'] ?? 'now',
                'delivery_date' => $validated['delivery_date'] ?? null,
                'delivery_time' => $validated['delivery_time'] ?? null,
                'courier_insurance' => $validated['courier_insurance'] ?? null,
                'order_note' => $validated['order_note'] ?? null,
            ]);
        } catch (\App\Exceptions\ExternalServiceException $e) {
            return response()->json(['message' => $e->getMessage()], 502);
        }

        return response()->json(['data' => new ShipmentResource($shipment->fresh('trackingEvents'))], 201);
    }

    public function track(Shipment $shipment): JsonResponse
    {
        $added = $this->tracking->syncTracking($shipment);

        if ($shipment->fresh()->shipment_status === Shipment::STATUS_DELIVERED) {
            event(new \App\Events\ShipmentDelivered($shipment->fresh()));
        }

        return response()->json([
            'data' => new ShipmentResource($shipment->fresh('trackingEvents')),
            'new_events' => $added,
        ]);
    }

    public function cancel(Shipment $shipment): JsonResponse
    {
        $shipment->shipment_status = Shipment::STATUS_CANCELLED;
        $shipment->save();

        return response()->json(['data' => new ShipmentResource($shipment->fresh())]);
    }
}
