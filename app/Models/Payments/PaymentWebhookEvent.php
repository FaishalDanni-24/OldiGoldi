<?php

namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'provider',
    'provider_event_id',
    'payment_id',
    'event_type',
    'payload',
    'processing_status',
    'received_at',
    'processed_at',
    'error_message'
])]
class PaymentWebhookEvent extends Model
{
    public const STATUS_RECEIVED = 'received';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_FAILED = 'failed';

    protected $table = 'payment_webhook_events';

    protected $primaryKey = 'webhook_id';

    public $timestamps = false;

    /** @use HasFactory<\Database\Factories\PaymentWebhookEventFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'payment_id' => 'integer',
            'payload' => 'array',
            'received_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    public function scopeReceived($query)
    {
        return $query->where('processing_status', self::STATUS_RECEIVED);
    }

    public function scopeFailed($query)
    {
        return $query->where('processing_status', self::STATUS_FAILED);
    }

    public function markProcessed(): void
    {
        $this->processing_status = self::STATUS_PROCESSED;
        $this->processed_at = now();
        $this->save();
    }

    public function markFailed(?string $errorMessage = null): void
    {
        $this->processing_status = self::STATUS_FAILED;
        $this->processed_at = now();
        if ($errorMessage !== null) {
            $this->error_message = $errorMessage;
        }
        $this->save();
    }
}
