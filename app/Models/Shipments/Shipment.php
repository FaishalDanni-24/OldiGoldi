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
    'delivered_at',
    'last_synced_at'
])]
class Shipment extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_LABEL_CREATED = 'label_created';
    public const STATUS_PICKED_UP = 'picked_up';
    public const STATUS_IN_TRANSIT = 'in_transit';
    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_FAILED = 'failed';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_UNKNOWN = 'unknown';

    protected $table = 'shipments';

    protected $primaryKey = 'shipment_id';

    /** @use HasFactory<\Database\Factories\Shipments\ShipmentFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'last_synced_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(ShipmentTrackingEvent::class, 'shipment_id', 'shipment_id');
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('shipment_status', [
            self::STATUS_LABEL_CREATED,
            self::STATUS_PICKED_UP,
            self::STATUS_IN_TRANSIT,
            self::STATUS_OUT_FOR_DELIVERY,
        ]);
    }

    public function scopeDelivered($query)
    {
        return $query->where('shipment_status', self::STATUS_DELIVERED);
    }

    public function markDelivered(): void
    {
        $this->shipment_status = self::STATUS_DELIVERED;
        $this->delivered_at = now();
        $this->save();
    }
}
