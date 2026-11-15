<?php

namespace Tests\Unit;

use App\Models\Orders\Order;
use App\Models\Orders\OrderStatusHistory;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Services\OrderStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_transition_updates_status_and_records_history(): void
    {
        $role = Role::factory()->create(['role_name' => 'customer']);
        $user = User::factory()->create(['role_id' => $role->role_id]);
        $order = Order::factory()->create(['customer_id' => $user->user_id, 'order_status' => 'pending_payment']);

        $service = app(OrderStatusService::class);
        $updated = $service->transition($order, 'paid', $user, 'paid via test');

        $this->assertEquals('paid', $updated->order_status);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->order_id,
            'old_status' => 'pending_payment',
            'new_status' => 'paid',
        ]);
    }
}
