<?php

namespace App\Http\Controllers\Payments;

use App\Events\PaymentSucceeded;
use App\Http\Controllers\Controller;
use App\Http\Resources\Payments\PaymentWebhookEventResource;
use App\Models\Orders\Order;
use App\Models\Payments\Payment;
use App\Models\Payments\PaymentWebhookEvent;
use App\Services\PaymentGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookEventController extends Controller
{
    public function __construct(protected PaymentGatewayService $gateway) {}

    public function store(Request $request, string $provider = 'doku'): JsonResponse
    {
        $raw = $request->getContent();

        // DOKU verification probe when registering the webhook: empty body.
        if (trim($raw) === '') {
            return response()->json(['message' => 'OK'], 200);
        }

        $payload = json_decode($raw, true) ?: $request->all();

        $invoiceNumber = $payload['order']['invoice_number'] ?? $payload['order_id'] ?? null;
        if (! $invoiceNumber) {
            return response()->json(['message' => 'Missing order.invoice_number.'], 422);
        }

        // Idempotency key: DOKU Request-Id header, else body transaction.original_request_id, else event_id.
        $eventId = $request->header('Request-Id')
            ?? ($payload['transaction']['original_request_id'] ?? null)
            ?? ($payload['event_id'] ?? $payload['id'] ?? null);

        if (! $eventId) {
            $eventId = sha1($raw);
        }

        $existing = PaymentWebhookEvent::where('provider', $provider)
            ->where('provider_event_id', $eventId)
            ->first();

        if ($existing) {
            return response()->json(['data' => new PaymentWebhookEventResource($existing), 'duplicate' => true], 200);
        }

        // Resolve payment via order.invoice_number -> Order -> Payment.
        $invoiceNumber = $payload['order']['invoice_number'] ?? $payload['order_id'] ?? null;
        $order = $invoiceNumber ? Order::where('order_number', $invoiceNumber)->first() : null;
        $payment = $order?->payment;

        $event = PaymentWebhookEvent::create([
            'provider' => $provider,
            'provider_event_id' => $eventId,
            'payment_id' => $payment?->payment_id,
            'event_type' => $payload['service']['id'] ?? $payload['channel']['id'] ?? 'payment.notification',
            'payload' => $payload,
            'processing_status' => PaymentWebhookEvent::STATUS_RECEIVED,
            'received_at' => now(),
        ]);

        // Process (sandbox inline; production should dispatch ProcessPaymentWebhook job).
        try {
            $status = $payload['transaction']['status'] ?? $payload['status'] ?? $payload['payment_status'] ?? null;
            if ($payment && $status) {
                if (in_array(strtoupper((string) $status), ['SUCCESS', 'PAID', 'SETTLEMENT'], true)) {
                    $payment->markPaid($payload['transaction']['original_request_id'] ?? null);
                    $payment->order?->update(['order_status' => Order::STATUS_PAID]);
                    event(new PaymentSucceeded($payment->fresh()));
                } elseif (in_array(strtoupper((string) $status), ['FAILED', 'DENY', 'CANCEL'], true)) {
                    $payment->payment_status = Payment::STATUS_FAILED;
                    $payment->save();
                } elseif (strtoupper((string) $status) === 'EXPIRED') {
                    $payment->payment_status = Payment::STATUS_EXPIRED;
                    $payment->save();
                } elseif (strtoupper((string) $status) === 'REFUNDED') {
                    $payment->payment_status = Payment::STATUS_REFUNDED;
                    $payment->save();
                }
            }
            $event->markProcessed();
        } catch (\Throwable $e) {
            $event->markFailed($e->getMessage());
        }

        return response()->json(['data' => new PaymentWebhookEventResource($event)], 201);
    }
}
