<?php

namespace Tests\Feature;

use App\Models\Orders\Order;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentTrackingEvent;
use App\Models\Users\Role;
use App\Models\Users\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingWebhookTest extends TestCase
{
    use RefreshDatabase;

    private function customer(): User
    {
        $role = Role::factory()->create(['role_name' => 'customer']);
        return User::factory()->create(['role_id' => $role->role_id, 'account_status' => 'active']);
    }

    public function test_webhook_rejects_unknown_shipment(): void
    {
        $this->postJson('/api/v1/tracking/webhook/sandbox', [
            'tracking_number' => 'NOPE',
            'status' => 'in_transit',
            'event_id' => 'EVT-X',
        ])->assertStatus(404);
    }

    public function test_webhook_creates_tracking_event(): void
    {
        $shipment = Shipment::factory()->create([
            'tracking_number' => 'TRK-TEST-1',
            'shipment_status' => 'label_created',
        ]);

        $response = $this->postJson('/api/v1/tracking/webhook/sandbox', [
            'tracking_number' => 'TRK-TEST-1',
            'status' => 'in_transit',
            'event_id' => 'EVT-T1',
            'description' => 'Paket transit',
            'location' => 'Jakarta',
            'happened_at' => now()->toDateTimeString(),
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('shipment_tracking_events', [
            'shipment_id' => $shipment->shipment_id,
            'external_event_id' => 'EVT-T1',
            'event_status' => 'in_transit',
        ]);
        $this->assertEquals('in_transit', $shipment->fresh()->shipment_status);
    }

    public function test_webhook_is_idempotent(): void
    {
        $shipment = Shipment::factory()->create([
            'tracking_number' => 'TRK-TEST-2',
            'shipment_status' => 'label_created',
        ]);

        $this->postJson('/api/v1/tracking/webhook/sandbox', [
            'tracking_number' => 'TRK-TEST-2',
            'status' => 'in_transit',
            'event_id' => 'EVT-DUP',
        ])->assertStatus(200);

        $second = $this->postJson('/api/v1/tracking/webhook/sandbox', [
            'tracking_number' => 'TRK-TEST-2',
            'status' => 'in_transit',
            'event_id' => 'EVT-DUP',
        ]);

        $second->assertStatus(200)
            ->assertJsonPath('duplicate', true);
    }

    public function test_customer_can_list_tracking_events(): void
    {
        $user = $this->customer();
        $order = \App\Models\Orders\Order::factory()->create(['customer_id' => $user->user_id]);
        $shipment = Shipment::factory()->create(['order_id' => $order->order_id]);
        ShipmentTrackingEvent::factory()->create(['shipment_id' => $shipment->shipment_id]);

        $this->actingAs($user, 'sanctum')->getJson("/api/v1/shipments/{$shipment->shipment_id}/tracking-events")
            ->assertStatus(200);
    }
}
