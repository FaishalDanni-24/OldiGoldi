<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'order_number',
    'customer_id',
    'address_id',
    'order_status',
    'subtotal',
    'shipping_cost',
    'discount_amount',
    'total_amount',
    'recipient_name',
    'recipient_phone',
    'shipping_address_line',
    'shipping_district',
    'shipping_city',
    'shipping_province',
    'shopping_postal_code',
    'customer_note'
])]
#[Table(key: 'order_id')]
class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
}
