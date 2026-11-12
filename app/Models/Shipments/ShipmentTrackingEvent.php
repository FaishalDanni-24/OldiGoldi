<?php

namespace App\Models\Shipments;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'shipment_id',
    'external_event_id',
    'event_code',
    'event_status',
    'description',
    'location',
    'happened_at',
    'raw_payload'
])]
class ShipmentTrackingEvent extends Model
{
    protected $table = 'shipment_tracking_events';

    protected $primaryKey = 'tracking_event_id';

    public $timestamps = false;

    /** @use HasFactory<\Database\Factories\ShipmentTrackingEventFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'shipment_id' => 'integer',
            'raw_payload' => 'array',
            'happened_at' => 'datetime',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class, 'shipment_id', 'shipment_id');
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc('happened_at');
    }
}
