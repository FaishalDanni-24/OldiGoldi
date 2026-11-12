<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            AddressSeeder::class,
            ProductSeeder::class,
            ProductImageSeeder::class,
            CartSeeder::class,
            CartItemSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            OrderStatusHistorySeeder::class,
            PaymentSeeder::class,
            PaymentWebhookEventSeeder::class,
            ShipmentSeeder::class,
            ShipmentTrackingEventSeeder::class,
            ConversationSeeder::class,
            ChatMessageSeeder::class,
        ]);
    }
}
