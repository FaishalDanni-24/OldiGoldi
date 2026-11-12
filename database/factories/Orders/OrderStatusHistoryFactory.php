<?php

namespace Database\Factories\Orders;

use App\Models\Orders\Order;
use App\Models\Orders\OrderStatusHistory;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderStatusHistory>
 */
class OrderStatusHistoryFactory extends Factory
{
    protected $model = OrderStatusHistory::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'old_status' => fake()->optional()->randomElement(['pending_payment', 'paid', 'processing']),
            'new_status' => fake()->randomElement(['pending_payment', 'paid', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'refunded']),
            'note' => fake()->optional()->sentence(),
            'changed_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
