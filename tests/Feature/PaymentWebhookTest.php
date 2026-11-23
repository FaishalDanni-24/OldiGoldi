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

    private function customer(): User
    {
        $role = Role::factory()->create(['role_name' => 'customer']);
        return User::factory()->create(['role_id' => $role->role_id, 'account_status' => 'active']);
    }

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

    public function test_webhook_rejects_missing_invoice_number(): void
    {
        $this->postJson('/api/v1/payments/webhook/sandbox', ['transaction' => ['status' => 'SUCCESS']])
            ->assertStatus(422);
    }

    public function test_webhook_marks_payment_paid(): void
    {
        $user = $this->customer();
        [$order, $payment] = $this->makeOrderWithPayment($user);

        $response = $this->postJson('/api/v1/payments/webhook/sandbox', [
            'order' => ['invoice_number' => $order->order_number, 'amount' => 100000],
            'transaction' => ['status' => 'SUCCESS', 'original_request_id' => 'EVT-001'],
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
            'order' => ['invoice_number' => $order->order_number, 'amount' => 100000],
            'transaction' => ['status' => 'SUCCESS', 'original_request_id' => 'EVT-DUP'],
        ])->assertStatus(201);

        $second = $this->postJson('/api/v1/payments/webhook/sandbox', [
            'order' => ['invoice_number' => $order->order_number, 'amount' => 100000],
            'transaction' => ['status' => 'SUCCESS', 'original_request_id' => 'EVT-DUP'],
        ]);

        $second->assertStatus(200)
            ->assertJsonPath('duplicate', true);
    }

    public function test_webhook_marks_payment_failed(): void
    {
        $user = $this->customer();
        [$order, $payment] = $this->makeOrderWithPayment($user);

        $this->postJson('/api/v1/payments/webhook/sandbox', [
            'order' => ['invoice_number' => $order->order_number, 'amount' => 100000],
            'transaction' => ['status' => 'FAILED', 'original_request_id' => 'EVT-FAIL'],
        ])->assertStatus(201);

        $this->assertEquals('failed', $payment->fresh()->payment_status);
    }

    public function test_webhook_handles_verification_probe(): void
    {
        $this->post('/api/v1/payments/webhook/sandbox', [], ['Content-Type' => 'application/json'])
            ->assertStatus(200);
    }
}
