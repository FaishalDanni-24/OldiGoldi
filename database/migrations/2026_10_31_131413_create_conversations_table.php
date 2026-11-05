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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id('conversation_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('customer_id')->references('user_id')->on('users'); // user_id dengan role customer
            $table->foreign('admin_id')->references('user_id')->on('users'); // user_id dengan role customer
            $table->foreign('order_id')->references('order_id')->on('orders'); // untuk chat berkaitan langsung dengan pesanan
            $table->foreign('product_id')->references('product_id')->on('products'); //untuk chat berkaitan dengan produk
            $table->enum('conversation_status', array('open', 'closed'))->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
