<?php

namespace App\Services;

use App\Models\Carts\Cart;
use App\Models\Carts\CartItem;
use App\Models\Products\Product;
use App\Models\Users\User;

class CartService
{
    public function getOrCreateCart(User $user): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => $user->user_id, 'cart_status' => Cart::STATUS_ACTIVE],
            ['user_id' => $user->user_id, 'cart_status' => Cart::STATUS_ACTIVE]
        );
    }

    public function addItem(Cart $cart, int $productId, int $quantity = 1): CartItem
    {
        $product = Product::findOrFail($productId);

        $item = CartItem::where('cart_id', $cart->cart_id)
            ->where('product_id', $productId)
            ->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->save();
            return $item;
        }

        return CartItem::create([
            'cart_id' => $cart->cart_id,
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }

    public function updateItem(CartItem $item, int $quantity): CartItem
    {
        $item->quantity = $quantity;
        $item->save();
        return $item;
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }
}
