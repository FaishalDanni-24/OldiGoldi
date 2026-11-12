<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payments\Payment;
use App\Models\Payments\PaymentWebhookEvent;
use App\Services\PaymentGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookEventController extends Controller
{
    public function __construct(protected PaymentGatewayService $gateway) {}

    public function store(Request $request, string $provider = 'sandbox'): JsonResponse
    {
        $raw = $request->getContent();
        $payload = json_decode($raw, true) ?: $request->all();
        $signature = $request->header('X-Signature') ?? $request->header('X-Webhook-Signature');

        if (! $this->gateway->verifyCallback($payload, $signature)) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $eventId = $payload['event_id'] ?? $payload['id'] ?? null;
        if (! $eventId) {
            return response()->json(['message' => 'Missing event_id.'], 422);
        }

        // idempotency
        $existing = PaymentWebhookEvent::where('provider', $provider)
            ->where('provider_event_id', $eventId)
            ->first();

        if ($existing) {
            return response()->json(['data' => $existing, 'duplicate' => true]);
        }

        $payment = null;
        if (! empty($payload['order_id']) || ! empty($payload['provider_order_id'])) {
            $payment = Payment::where('provider', $provider)
                ->where(function ($q) use ($payload) {
                    if (! empty($payload['provider_order_id'])) {
                        $q->orWhere('provider_order_id', $payload['provider_order_id']);
                    }
                    if (! empty($payload['order_id'])) {
                        $q->orWhere('order_id', $payload['order_id']);
                    }
                })
                ->first();
        }

        $event = PaymentWebhookEvent::create([
            'provider' => $provider,
            'provider_event_id' => $eventId,
            'payment_id' => $payment?->payment_id,
            'event_type' => $payload['event_type'] ?? $payload['type'] ?? 'unknown',
            'payload' => $payload,
            'processing_status' => PaymentWebhookEvent::STATUS_RECEIVED,
            'received_at' => now(),
        ]);

        // process inline (sandbox); in prod dispatch ProcessPaymentWebhook job
        try {
            $status = $payload['status'] ?? $payload['payment_status'] ?? null;
            if ($payment && $status) {
                if (in_array($status, ['paid', 'success', 'settlement'], true)) {
                    $payment->markPaid($payload['transaction_id'] ?? null);
                    $payment->order?->update(['order_status' => \App\Models\Orders\Order::STATUS_PAID]);
                } elseif (in_array($status, ['failed', 'deny', 'cancel'], true)) {
                    $payment->payment_status = Payment::STATUS_FAILED;
                    $payment->save();
                } elseif (in_array($status, ['expired'], true)) {
                    $payment->payment_status = Payment::STATUS_EXPIRED;
                    $payment->save();
                }
            }
            $event->markProcessed();
        } catch (\Throwable $e) {
            $event->markFailed($e->getMessage());
        }

        return response()->json(['data' => $event], 201);
    }
}
