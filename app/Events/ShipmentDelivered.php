<?php

namespace App\Events;

use App\Models\Shipments\Shipment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShipmentDelivered
{
    use Dispatchable, SerializesModels;

    public function __construct(public Shipment $shipment) {}
}
