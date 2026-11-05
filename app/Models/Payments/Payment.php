<?php

namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
#[Table(key: 'payment_id')]
class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;
}
