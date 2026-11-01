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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->foreignId('category_id');
            $table->foreignId('user_id')->nullable();
            $table->string('sku', 60)->unique();
            $table->string('product_name', 180);
            $table->string('brand', 100)->nullable();
            $table->string('model', 120)->nullable();
            $table->text('product_description')->nullable();
            $table->enum('condition_grade', array('like_new', 'good', 'fair', 'for_parts'));
            $table->text('condition_notes')->nullable();
            $table->decimal('price', 12, 2); // Cek apakah >= 0
            $table->unsignedInteger('stock')->default(1);
            $table->enum('product_status', array('draft', 'active', 'sold_out', 'inactive'))->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
