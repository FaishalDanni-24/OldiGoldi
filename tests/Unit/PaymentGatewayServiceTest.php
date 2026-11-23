<?php

namespace Tests\Unit;

use App\Models\Orders\Order;
use App\Models\Payments\Payment;
use App\Models\Products\Product;
use App\Models\Users\Address;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Services\DokuSignature;
use App\Services\PaymentGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentGatewayServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(): Order
    {
        $role = Role::factory()->create(['role_name' => 'customer']);
        $user = User::factory()->create(['role_id' => $role->role_id, 'account_status' => 'active']);
        $product = Product::factory()->create(['product_status' => 'active', 'stock' => 5, 'price' => 100000]);
        $address = Address::factory()->create(['user_id' => $user->user_id]);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/cart/items', [
            'product_id' => $product->product_id,
            'quantity' => 2,
        ]);
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/checkout', [
            'address_id' => $address->address_id,
        ]);

        return Order::where('customer_id', $user->user_id)->first();
    }

    public function test_create_transaction_calls_doku_and_parses_response(): void
    {
        config([
            'services.payment_gateway.client_id' => 'CLIENT-123',
            'services.payment_gateway.secret_key' => 'SECRET-KEY',
            'services.payment_gateway.base_url' => 'https://api-sandbox.doku.com',
            'services.payment_gateway.currency' => 'IDR',
            'services.payment_gateway.expiry_minutes' => 60,
            'services.payment_gateway.notification_url' => 'https://example.com/webhook',
            'services.payment_gateway.return_url' => 'https://example.com/return',
        ]);

        Http::fake([
            'api-sandbox.doku.com/checkout/v1/payment' => Http::response([
                'response' => [
                    'order' => ['invoice_number' => 'OG-TEST', 'amount' => 200000, 'session_id' => 'SESSION-1'],
                    'payment' => [
                        'url' => 'https://sandbox.doku.com/checkout/abc',
                        'token_id' => 'TOKEN-XYZ',
                        'expired_date' => now()->addHour()->setTimezone('Asia/Jakarta')->format('YmdHis'),
                    ],
                ],
            ], 200),
        ]);

        $order = $this->makeOrder();
        $payment = \App\Models\Payments\Payment::where('order_id', $order->order_id)->first();

        $this->assertNotNull($payment);
        $this->assertEquals('https://sandbox.doku.com/checkout/abc', $payment->payment_url);
        $this->assertEquals('TOKEN-XYZ', $payment->provider_transaction_id);
        $this->assertEquals($order->order_number, $payment->provider_order_id);

        // Verify request headers & signature.
        Http::assertSent(function ($request) use ($order) {
            $this->assertTrue($request->hasHeader('Client-Id'));
            $this->assertContains('CLIENT-123', (array) $request->header('Client-Id'));
            $this->assertTrue($request->hasHeader('Request-Id'));
            $this->assertTrue($request->hasHeader('Request-Timestamp'));
            $this->assertTrue($request->hasHeader('Signature'));
            $this->assertStringStartsWith('HMACSHA256=', implode('', (array) $request->header('Signature')));

            $body = $request->data();
            $this->assertEquals((int) round((float) $order->total_amount), $body['order']['amount']);
            $this->assertEquals($order->order_number, $body['order']['invoice_number']);

            // Verify signature validity.
            $sig = DokuSignature::sign(
                'CLIENT-123',
                implode('', (array) $request->header('Request-Id')),
                implode('', (array) $request->header('Request-Timestamp')),
                '/checkout/v1/payment',
                json_encode($body),
                'SECRET-KEY'
            );
            $this->assertEquals($sig, implode('', (array) $request->header('Signature')));

            return true;
        });
    }

    public function test_create_transaction_without_credentials_keeps_pending(): void
    {
        config([
            'services.payment_gateway.client_id' => null,
            'services.payment_gateway.secret_key' => null,
            'services.payment_gateway.base_url' => null,
        ]);

        $order = $this->makeOrder();
        $service = app(PaymentGatewayService::class);
        $payment = $service->createTransaction($order, 'doku');

        $this->assertNotNull($payment);
        $this->assertEquals(Payment::STATUS_PENDING, $payment->payment_status);
        $this->assertNull($payment->payment_url);
        Http::assertNothingSent();
    }
}
