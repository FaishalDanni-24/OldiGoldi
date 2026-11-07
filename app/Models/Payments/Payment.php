<?php

namespace App\Models\Payments;

use App\Models\Orders\Order;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'order_id',
    'provider',
    'provider_order_id',
    'provider_transaction_id',
    'payment_method',
    'amount',
    'payment_status',
    'payment_url',
    'expires_at',
    'paid_at'
])]
class Payment extends Model
{
    protected $table = 'payments';

    protected $primaryKey = 'payment_id';

    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function webhookEvents(): HasMany
    {
        return $this->hasMany(PaymentWebhookEvent::class, 'payment_id', 'payment_id');
    }
}
