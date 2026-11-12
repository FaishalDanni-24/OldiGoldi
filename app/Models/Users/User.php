<?php

namespace App\Models\Users;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Carts\Cart;
use App\Models\Chats\ChatMessage;
use App\Models\Chats\Conversation;
use App\Models\Orders\Order;
use App\Models\Orders\OrderStatusHistory;
use App\Models\Products\Product;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'role_id',
    'name',
    'email',
    'password',
    'phone_number',
    'account_status'
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PENDING_VERIFICATION = 'pending_verification';
    public const STATUS_BLOCKED = 'blocked';

    protected $table = 'users';

    protected $primaryKey = 'user_id';

    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role_id' => 'integer',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'user_id', 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id', 'user_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'user_id', 'user_id');
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class, 'user_id', 'user_id');
    }

    public function conversationsAsCustomer(): HasMany
    {
        return $this->hasMany(Conversation::class, 'customer_id', 'user_id');
    }

    public function conversationsAsAdmin(): HasMany
    {
        return $this->hasMany(Conversation::class, 'admin_id', 'user_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_id', 'user_id');
    }

    public function orderStatusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'user_id', 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('account_status', self::STATUS_ACTIVE);
    }

    public function scopeBlocked($query)
    {
        return $query->where('account_status', self::STATUS_BLOCKED);
    }
}
