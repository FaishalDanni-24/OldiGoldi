<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'product_id',
    'image_url',
    'is_primary',
    'sort_order'
])]
class ProductImage extends Model
{
    protected $table = 'product_images';

    protected $primaryKey = 'image_id';

    public $timestamps = false;

    /** @use HasFactory<\Database\Factories\ProductImageFactory> */
    use HasFactory;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
