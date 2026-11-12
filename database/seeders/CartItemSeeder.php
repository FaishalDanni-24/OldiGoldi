<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartItemSeeder extends Seeder
{
    public function run(): void
    {
        // Minimal: only required attributes (cart_id, product_id; quantity defaults to 1)
        DB::table('cart_items')->insert([
            'cart_id' => 1,
            'product_id' => 1,
        ]);

        // Full: all attributes filled
        DB::table('cart_items')->insert([
            'cart_id' => 2,
            'product_id' => 2,
            'quantity' => 3,
        ]);
    }
}
