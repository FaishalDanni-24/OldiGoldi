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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->foreignId('user_id');
            $table->string('address_label', 50)->nullable();
            $table->string('recipient_name', 120);
            $table->string('recipient_phone', 30);
            $table->string('address_line', 500);
            $table->string('district', 100)->nullable();
            $table->string('city', 100);
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 15)->nullable();
            $table->boolean('is_default')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
