<?php

namespace App\Models\Orders;

use App\Models\Chats\Conversation;
use App\Models\Payments\Payment;
use App\Models\Shipments\Shipment;
use App\Models\Users\Address;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

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
class Order extends Model
{
    protected $table = 'orders';

    protected $primaryKey = 'order_id';

    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory, SoftDeletes;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id', 'user_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id', 'address_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id', 'order_id');
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'order_id', 'order_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id', 'order_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'order_id', 'order_id');
    }
}
