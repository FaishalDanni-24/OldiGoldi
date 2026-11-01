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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->unsignedInteger('order_number')->unique();
            $table->foreignId('customer_id');
            $table->foreignId('address_id')->nullable();
            $table->enum('order_status', array('pending_payment', 'paid', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'refunded'))->default('pending_payment');
            $table->decimal('subtotal', 12, 2); // Cek apakah >= 0
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2); // Cek apakah >= 0
            $table->string('recipient_name', 120);
            $table->string('recipient_phone', 30);
            $table->string('shipping_address_line', 500);
            $table->string('shipping_district', 100)->nullable();
            $table->string('shipping_city', 100);
            $table->string('shipping_province', 100)->nullable();
            $table->string('shipping_postal_code', 15)->nullable();
            $table->text('customer_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
