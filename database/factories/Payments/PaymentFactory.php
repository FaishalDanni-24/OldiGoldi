<?php

namespace Database\Factories\Payments;

use App\Models\Orders\Order;
use App\Models\Payments\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'provider' => fake()->randomElement(['sandbox', 'midtrans', 'xendit']),
            'provider_order_id' => fake()->optional()->uuid(),
            'provider_transaction_id' => fake()->optional()->uuid(),
            'payment_method' => fake()->optional()->randomElement(['bank_transfer', 'credit_card', 'ewallet', 'qris']),
            'amount' => fake()->randomFloat(2, 100000, 5000000),
            'payment_status' => fake()->randomElement(['pending', 'paid', 'failed', 'expired', 'cancelled', 'refunded']),
            'payment_url' => fake()->optional()->url(),
            'expires_at' => fake()->optional()->dateTimeBetween('now', '+2 days'),
            'paid_at' => fake()->optional()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
