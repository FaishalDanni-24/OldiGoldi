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
            $table->foreign('customer_id')->references('user_id')->on('users'); // user_id dengan role customer
            $table->foreign('admin_id')->references('user_id')->on('users'); // user_id dengan role customer
            $table->foreignId('order_id')->nullable(); // untuk chat berkaitan langsung dengan pesanan
            $table->foreignId('product_id')->nullable(); //untuk chat berkaitan dengan produk
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
