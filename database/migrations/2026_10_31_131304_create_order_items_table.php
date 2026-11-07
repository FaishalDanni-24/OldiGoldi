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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('order_item_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('order_id')->references('order_id')->on('orders');
            $table->foreign('product_id')->references('product_id')->on('products');
            $table->string('product_name_snapshot', 180);
            $table->string('sku_snapshot', 60)->nullable();
            $table->decimal('unit_price', 12, 2); # Cek apakah >= 0
            $table->unsignedInteger('quantity'); # Cek apakah > 0
            $table->decimal('line_total', 12, 2)->storedAs('unit_price * quantity'); # Hitung otomatis dari unit_price * quantity
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
