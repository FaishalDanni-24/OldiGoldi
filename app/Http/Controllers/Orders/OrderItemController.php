<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use Illuminate\Http\JsonResponse;

class OrderItemController extends Controller
{
    public function index(Order $order): JsonResponse
    {
        $this->authorizeOwner($order, request()->user());

        return response()->json(['data' => $order->items]);
    }

    protected function authorizeOwner(Order $order, ?\App\Models\Users\User $user): void
    {
        if (! $user || ($order->customer_id !== $user->user_id && $user->role?->role_name !== 'admin')) {
            abort(403);
        }
    }
}
