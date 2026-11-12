<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        // Minimal: only required attributes (customer_id, admin_id)
        DB::table('conversations')->insert([
            'customer_id' => 2,
            'admin_id' => 1,
        ]);

        // Full: all attributes filled
        DB::table('conversations')->insert([
            'customer_id' => 2,
            'admin_id' => 1,
            'order_id' => 2,
            'product_id' => 1,
            'conversation_status' => 'open',
        ]);
    }
}
