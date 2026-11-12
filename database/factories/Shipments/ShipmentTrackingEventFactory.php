<?php

namespace Database\Factories\Shipments;

use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentTrackingEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShipmentTrackingEvent>
 */
class ShipmentTrackingEventFactory extends Factory
{
    protected $model = ShipmentTrackingEvent::class;

    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'external_event_id' => fake()->optional()->uuid(),
            'event_code' => fake()->optional()->bothify('EVT-###'),
            'event_status' => fake()->randomElement(['pending', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed']),
            'description' => fake()->optional()->sentence(),
            'location' => fake()->optional()->city(),
            'happened_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'raw_payload' => ['event' => 'test'],
        ];
    }
}
