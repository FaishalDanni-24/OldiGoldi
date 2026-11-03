<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
#[Table(key: 'shipment_id')]
class Shipment extends Model
{
    /** @use HasFactory<\Database\Factories\ShipmentFactory> */
    use HasFactory;
}
