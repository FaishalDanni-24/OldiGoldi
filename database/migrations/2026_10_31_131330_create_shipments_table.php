<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id('shipment_id');
            $table->foreignId('order_id');
            $table->string('tracking_provider', 60);
            $table->string('provider_shipment_id', 150)->nullable();
            $table->string('courier_code', 60)->nullable();
            $table->string('courier_name', 100)->nullable();
            $table->string('tracking_number', 150)->nullable();
            $table->enum('shipment_status', array('pending', 'label_created', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned', 'cancelled', 'unknown'))->default('pending');
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->unique(array('tracking_provider', 'tracking_number'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
