<?php

namespace Database\Factories\Orders;

use App\Models\Orders\Order;
use App\Models\Users\Address;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 100000, 5000000);
        $shipping = fake()->randomFloat(2, 0, 50000);
        $discount = fake()->randomFloat(2, 0, 20000);

        return [
            'order_number' => 'OG-' . now()->format('Ymd') . '-' . fake()->unique()->numerify('######'),
            'customer_id' => User::factory(),
            'address_id' => Address::factory(),
            'order_status' => fake()->randomElement(['pending_payment', 'paid', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'refunded']),
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping,
            'discount_amount' => $discount,
            'total_amount' => $subtotal + $shipping - $discount,
            'recipient_name' => fake()->name(),
            'recipient_phone' => fake()->phoneNumber(),
            'shipping_address_line' => fake()->streetAddress(),
            'shipping_district' => fake()->optional()->citySuffix(),
            'shipping_city' => fake()->city(),
            'shipping_province' => fake()->optional()->state(),
            'shipping_postal_code' => fake()->postcode(),
            'customer_note' => fake()->optional()->sentence(),
        ];
    }
}
