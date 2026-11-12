<?php

namespace App\Models\Users;

use App\Models\Orders\Order;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
class Address extends Model
{
    protected $table = 'addresses';

    protected $primaryKey = 'address_id';

    /** @use HasFactory<\Database\Factories\Users\AddressFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'address_id', 'address_id');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
