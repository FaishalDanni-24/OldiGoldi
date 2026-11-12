<?php

namespace App\Services;

use App\Models\Orders\Order;
use App\Models\Orders\OrderStatusHistory;
use App\Models\Users\User;

class OrderStatusService
{
    public function transition(Order $order, string $newStatus, ?User $actor = null, ?string $note = null): Order
    {
        $old = $order->order_status;
        $order->order_status = $newStatus;
        $order->save();

        OrderStatusHistory::create([
            'order_id' => $order->order_id,
            'user_id' => $actor?->user_id,
            'old_status' => $old,
            'new_status' => $newStatus,
            'note' => $note,
        ]);

        return $order->fresh();
    }
}
