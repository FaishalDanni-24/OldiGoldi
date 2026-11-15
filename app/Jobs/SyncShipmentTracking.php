<?php

namespace App\Jobs;

use App\Models\Shipments\Shipment;
use App\Services\TrackingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncShipmentTracking implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Shipment $shipment) {}

    public function handle(TrackingService $tracking): void
    {
        $tracking->syncTracking($this->shipment);
    }
}
