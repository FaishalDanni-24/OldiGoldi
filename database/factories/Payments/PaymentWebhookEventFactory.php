<?php

namespace Database\Factories\Payments;

use App\Models\Payments\Payment;
use App\Models\Payments\PaymentWebhookEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentWebhookEvent>
 */
class PaymentWebhookEventFactory extends Factory
{
    protected $model = PaymentWebhookEvent::class;

    public function definition(): array
    {
        return [
            'provider' => fake()->randomElement(['sandbox', 'midtrans', 'xendit']),
            'provider_event_id' => fake()->uuid(),
            'payment_id' => Payment::factory(),
            'event_type' => fake()->randomElement(['payment.success', 'payment.failed', 'payment.expired']),
            'payload' => ['event' => 'test', 'amount' => fake()->randomFloat(2, 1000, 100000)],
            'processing_status' => fake()->randomElement(['received', 'processed', 'failed']),
            'received_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'processed_at' => fake()->optional()->dateTimeBetween('-1 week', 'now'),
            'error_message' => fake()->optional()->sentence(),
        ];
    }
}
