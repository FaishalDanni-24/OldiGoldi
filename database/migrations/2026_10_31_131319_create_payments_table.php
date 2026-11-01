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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('order_id');
            $table->string('provider', 50);  
            $table->string('provider_order_id', 150)->nullable();  
            $table->string('provider_transaction_id', 150)->nullable();  
            $table->string('payment_method', 60)->nullable();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_status', array('pending', 'paid', 'failed', 'expired', 'cancelled', 'refunded'))->default('pending');
            $table->string('payment_url', 1000)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->unique(array('provider', 'provider_order_id'));
            $table->unique(array('provider', 'provider_transaction_id'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
