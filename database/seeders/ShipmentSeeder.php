<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        // Minimal: only required attributes (order_id, tracking_provider)
        DB::table('shipments')->insert([
            'order_id' => 1,
            'tracking_provider' => 'sandbox',
        ]);

        // Full: all attributes filled
        DB::table('shipments')->insert([
            'order_id' => 2,
            'tracking_provider' => 'jne',
            'provider_shipment_id' => 'SHIP-002',
            'courier_code' => 'JNE',
            'courier_name' => 'JNE Express',
            'tracking_number' => 'TRK-000002',
            'shipment_status' => 'in_transit',
            'shipped_at' => now()->subDay(),
            'delivered_at' => null,
            'last_synced_at' => now(),
        ]);
    }
}
