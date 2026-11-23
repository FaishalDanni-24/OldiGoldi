<?php

namespace App\Console\Commands;

use App\Jobs\SyncShipmentTracking;
use App\Models\Shipments\Shipment;
use Illuminate\Console\Command;

class SyncShipmentTrackingCommand extends Command
{
    protected $signature = 'shipments:sync-tracking';

    protected $description = 'Sync tracking status for all in-progress shipments';

    public function handle(): int
    {
        $shipments = Shipment::query()
            ->whereNotIn('shipment_status', [Shipment::STATUS_DELIVERED, Shipment::STATUS_CANCELLED, Shipment::STATUS_FAILED, Shipment::STATUS_RETURNED])
            ->whereNotNull('provider_tracking_id')
            ->get();

        foreach ($shipments as $shipment) {
            SyncShipmentTracking::dispatch($shipment);
        }

        $this->info("Dispatched sync for {$shipments->count()} shipments.");

        return self::SUCCESS;
    }
}
