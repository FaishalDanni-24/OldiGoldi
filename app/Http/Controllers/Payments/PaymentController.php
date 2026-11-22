<?php

namespace App\Http\Controllers\Payments;

use App\Events\PaymentSucceeded;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\CreatePaymentRequest;
use App\Http\Requests\Payments\RefundPaymentRequest;
use App\Http\Resources\Payments\PaymentResource;
use App\Models\Orders\Order;
use App\Models\Payments\Payment;
use App\Services\PaymentGatewayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(protected PaymentGatewayService $gateway) {}

    public function show(Payment $payment): JsonResponse
    {
        $user = request()->user();
        if (! $user || ($payment->order->customer_id !== $user->user_id && $user->role?->role_name !== 'admin')) {
            abort(403);
        }

        return response()->json(['data' => new PaymentResource($payment->load('order', 'webhookEvents'))]);
    }

    public function store(CreatePaymentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $order = Order::findOrFail($validated['order_id']);

        if ($order->customer_id !== $request->user()->user_id) {
            abort(403);
        }

        if ($order->payment && $order->payment->payment_status === Payment::STATUS_PENDING) {
            return response()->json(['data' => new PaymentResource($order->payment)]);
        }

        try {
            $payment = $this->gateway->createTransaction($order, $validated['provider'] ?? 'doku');
        } catch (\App\Exceptions\ExternalServiceException $e) {
            return response()->json(['message' => $e->getMessage()], 502);
        }

        return response()->json(['data' => new PaymentResource($payment)], 201);
    }

    public function cancel(Payment $payment): JsonResponse
    {
        $user = request()->user();
        if (! $user || ($payment->order->customer_id !== $user->user_id && $user->role?->role_name !== 'admin')) {
            abort(403);
        }

        $this->gateway->cancel($payment);

        return response()->json(['data' => new PaymentResource($payment->fresh())]);
    }

    public function refund(RefundPaymentRequest $request, Payment $payment): JsonResponse
    {
        $user = request()->user();
        if (! $user || $user->role?->role_name !== 'admin') {
            abort(403);
        }

        $this->gateway->refund($payment, $request->input('amount'));

        return response()->json(['data' => new PaymentResource($payment->fresh())]);
    }
}
