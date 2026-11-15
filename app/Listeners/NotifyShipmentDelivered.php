<?php

namespace App\Listeners;

use App\Events\ShipmentDelivered;
use App\Notifications\ShipmentDeliveredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyShipmentDelivered implements ShouldQueue
{
    public function handle(ShipmentDelivered $event): void
    {
        $user = $event->shipment->order?->customer;
        if ($user) {
            $user->notify(new ShipmentDeliveredNotification($event->shipment));
        }
    }
}
