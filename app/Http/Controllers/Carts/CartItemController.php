<?php

namespace App\Http\Controllers\Carts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Carts\AddToCartRequest;
use App\Http\Requests\Carts\UpdateCartItemRequest;
use App\Http\Resources\Carts\CartItemResource;
use App\Models\Carts\Cart;
use App\Models\Carts\CartItem;
use App\Models\Products\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function store(AddToCartRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $cart = $this->cartService->getOrCreateCart($request->user());
        $product = Product::findOrFail($validated['product_id']);

        if ($product->stock < ($validated['quantity'] ?? 1)) {
            return response()->json(['message' => 'Insufficient stock.'], 422);
        }

        $item = $this->cartService->addItem($cart, $validated['product_id'], $validated['quantity'] ?? 1);

        return response()->json(['data' => new CartItemResource($item->load('product'))], 201);
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): JsonResponse
    {
        if ($cartItem->cart->user_id !== $request->user()->user_id) {
            abort(403);
        }

        $validated = $request->validated();

        if ($cartItem->product && $cartItem->product->stock < $validated['quantity']) {
            return response()->json(['message' => 'Insufficient stock.'], 422);
        }

        $item = $this->cartService->updateItem($cartItem, $validated['quantity']);

        return response()->json(['data' => new CartItemResource($item->fresh('product'))]);
    }

    public function destroy(CartItem $cartItem): JsonResponse
    {
        if ($cartItem->cart->user_id !== request()->user()->user_id) {
            abort(403);
        }

        $this->cartService->removeItem($cartItem);

        return response()->json(['message' => 'Item removed.']);
    }
}
