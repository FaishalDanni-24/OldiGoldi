<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShipmentTrackingEventSeeder extends Seeder
{
    public function run(): void
    {
        // Minimal: only required attributes (shipment_id, event_status)
        DB::table('shipment_tracking_events')->insert([
            'shipment_id' => 1,
            'event_status' => 'pending',
        ]);

        // Full: all attributes filled
        DB::table('shipment_tracking_events')->insert([
            'shipment_id' => 2,
            'external_event_id' => 'EXT-EVT-002',
            'event_code' => 'EVT-002',
            'event_status' => 'in_transit',
            'description' => 'Paket sedang dalam perjalanan.',
            'location' => 'Jakarta',
            'happened_at' => now()->subHours(2),
            'raw_payload' => json_encode(['status' => 'in_transit', 'location' => 'Jakarta']),
        ]);
    }
}
