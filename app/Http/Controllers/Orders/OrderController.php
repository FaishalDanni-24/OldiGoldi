<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use App\Services\CheckoutService;
use App\Services\OrderStatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected CheckoutService $checkout,
        protected OrderStatusService $statusService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->where('customer_id', $request->user()->user_id)
            ->with(['items', 'payment', 'shipments'])
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json($orders);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorizeOwner($order, request()->user());

        return response()->json(['data' => $order->load(['items', 'payment', 'shipments.trackingEvents', 'statusHistories'])]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'address_id' => ['required', 'integer', 'exists:addresses,address_id'],
            'shipping_cost' => ['sometimes', 'numeric', 'min:0'],
            'discount_amount' => ['sometimes', 'numeric', 'min:0'],
            'recipient_name' => ['sometimes', 'string', 'max:120'],
            'recipient_phone' => ['sometimes', 'string', 'max:30'],
            'shipping_address_line' => ['sometimes', 'string', 'max:500'],
            'shipping_district' => ['sometimes', 'nullable', 'string', 'max:100'],
            'shipping_city' => ['sometimes', 'string', 'max:100'],
            'shipping_province' => ['sometimes', 'nullable', 'string', 'max:100'],
            'shipping_postal_code' => ['sometimes', 'nullable', 'string', 'max:15'],
            'customer_note' => ['sometimes', 'nullable', 'string'],
        ]);

        $user = $request->user();
        $cart = $user->cart;

        if (! $cart || $cart->items()->count() === 0) {
            return response()->json(['message' => 'Cart is empty.'], 422);
        }

        $snapshot = array_intersect_key($validated, array_flip([
            'shipping_cost', 'discount_amount', 'recipient_name', 'recipient_phone',
            'shipping_address_line', 'shipping_district', 'shipping_city',
            'shipping_province', 'shipping_postal_code',
        ]));

        $order = $this->checkout->checkout($cart, $validated['address_id'], $snapshot, $validated['customer_note'] ?? null);

        return response()->json(['data' => $order], 201);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOwner($order, $request->user());

        if (! in_array($order->order_status, [Order::STATUS_PENDING_PAYMENT, Order::STATUS_PAID], true)) {
            return response()->json(['message' => 'Order cannot be cancelled in current status.'], 422);
        }

        $order = $this->statusService->transition($order, Order::STATUS_CANCELLED, $request->user(), $request->input('note'));

        return response()->json(['data' => $order]);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending_payment,paid,processing,shipped,delivered,completed,cancelled,refunded'],
            'note' => ['sometimes', 'nullable', 'string'],
        ]);

        $order = $this->statusService->transition($order, $validated['status'], $request->user(), $validated['note'] ?? null);

        return response()->json(['data' => $order]);
    }

    protected function authorizeOwner(Order $order, ?\App\Models\Users\User $user): void
    {
        if (! $user || ($order->customer_id !== $user->user_id && ! $this->isAdmin($user))) {
            abort(403, 'Forbidden.');
        }
    }

    protected function isAdmin(\App\Models\Users\User $user): bool
    {
        return $user->role?->role_name === 'admin';
    }
}
