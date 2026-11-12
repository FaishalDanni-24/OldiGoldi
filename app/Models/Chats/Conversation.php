<?php

namespace App\Models\Chats;

use App\Models\Orders\Order;
use App\Models\Products\Product;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'customer_id',
    'admin_id',
    'order_id',
    'product_id',
    'conversation_status'
])]
class Conversation extends Model
{
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';

    protected $table = 'conversations';

    protected $primaryKey = 'conversation_id';

    /** @use HasFactory<\Database\Factories\ConversationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'customer_id' => 'integer',
            'admin_id' => 'integer',
            'order_id' => 'integer',
            'product_id' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id', 'user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id', 'user_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id', 'conversation_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('conversation_status', self::STATUS_OPEN);
    }

    public function scopeClosed($query)
    {
        return $query->where('conversation_status', self::STATUS_CLOSED);
    }
}
