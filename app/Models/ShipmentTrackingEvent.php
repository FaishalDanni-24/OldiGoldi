<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'shipment_id',
    'external_event_id',
    'event_code',
    'event_status',
    'description',
    'location',
    'happened_at',
    'raw_payload',
    'created_at'
])]
#[Table(key: 'tracking_event_id', timestamps: false)]
class ShipmentTrackingEvent extends Model
{
    /** @use HasFactory<\Database\Factories\ShipmentTrackingEventFactory> */
    use HasFactory;
}
