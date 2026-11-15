<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Models\Orders\OrderStatusHistory;

class RecordOrderStatusHistory
{
    public function handle(OrderStatusChanged $event): void
    {
        OrderStatusHistory::create([
            'order_id' => $event->order->order_id,
            'user_id' => null,
            'old_status' => $event->oldStatus,
            'new_status' => $event->newStatus,
            'note' => 'Status changed via event.',
        ]);
    }
}
