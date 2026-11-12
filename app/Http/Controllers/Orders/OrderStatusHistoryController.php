<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use Illuminate\Http\JsonResponse;

class OrderStatusHistoryController extends Controller
{
    public function index(Order $order): JsonResponse
    {
        $user = request()->user();
        if (! $user || ($order->customer_id !== $user->user_id && $user->role?->role_name !== 'admin')) {
            abort(403);
        }

        return response()->json(['data' => $order->statusHistories()->orderBy('changed_at')->get()]);
    }
}
