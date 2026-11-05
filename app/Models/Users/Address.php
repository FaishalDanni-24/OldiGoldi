<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'address_label',
    'recipient_name',
    'recipient_phone',
    'address_line',
    'district',
    'city',
    'province',
    'postal_code',
    'is_default'
])]
#[Table(key: 'address_id')]
class Address extends Model
{
    /** @use HasFactory<\Database\Factories\AddressFactory> */
    use HasFactory;
}
