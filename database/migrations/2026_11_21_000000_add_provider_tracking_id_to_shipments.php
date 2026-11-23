<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('provider_tracking_id', 150)->nullable()->after('provider_shipment_id');
            $table->index('provider_tracking_id');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropIndex(['provider_tracking_id']);
            $table->dropColumn('provider_tracking_id');
        });
    }
};
