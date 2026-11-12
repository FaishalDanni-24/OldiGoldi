<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        // Minimal: only required attribute (user_id; cart_status defaults to 'active')
        DB::table('carts')->insert([
            'user_id' => 2,
        ]);

        // Full: all attributes filled
        DB::table('carts')->insert([
            'user_id' => 2,
            'cart_status' => 'converted',
        ]);
    }
}
