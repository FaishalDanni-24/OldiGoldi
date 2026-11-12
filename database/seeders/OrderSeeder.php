<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Minimal: only required attributes (no defaults)
        DB::table('orders')->insert([
            'order_number' => 'OG-20261112-000001',
            'customer_id' => 2,
            'subtotal' => 5000000,
            'total_amount' => 5000000,
            'recipient_name' => 'Big Jack',
            'recipient_phone' => '08123456789',
            'shipping_address_line' => 'Jl. Kesehatan XI No.13',
            'shipping_city' => 'Jakarta Pusat',
        ]);

        // Full: all attributes filled
        DB::table('orders')->insert([
            'order_number' => 'OG-20261112-000002',
            'customer_id' => 2,
            'address_id' => 2,
            'order_status' => 'paid',
            'subtotal' => 250000,
            'shipping_cost' => 15000,
            'discount_amount' => 5000,
            'total_amount' => 260000,
            'recipient_name' => 'Little Jack',
            'recipient_phone' => '08123456789',
            'shipping_address_line' => 'Jl. Puri Kresna No.31',
            'shipping_district' => 'Gerogol',
            'shipping_city' => 'Cilegon',
            'shipping_province' => 'Banten',
            'shipping_postal_code' => '42436',
            'customer_note' => 'Tolong bungkus rapi.',
        ]);
    }
}
