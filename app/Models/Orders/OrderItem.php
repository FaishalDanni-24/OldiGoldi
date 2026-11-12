<?php

namespace App\Models\Orders;

use App\Models\Products\Product;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_id',
    'product_id',
    'product_name_snapshot',
    'sku_snapshot',
    'unit_price',
    'quantity',
    'line_total'
])]
class OrderItem extends Model
{
    protected $table = 'order_items';

    protected $primaryKey = 'order_item_id';

    public $timestamps = false;

    /** @use HasFactory<\Database\Factories\Orders\OrderItemFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (self $item) {
            if ($item->unit_price !== null && $item->quantity !== null) {
                $item->line_total = (float) $item->unit_price * (int) $item->quantity;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'product_id' => 'integer',
            'unit_price' => 'decimal:2',
            'quantity' => 'integer',
            'line_total' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
