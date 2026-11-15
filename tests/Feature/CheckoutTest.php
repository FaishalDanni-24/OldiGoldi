<?php

namespace Tests\Feature;

use App\Models\Products\Product;
use App\Models\Users\Address;
use App\Models\Users\Role;
use App\Models\Users\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function customer(): User
    {
        $role = Role::factory()->create(['role_name' => 'customer']);
        return User::factory()->create(['role_id' => $role->role_id, 'account_status' => 'active']);
    }

    public function test_checkout_requires_auth(): void
    {
        $this->postJson('/api/v1/checkout', [])->assertStatus(401);
    }

    public function test_checkout_with_empty_cart_fails(): void
    {
        $user = $this->customer();
        $address = Address::factory()->create(['user_id' => $user->user_id]);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/checkout', [
            'address_id' => $address->address_id,
        ])->assertStatus(422);
    }

    public function test_checkout_creates_order_and_payment(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['product_status' => 'active', 'stock' => 5, 'price' => 100000]);
        $address = Address::factory()->create(['user_id' => $user->user_id]);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/cart/items', [
            'product_id' => $product->product_id,
            'quantity' => 2,
        ])->assertStatus(201);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/checkout', [
            'address_id' => $address->address_id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.order_status', 'pending_payment');

        $this->assertDatabaseHas('orders', [
            'customer_id' => $user->user_id,
            'total_amount' => 200000,
        ]);
        $this->assertDatabaseHas('payments', [
            'payment_status' => 'pending',
        ]);
        $this->assertEquals(3, $product->fresh()->stock);
    }

    public function test_customer_can_list_and_view_orders(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['product_status' => 'active', 'stock' => 5, 'price' => 100000]);
        $address = Address::factory()->create(['user_id' => $user->user_id]);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/cart/items', [
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/checkout', [
            'address_id' => $address->address_id,
        ]);

        $this->actingAs($user, 'sanctum')->getJson('/api/v1/orders')
            ->assertStatus(200);

        $orderId = \App\Models\Orders\Order::where('customer_id', $user->user_id)->first()->order_id;
        $this->actingAs($user, 'sanctum')->getJson("/api/v1/orders/{$orderId}")
            ->assertStatus(200);
    }
}
