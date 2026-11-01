<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            'category_id' => 1,
            'user_id' => 0,
            'sku' => Str::random(60),
            'product_name' => 'Laptop Asus Vivobook 15',
            'brand' => 'ASUS',
            'model' => 'K513EQ',
            'product_description' => 'Spek:CPU Intel I5, GPU Geforce MX350, 8 GB RAM',
            'condition_grade' => 'fair',
            'condition_notes' => 'Baterai rusak, perlu diganti.',
            'price' => 5000000,
            'stock' => 2,
            'product_status' => 'active',
        ]);
        DB::table('products')->insert([
            'category_id' => 0,
            'user_id' => 0,
            'sku' => Str::random(60),
            'product_name' => 'PalmPilot',
            'condition_grade' => 'for_parts',
            'price' => 250000,
            'stock' => 0,
            'product_status' => 'sold_out',
        ]);
    }
}
