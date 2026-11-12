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
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';

    protected $table = 'payments';

    protected $primaryKey = 'payment_id';

    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'order_id' => 'integer',
            'amount' => 'decimal:2',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function webhookEvents(): HasMany
    {
        return $this->hasMany(PaymentWebhookEvent::class, 'payment_id', 'payment_id');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', self::STATUS_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', self::STATUS_PAID);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null
            && $this->expires_at->isPast()
            && $this->payment_status === self::STATUS_PENDING;
    }

    public function markPaid(?string $transactionId = null): void
    {
        $this->payment_status = self::STATUS_PAID;
        $this->paid_at = now();
        if ($transactionId !== null) {
            $this->provider_transaction_id = $transactionId;
        }
        $this->save();
    }
}
