<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentWebhookEventSeeder extends Seeder
{
    public function run(): void
    {
        // Minimal: only required attributes (provider, provider_event_id, event_type, payload)
        DB::table('payment_webhook_events')->insert([
            'provider' => 'sandbox',
            'provider_event_id' => 'EVT-MIN-001',
            'event_type' => 'payment.pending',
            'payload' => json_encode(['status' => 'pending']),
        ]);

        // Full: all attributes filled
        DB::table('payment_webhook_events')->insert([
            'provider' => 'sandbox',
            'provider_event_id' => 'EVT-FULL-002',
            'payment_id' => 2,
            'event_type' => 'payment.success',
            'payload' => json_encode(['status' => 'paid', 'amount' => 260000]),
            'processing_status' => 'processed',
            'received_at' => now(),
            'processed_at' => now(),
            'error_message' => null,
        ]);
    }
}
