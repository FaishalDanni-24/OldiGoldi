<?php

namespace Tests\Unit;

use App\Models\Orders\Order;
use App\Models\Shipments\Shipment;
use App\Models\Users\Address;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Services\TrackingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TrackingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_label_calls_biteship_and_parses_response(): void
    {
        config([
            'services.tracking.api_key' => 'BITESHIP-KEY',
            'services.tracking.base_url' => 'https://api.biteship.com',
            'services.tracking.origin.contact_name' => 'Warehouse',
            'services.tracking.origin.contact_phone' => '08123456789',
            'services.tracking.origin.address' => 'Jl. Warehouse',
            'services.tracking.origin.postal_code' => '12345',
            'services.tracking.default_weight' => 200,
            'services.tracking.default_length' => 10,
            'services.tracking.default_width' => 10,
            'services.tracking.default_height' => 10,
        ]);

        Http::fake([
            'api.biteship.com/v1/orders' => Http::response([
                'success' => true,
                'id' => 'BS-ORDER-1',
                'courier' => [
                    'tracking_id' => 'BS-TRACK-1',
                    'waybill_id' => 'WB-123',
                    'company' => 'jne',
                    'name' => 'JNE',
                ],
                'status' => 'confirmed',
            ], 200),
        ]);

        $role = Role::factory()->create(['role_name' => 'customer']);
        $user = User::factory()->create(['role_id' => $role->role_id]);
        $order = Order::factory()->create(['customer_id' => $user->user_id, 'recipient_name' => 'Buyer', 'recipient_phone' => '08111', 'shipping_address_line' => 'Jl. Buyer', 'shipping_postal_code' => '54321', 'shipping_city' => 'Jakarta']);
        \App\Models\Orders\OrderItem::factory()->create(['order_id' => $order->order_id, 'product_id' => null, 'product_name_snapshot' => 'Test Item', 'unit_price' => 100000, 'quantity' => 1]);
        $shipment = Shipment::factory()->create(['order_id' => $order->order_id, 'courier_code' => 'jne', 'shipment_status' => 'pending']);

        $service = app(TrackingService::class);
        $result = $service->createLabel($shipment, ['courier_type' => 'reg', 'delivery_type' => 'now']);

        $this->assertTrue($result);
        $fresh = $shipment->fresh();
        $this->assertEquals('BS-ORDER-1', $fresh->provider_shipment_id);
        $this->assertEquals('BS-TRACK-1', $fresh->provider_tracking_id);
        $this->assertEquals('WB-123', $fresh->tracking_number);
        $this->assertEquals('jne', $fresh->courier_code);
        $this->assertEquals(Shipment::STATUS_LABEL_CREATED, $fresh->shipment_status);

        Http::assertSent(function ($request) {
            $this->assertContains('BITESHIP-KEY', (array) $request->header('Authorization'));
            $body = $request->data();
            $this->assertEquals('jne', $body['courier_company']);
            $this->assertEquals('reg', $body['courier_type']);
            $this->assertArrayHasKey('origin_contact_name', $body);
            $this->assertArrayHasKey('destination_contact_name', $body);
            $this->assertArrayHasKey('items', $body);
            return true;
        });
    }

    public function test_create_label_without_api_key_falls_back(): void
    {
        config([
            'services.tracking.api_key' => null,
            'services.tracking.base_url' => null,
        ]);

        $role = Role::factory()->create(['role_name' => 'customer']);
        $user = User::factory()->create(['role_id' => $role->role_id]);
        $order = Order::factory()->create(['customer_id' => $user->user_id]);
        $shipment = Shipment::factory()->create(['order_id' => $order->order_id, 'shipment_status' => 'pending']);

        $service = app(TrackingService::class);
        $result = $service->createLabel($shipment);

        $this->assertTrue($result);
        $this->assertEquals(Shipment::STATUS_LABEL_CREATED, $shipment->fresh()->shipment_status);
        Http::assertNothingSent();
    }
}
