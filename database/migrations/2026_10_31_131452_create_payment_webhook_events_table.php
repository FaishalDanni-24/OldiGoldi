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
        Schema::create('payment_webhook_events', function (Blueprint $table) {
            $table->id('webhook_id');
            $table->string('provider', 50);
            $table->string('provider_event_id', 180);
            $table->foreignId('payment_id')->nullable();
            $table->string('event_type', 100);
            $table->json('payload');
            $table->enum('processing_status', array('received', 'processed', 'failed'))->default('received');
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->unique(array('provider', 'provider_event_id'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_events');
    }
};
