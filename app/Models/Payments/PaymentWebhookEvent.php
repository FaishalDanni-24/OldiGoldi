<?php

namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
#[Table(key: 'webhook_id', timestamps: false)]
class PaymentWebhookEvent extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentWebhookEventFactory> */
    use HasFactory;
}
