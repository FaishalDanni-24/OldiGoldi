<?php

namespace Database\Factories\Carts;

use App\Models\Carts\Cart;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cart>
 */
class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'cart_status' => fake()->randomElement(['active', 'converted', 'abandoned']),
        ];
    }
}
