<?php

namespace App\Models\Products;

use App\Models\Carts\CartItem;
use App\Models\Chats\Conversation;
use App\Models\Orders\OrderItem;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'category_id',
    'user_id',
    'sku',
    'product_name',
    'brand',
    'model',
    'product_description',
    'condition_grade',
    'condition_notes',
    'price',
    'stock',
    'product_status'
])]
class Product extends Model
{
    public const CONDITION_LIKE_NEW = 'like_new';
    public const CONDITION_GOOD = 'good';
    public const CONDITION_FAIR = 'fair';
    public const CONDITION_FOR_PARTS = 'for_parts';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SOLD_OUT = 'sold_out';
    public const STATUS_INACTIVE = 'inactive';

    protected $table = 'products';

    protected $primaryKey = 'product_id';

    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'category_id' => 'integer',
            'user_id' => 'integer',
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id')
            ->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'product_id')
            ->where('is_primary', true);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'product_id', 'product_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'product_id', 'product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('product_status', self::STATUS_ACTIVE);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeByCondition($query, string $grade)
    {
        return $query->where('condition_grade', $grade);
    }
}
