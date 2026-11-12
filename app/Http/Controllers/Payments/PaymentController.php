<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
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

        return response()->json(['data' => $payment->load('order', 'webhookEvents')]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,order_id'],
            'provider' => ['sometimes', 'string', 'max:50'],
        ]);

        $order = Order::findOrFail($validated['order_id']);

        if ($order->customer_id !== $request->user()->user_id) {
            abort(403);
        }

        if ($order->payment && $order->payment->payment_status === Payment::STATUS_PENDING) {
            return response()->json(['data' => $order->payment]);
        }

        $payment = $this->gateway->createTransaction($order, $validated['provider'] ?? 'sandbox');

        return response()->json(['data' => $payment], 201);
    }

    public function cancel(Payment $payment): JsonResponse
    {
        $user = request()->user();
        if (! $user || ($payment->order->customer_id !== $user->user_id && $user->role?->role_name !== 'admin')) {
            abort(403);
        }

        $this->gateway->cancel($payment);

        return response()->json(['data' => $payment->fresh()]);
    }

    public function refund(Request $request, Payment $payment): JsonResponse
    {
        $user = request()->user();
        if (! $user || $user->role?->role_name !== 'admin') {
            abort(403);
        }

        $this->gateway->refund($payment, $request->input('amount'));

        return response()->json(['data' => $payment->fresh()]);
    }
}
