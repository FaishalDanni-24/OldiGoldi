<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('weight')->nullable()->after('stock'); // grams
            $table->unsignedInteger('length')->nullable()->after('weight'); // cm
            $table->unsignedInteger('width')->nullable()->after('length'); // cm
            $table->unsignedInteger('height')->nullable()->after('width'); // cm
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['weight', 'length', 'width', 'height']);
        });
    }
};
