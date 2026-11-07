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
    protected $table = 'payment_webhook_events';

    protected $primaryKey = 'webhook_id';

    public $timestamps = false;

    /** @use HasFactory<\Database\Factories\PaymentWebhookEventFactory> */
    use HasFactory;

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }
}
