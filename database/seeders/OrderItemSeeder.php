<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        // Minimal: only required attributes
        DB::table('order_items')->insert([
            'order_id' => 1,
            'product_name_snapshot' => 'Laptop Asus Vivobook 15',
            'unit_price' => 5000000,
            'quantity' => 1,
        ]);

        // Full: all attributes filled
        DB::table('order_items')->insert([
            'order_id' => 2,
            'product_id' => 2,
            'product_name_snapshot' => 'PalmPilot',
            'sku_snapshot' => 'SKU-PALMPILOT-001',
            'unit_price' => 250000,
            'quantity' => 1,
            'line_total' => 250000,
        ]);
    }
}
