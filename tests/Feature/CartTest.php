<?php

namespace Tests\Feature;

use App\Models\Carts\Cart;
use App\Models\Carts\CartItem;
use App\Models\Products\Product;
use App\Models\Users\Role;
use App\Models\Users\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private function customer(): User
    {
        $role = Role::factory()->create(['role_name' => 'customer']);
        return User::factory()->create(['role_id' => $role->role_id, 'account_status' => 'active']);
    }

    public function test_guest_cannot_access_cart(): void
    {
        $this->getJson('/api/v1/cart')->assertStatus(401);
    }

    public function test_customer_can_view_empty_cart(): void
    {
        $user = $this->customer();
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/cart');

        $response->assertStatus(200)
            ->assertJsonPath('data.cart_status', 'active');
    }

    public function test_customer_can_add_item_to_cart(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['product_status' => 'active', 'stock' => 5, 'price' => 100000]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/cart/items', [
            'product_id' => $product->product_id,
            'quantity' => 2,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->product_id,
            'quantity' => 2,
        ]);
    }

    public function test_adding_same_product_increments_quantity(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['product_status' => 'active', 'stock' => 10, 'price' => 50000]);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/cart/items', [
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/cart/items', [
            'product_id' => $product->product_id,
            'quantity' => 2,
        ]);

        $cart = Cart::where('user_id', $user->user_id)->first();
        $item = CartItem::where('cart_id', $cart->cart_id)->where('product_id', $product->product_id)->first();
        $this->assertEquals(3, $item->quantity);
    }

    public function test_customer_can_update_cart_item_quantity(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['product_status' => 'active', 'stock' => 10, 'price' => 50000]);
        $add = $this->actingAs($user, 'sanctum')->postJson('/api/v1/cart/items', [
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);
        $itemId = $add->json('data.cart_item_id');

        $response = $this->actingAs($user, 'sanctum')->patchJson("/api/v1/cart/items/{$itemId}", [
            'quantity' => 4,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.quantity', 4);
    }

    public function test_customer_can_remove_cart_item(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['product_status' => 'active', 'stock' => 10, 'price' => 50000]);
        $add = $this->actingAs($user, 'sanctum')->postJson('/api/v1/cart/items', [
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);
        $itemId = $add->json('data.cart_item_id');

        $this->actingAs($user, 'sanctum')->deleteJson("/api/v1/cart/items/{$itemId}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('cart_items', ['cart_item_id' => $itemId]);
    }

    public function test_customer_can_clear_cart(): void
    {
        $user = $this->customer();
        $product = Product::factory()->create(['product_status' => 'active', 'stock' => 10, 'price' => 50000]);
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/cart/items', [
            'product_id' => $product->product_id,
            'quantity' => 1,
        ]);

        $this->actingAs($user, 'sanctum')->deleteJson('/api/v1/cart')
            ->assertStatus(200);

        $cart = Cart::where('user_id', $user->user_id)->first();
        $this->assertEquals(0, $cart->items()->count());
    }
}
