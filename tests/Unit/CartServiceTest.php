<?php

namespace Tests\Unit;

use App\Models\Carts\Cart;
use App\Models\Carts\CartItem;
use App\Models\Products\Product;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_or_create_cart_returns_same_cart(): void
    {
        $role = Role::factory()->create(['role_name' => 'customer']);
        $user = User::factory()->create(['role_id' => $role->role_id]);

        $service = app(CartService::class);
        $cart1 = $service->getOrCreateCart($user);
        $cart2 = $service->getOrCreateCart($user);

        $this->assertEquals($cart1->cart_id, $cart2->cart_id);
    }

    public function test_add_item_creates_and_increments(): void
    {
        $role = Role::factory()->create(['role_name' => 'customer']);
        $user = User::factory()->create(['role_id' => $role->role_id]);
        $product = Product::factory()->create(['stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->user_id, 'cart_status' => 'active']);

        $service = app(CartService::class);
        $item = $service->addItem($cart, $product->product_id, 2);
        $this->assertEquals(2, $item->quantity);

        $item2 = $service->addItem($cart, $product->product_id, 3);
        $this->assertEquals(5, $item2->quantity);
    }

    public function test_clear_removes_items(): void
    {
        $role = Role::factory()->create(['role_name' => 'customer']);
        $user = User::factory()->create(['role_id' => $role->role_id]);
        $product = Product::factory()->create(['stock' => 10]);
        $cart = Cart::factory()->create(['user_id' => $user->user_id, 'cart_status' => 'active']);
        CartItem::factory()->create(['cart_id' => $cart->cart_id, 'product_id' => $product->product_id]);

        $service = app(CartService::class);
        $service->clear($cart);

        $this->assertEquals(0, $cart->items()->count());
    }
}
