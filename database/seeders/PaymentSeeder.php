<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Minimal: only required attributes (order_id, provider, amount)
        DB::table('payments')->insert([
            'order_id' => 1,
            'provider' => 'sandbox',
            'amount' => 5000000,
        ]);

        // Full: all attributes filled
        DB::table('payments')->insert([
            'order_id' => 2,
            'provider' => 'sandbox',
            'provider_order_id' => 'SBX-ORDER-002',
            'provider_transaction_id' => 'SBX-TRX-002',
            'payment_method' => 'qris',
            'amount' => 260000,
            'payment_status' => 'paid',
            'payment_url' => 'https://sandbox.pay/checkout/002',
            'expires_at' => now()->addHour(),
            'paid_at' => now(),
        ]);
    }
}
