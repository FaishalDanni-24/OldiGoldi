<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusHistorySeeder extends Seeder
{
    public function run(): void
    {
        // Minimal: only required attributes (order_id, new_status)
        DB::table('order_status_histories')->insert([
            'order_id' => 1,
            'new_status' => 'pending_payment',
        ]);

        // Full: all attributes filled
        DB::table('order_status_histories')->insert([
            'order_id' => 2,
            'user_id' => 1,
            'old_status' => 'pending_payment',
            'new_status' => 'paid',
            'note' => 'Pembayaran diterima.',
            'changed_at' => now(),
        ]);
    }
}
