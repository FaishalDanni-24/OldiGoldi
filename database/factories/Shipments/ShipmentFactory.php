<?php

namespace Database\Factories\Shipments;

use App\Models\Orders\Order;
use App\Models\Shipments\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shipment>
 */
class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'tracking_provider' => fake()->randomElement(['sandbox', 'jne', 'jnt', 'sicepat']),
            'provider_shipment_id' => fake()->optional()->uuid(),
            'courier_code' => fake()->optional()->randomElement(['JNE', 'JNT', 'SICEPAT']),
            'courier_name' => fake()->optional()->company(),
            'tracking_number' => fake()->optional()->bothify('TRK-########'),
            'shipment_status' => fake()->randomElement(['pending', 'label_created', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned', 'cancelled', 'unknown']),
            'shipped_at' => fake()->optional()->dateTimeBetween('-1 week', 'now'),
            'delivered_at' => fake()->optional()->dateTimeBetween('-1 week', 'now'),
            'last_synced_at' => fake()->optional()->dateTimeBetween('-1 day', 'now'),
        ];
    }
}
