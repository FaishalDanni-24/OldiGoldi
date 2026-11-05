<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'order_id',
    'product_id',
    'product_name_snapshot',
    'sku_snapshot',
    'unit_price',
    'quantity',
    'line_total'
])]
#[Table(key: 'order_item_id', timestamps:false)]
class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;
}
