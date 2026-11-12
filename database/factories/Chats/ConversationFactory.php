<?php

namespace Database\Factories\Chats;

use App\Models\Chats\Conversation;
use App\Models\Orders\Order;
use App\Models\Products\Product;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        return [
            'customer_id' => User::factory(),
            'admin_id' => User::factory(),
            'order_id' => fake()->optional()->randomElement([null, Order::factory()]),
            'product_id' => fake()->optional()->randomElement([null, Product::factory()]),
            'conversation_status' => fake()->randomElement(['open', 'closed']),
        ];
    }
}
