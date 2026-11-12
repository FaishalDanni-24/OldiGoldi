<?php

namespace App\Http\Controllers\Carts;

use App\Http\Controllers\Controller;
use App\Models\Carts\Cart;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function show(Request $request): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart($request->user());

        return response()->json(['data' => $cart->load('items.product')]);
    }

    public function clear(Request $request): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart($request->user());
        $this->cartService->clear($cart);

        return response()->json(['message' => 'Cart cleared.']);
    }
}
