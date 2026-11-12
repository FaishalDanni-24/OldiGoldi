<?php

namespace App\Services;

use App\Models\Carts\Cart;
use App\Models\Orders\Order;
use App\Models\Orders\OrderItem;
use App\Models\Payments\Payment;
use Illuminate\Support\Facades\DB;

/**
 * Handles checkout: creates Order + OrderItems from Cart, decrements stock,
 * and creates a pending Payment via PaymentGatewayService.
 */
class CheckoutService
{
    public function __construct(protected PaymentGatewayService $gateway) {}

    public function checkout(Cart $cart, int $addressId, array $shippingSnapshot = [], ?string $customerNote = null): Order
    {
        return DB::transaction(function () use ($cart, $addressId, $shippingSnapshot, $customerNote) {
            $cart->load('items.product');

            $subtotal = 0;
            foreach ($cart->items as $item) {
                $subtotal += (float) $item->product->price * $item->quantity;
            }

            $shippingCost = (float) ($shippingSnapshot['shipping_cost'] ?? 0);
            $discount = (float) ($shippingSnapshot['discount_amount'] ?? 0);
            $total = $subtotal + $shippingCost - $discount;

            $address = \App\Models\Users\Address::findOrFail($addressId);

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $cart->user_id,
                'address_id' => $addressId,
                'order_status' => Order::STATUS_PENDING_PAYMENT,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'recipient_name' => $shippingSnapshot['recipient_name'] ?? $address->recipient_name,
                'recipient_phone' => $shippingSnapshot['recipient_phone'] ?? $address->recipient_phone,
                'shipping_address_line' => $shippingSnapshot['shipping_address_line'] ?? $address->address_line,
                'shipping_district' => $shippingSnapshot['shipping_district'] ?? $address->district,
                'shipping_city' => $shippingSnapshot['shipping_city'] ?? $address->city,
                'shipping_province' => $shippingSnapshot['shipping_province'] ?? $address->province,
                'shipping_postal_code' => $shippingSnapshot['shipping_postal_code'] ?? $address->postal_code,
                'customer_note' => $customerNote,
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product;
                // lock stock
                $product->decrement('stock', $item->quantity);

                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $product->product_id,
                    'product_name_snapshot' => $product->product_name,
                    'sku_snapshot' => $product->sku,
                    'unit_price' => $product->price,
                    'quantity' => $item->quantity,
                ]);
            }

            // convert cart
            $cart->cart_status = Cart::STATUS_CONVERTED;
            $cart->save();

            // create payment
            $this->gateway->createTransaction($order);

            return $order->fresh(['items', 'payment']);
        });
    }

    protected function generateOrderNumber(): string
    {
        return 'OG-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }
}
