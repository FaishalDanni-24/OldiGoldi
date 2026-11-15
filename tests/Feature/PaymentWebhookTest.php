<?php

namespace Tests\Feature;

use App\Models\Orders\Order;
use App\Models\Payments\Payment;
use App\Models\Products\Product;
use App\Models\Users\Address;
use App\Models\Users\Role;
use App\Models\Users\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrderWithPayment(User $user): array
    {
        $product = Product::factory()->create(['product_status' => 'active', 'stock' => 5, 'price' => 100000]);
        $address = Address::factory()->create(['user_id' => $user->user_id]);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/cart/items', [
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/checkout', [
            'address_id' => $address->address_id,
        ]);

        $order = Order::where('customer_id', $user->user_id)->first();
        $payment = Payment::where('order_id', $order->order_id)->first();

        return [$order, $payment];
    }

    private function customer(): User
    {
        $role = Role::factory()->create(['role_name' => 'customer']);
        return User::factory()->create(['role_id' => $role->role_id, 'account_status' => 'active']);
    }

    public function test_webhook_rejects_missing_event_id(): void
    {
        $this->postJson('/api/v1/payments/webhook/sandbox', ['status' => 'paid'])
            ->assertStatus(422);
    }

    public function test_webhook_marks_payment_paid(): void
    {
        $user = $this->customer();
        [$order, $payment] = $this->makeOrderWithPayment($user);

        $response = $this->postJson('/api/v1/payments/webhook/sandbox', [
            'event_id' => 'EVT-001',
            'order_id' => $order->order_id,
            'status' => 'paid',
            'transaction_id' => 'TRX-001',
        ]);

        $response->assertStatus(201);
        $this->assertEquals('paid', $payment->fresh()->payment_status);
        $this->assertEquals('paid', $order->fresh()->order_status);
    }

    public function test_webhook_is_idempotent(): void
    {
        $user = $this->customer();
        [$order, $payment] = $this->makeOrderWithPayment($user);

        $this->postJson('/api/v1/payments/webhook/sandbox', [
            'event_id' => 'EVT-DUP',
            'order_id' => $order->order_id,
            'status' => 'paid',
        ])->assertStatus(201);

        $second = $this->postJson('/api/v1/payments/webhook/sandbox', [
            'event_id' => 'EVT-DUP',
            'order_id' => $order->order_id,
            'status' => 'paid',
        ]);

        $second->assertStatus(200)
            ->assertJsonPath('duplicate', true);
    }

    public function test_webhook_marks_payment_failed(): void
    {
        $user = $this->customer();
        [$order, $payment] = $this->makeOrderWithPayment($user);

        $this->postJson('/api/v1/payments/webhook/sandbox', [
            'event_id' => 'EVT-FAIL',
            'order_id' => $order->order_id,
            'status' => 'failed',
        ])->assertStatus(201);

        $this->assertEquals('failed', $payment->fresh()->payment_status);
    }
}
