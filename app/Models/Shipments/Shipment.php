<?php

namespace App\Models\Shipments;

use App\Models\Orders\Order;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'order_id',
    'tracking_provider',
    'provider_shipment_id',
    'courier_code',
    'courier_name',
    'tracking_number',
    'shipment_status',
    'shipped_at',
    'last_synced_at'
])]
class Shipment extends Model
{
    protected $table = 'shipments';

    protected $primaryKey = 'shipment_id';

    /** @use HasFactory<\Database\Factories\ShipmentFactory> */
    use HasFactory;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(ShipmentTrackingEvent::class, 'shipment_id', 'shipment_id');
    }
}
