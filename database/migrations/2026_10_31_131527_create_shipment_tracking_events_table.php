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
        Schema::create('shipment_tracking_events', function (Blueprint $table) {
            $table->id('tracking_event_id');
            $table->foreignId('shipment_id');
            $table->string('external_event_id', 150)->nullable();
            $table->string('event_code', 100)->nullable();
            $table->string('event_status', 50);
            $table->text('description')->nullable();
            $table->string('location', 255)->nullable();
            $table->dateTime('happened_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(array('shipment_id', 'external_event_id'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_tracking_events');
    }
};
